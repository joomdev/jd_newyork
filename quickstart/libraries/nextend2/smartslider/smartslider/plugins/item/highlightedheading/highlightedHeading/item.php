<?php

N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemHighlightedHeading extends N2SSItemAbstract {

    protected $type = 'highlightedHeading';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $heading = array();

        $beforeText = $owner->fill($this->data->get('before-text', ''));
        if (!empty($beforeText)) {
            $heading[] = N2Html::tag('div', array(
                'class' => 'n2-ss-highlighted-heading-before'
            ), $beforeText);
        }

        $highlightedText = $owner->fill($this->data->get('highlighted-text', ''));
        if (!empty($highlightedText)) {

            $svg           = '';
            $highlightType = $this->data->get('type', '');
            if (!empty($highlightType)) {
                $svgPath = dirname(__FILE__) . '/svg/' . $highlightType . '.svg';
                if (N2Filesystem::fileexists($svgPath)) {
                    $svg = N2Filesystem::readFile($svgPath);

                    $highlightColor = $this->data->get('color', '');
                    $css            = array(
                        'stroke:#' . substr($highlightColor, 0, 6) . ';',
                        'stroke-opacity:' . N2Color::hex2opacity($highlightColor) . ';',
                        'stroke-width:' . $this->data->get('width', 10) . 'px;'
                    );
                    $owner->addCSS('div #' . $owner->getElementID() . ' #' . $this->id . ' svg path{' . implode('', $css) . '}');
                }
            }

            $attributes = array(
                'class' => 'n2-highlighted n2-ss-highlighted-heading-highlighted n2-ow'
            );

            if ($this->data->get('animate', 1)) {
                $attributes['data-animate'] = 1;
            }

            $delay = $this->data->get('delay', 0);
            if ($delay > 0) {
                $attributes['data-delay'] = $delay;
            }

            $duration = $this->data->get('duration', 1500);
            if ($duration != 1500) {
                $attributes['data-duration'] = $duration;
            }

            if ($this->data->get('loop', 0)) {
                $attributes['data-loop'] = 1;
            }

            $loopDelay = $this->data->get('loop-delay', 0);
            if ($loopDelay >= 0) {
                $attributes['data-loop-delay'] = $loopDelay;
            }

            if ($this->data->get('front', 0)) {
                $attributes['data-front'] = 1;
            }

            $highlightedInner = N2Html::tag('div', array(
                    'class' => 'n2-ss-highlighted-heading-highlighted-text'
                ), $highlightedText) . $svg;


            list($link) = (array)N2Parse::parse($this->data->get('link', '#|*|'));
            if (!empty($link) && $link != '#') {
                $heading[] = $this->getLink($highlightedInner, $attributes);
            } else {
                $heading[] = N2Html::tag('div', $attributes, $highlightedInner);
            }
        }

        $afterText = $owner->fill($this->data->get('after-text', ''));
        if (!empty($afterText)) {
            $heading[] = N2Html::tag('div', array(
                'class' => 'n2-ss-highlighted-heading-after'
            ), $afterText);
        }


        $font = $owner->addFont($this->data->get('font'), 'highlight', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');


        $style = $owner->addStyle($this->data->get('style'), 'highlight');

        return $this->heading($this->data->get('priority', 'div'), array(
            "id"    => $this->id,
            "class" => 'n2-ss-highlighted-heading-wrapper ' . $font . ' ' . $style . ' n2-ow'
        ), implode('', $heading));
    }

    private function heading($type, $attributes, $content) {
        if ($type > 0) {
            return N2Html::tag("h{$type}", $attributes, $content);
        }

        return N2Html::tag("div", $attributes, $content);
    }

    /**
     * @param N2SmartSliderComponentOwnerAbstract $owner
     */
    public function loadResources($owner) {
        $owner->addLess(dirname(__FILE__) . "/highlightedHeading.n2less", array(
            "sliderid" => $owner->getElementID()
        ));

        if (!$owner->isScriptAdded('highlighted-heading')) {
            if ($this->isEditor && $owner->underEdit) {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-currently-edited-slide .n2-ss-highlighted-heading-highlighted[data-animate="1"]\').each($.proxy(function(i, el){new N2Classes.HighlightedHeadingItemAdmin(el, this)}, this));', 'highlighted-heading');
            } else {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-highlighted-heading-highlighted[data-animate="1"]\').each($.proxy(function(i, el){new N2Classes.FrontendItemHighlightedHeading(el, this)}, this));', 'highlighted-heading');
            }
        }
    }
}