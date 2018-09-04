<?php
class N2SSSlideComponentGroup extends N2SSSlideComponent {

    protected $type = 'group';

    protected $attributes = array(
        'class' => 'n2-ss-layer-group'
    );

    public function __construct($index, $owner, $group, $data, $placementType) {
        parent::__construct($index, $owner, $group, $data, 'group');
        $this->container = new N2SSLayersContainer($owner, $this, $data['layers'], 'absolute');
        $this->data->un_set('layers');

        $this->attributes['style'] = '';

        $this->placement->attributes($this->attributes);

    }

    public function render($isAdmin) {
        if ($this->isRenderAllowed()) {
            if ($isAdmin) {
                $this->admin();
            }
            $this->prepareHTML();
            $html = $this->renderPlugins(parent::renderContainer($isAdmin));

            return N2Html::tag('div', $this->attributes, $html);
        }

        return '';
    }

    protected function admin() {
        $this->createProperty('opened', 1);

        parent::admin();
    }

    /**
     * @param N2SmartSliderExport $export
     * @param array               $layer
     */
    public static function prepareExport($export, $layer) {

        N2SmartSliderExport::prepareExportLayer($export, $layer['layers']);
    }

    public static function prepareImport($import, &$layer) {
        N2SmartSliderImport::prepareImportLayer($import, $layer['layers']);
    }

    public static function prepareSample(&$layer) {
        N2SmartsliderSlidesModel::prepareSample($layer['layers']);
    }

    /**
     * @param N2SmartSliderSlide $slide
     * @param array              $layer
     */
    public static function getFilled($slide, &$layer) {
        N2SSSlideComponent::getFilled($slide, $layer);

        $slide->fillLayers($layer['layers']);
    }
}
