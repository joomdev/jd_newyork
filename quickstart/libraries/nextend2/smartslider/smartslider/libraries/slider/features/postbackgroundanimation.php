<?php
class N2SmartSliderFeaturePostBackgroundAnimation
{

    private $slider;

    private $hasSlideData = false;

    private $slideData = array();

    public function __construct($slider) {

        $this->slider = $slider;
    }

    /**
     * @param $slide N2SmartSliderSlide
     */
    public function makeSlide($slide) {
        $animations = $this->parseKenBurns($slide->parameters->get('kenburns-animation', '50|*|50|*|'));
        if ($animations) {
            $this->slideData[$slide->index] = array(
                'data'     => $animations,
                'speed'    => $slide->parameters->get('kenburns-animation-speed', 'default'),
                'strength' => $slide->parameters->get('kenburns-animation-strength', 'default')
            );
            $this->hasSlideData             = true;
        }
    }

    public function makeJavaScriptProperties(&$properties) {
        $properties['postBackgroundAnimations'] = array(
            'data'     => $this->parseKenBurns($this->slider->params->get('kenburns-animation', '50|*|50|*|')),
            'speed'    => $this->slider->params->get('kenburns-animation-speed', 'default'),
            'strength' => $this->slider->params->get('kenburns-animation-strength', 'default')
        );

        if ($this->hasSlideData) {
            $properties['postBackgroundAnimations']['slides'] = $this->slideData;
        } else if (!$properties['postBackgroundAnimations']['data']) {
            $properties['postBackgroundAnimations'] = 0;
        }
    }

    private function parseKenBurns($kenBurnsRaw) {

        $kenBurns   = N2Parse::parse($kenBurnsRaw);
        $animations = array();
        if(is_array($kenBurns)){
            if (count($kenBurns) >= 2) {
                $animations = array_unique(array_map('intval', (array)$kenBurns[2]));
            }
        }

        $jsProps = array();

        if (count($animations)) {
            N2Loader::import('libraries.postbackgroundanimation.storage', 'smartslider');

            foreach ($animations AS $animationId) {
                $animation = N2StorageSectionAdmin::getById($animationId, 'postbackgroundanimation');
                if (isset($animation)) {
                    $jsProps[] = $animation['value']['data'];
                }
            }
            if (count($jsProps)) {
                return array(
                    'transformOrigin' => $kenBurns[0] . '% ' . $kenBurns[1] . '%',
                    'animations'      => $jsProps
                );
            }
        }

        return 0;
    }
}
