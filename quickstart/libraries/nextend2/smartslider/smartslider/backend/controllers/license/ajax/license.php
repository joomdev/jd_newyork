<?php
class N2SmartsliderBackendLicenseControllerAjax extends N2SmartSliderControllerAjax {

    public function actionAdd() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');


        $licenseKey = N2Request::getVar('licenseKey');
        if (empty($licenseKey)) {
            N2Message::error(n2_('License key cannot be empty!'));
            $this->response->error();
        }


        $status = N2SmartsliderLicenseModel::getInstance()
                                           ->checkKey($licenseKey, 'licenseadd');

        $hasError = N2SS3::hasApiError($status);
        if (is_array($hasError)) {
            $this->response->redirect($hasError);
        } else if ($hasError !== false) {
            $this->response->error();
        }

        N2SmartsliderLicenseModel::getInstance()
                                 ->setKey($licenseKey);
        $this->response->respond(array(
            'valid' => true
        ));
    
    }

    public function actionCheck() {
        $this->validateToken();
        $verbose = N2Request::getInt('verbose', 1);

        $status = N2SmartsliderLicenseModel::getInstance()
                                           ->isActive(N2Request::getInt('cacheAccepted', 1));

        if ($verbose) {
            $hasError = N2SS3::hasApiError($status);
            if (is_array($hasError)) {
                $this->response->redirect($hasError);
            } else if ($hasError !== false) {
                $this->response->error();
            }
            N2Message::notice(n2_('License key is active!'));
            $this->response->respond();
        }

        if ($status == 'OK') {
            $this->response->respond();
        }
        $this->response->error();
    
    }
}
