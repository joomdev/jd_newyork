<?php

class N2SliderGeneratorEcwidConfiguration {

    private $data;

    /** @var N2SliderGeneratorPluginAbstract */
    protected $group;

    /**
     * @param N2SliderGeneratorPluginAbstract $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'storeID' => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('ecwid'));
    }

    public function wellConfigured() {
        return $this->isValidStoreID();
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('ecwid', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $settings = new N2Tab($form, 'ecwid-generator', 'Ecwid api');
        new N2ElementText($settings, 'storeID', 'Store ID', '', array(
            'style' => 'width:150px;'
        ));
        new N2ElementToken($settings);

        $this->checkStoreID();

        echo $form->render('generator');
    }

    public function getStoreID() {
        return $this->data->get('storeID');
    }

    private function isValidStoreID() {
        $storeID = $this->data->get('storeID');
        if (!empty($storeID)) {
            $categories   = "http://app.ecwid.com/api/v1/" . $storeID . "/categories";
            $categoryList = N2TransferData::get($categories);
            if ($categoryList === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkStoreID() {
        if (!$this->isValidStoreID()) {
            N2Message::error(n2_('The store ID is not valid!'));

            return false;
        }

        return true;
    }

}
