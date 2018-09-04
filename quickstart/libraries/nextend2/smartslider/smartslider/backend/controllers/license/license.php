<?php
class N2SmartsliderBackendLicenseController extends N2SmartSliderController {

    public function actionDeAuthorize() {
        $status = N2SmartsliderLicenseModel::getInstance()
                                           ->deAuthorize();

        $hasError = N2SS3::hasApiError($status);
        if (is_array($hasError)) {
            $this->response->redirect($hasError);
        }

        $this->redirectToSliders();
    }
}

