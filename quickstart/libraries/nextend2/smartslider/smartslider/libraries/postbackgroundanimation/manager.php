<?php
N2Loader::import('libraries.postbackgroundanimation.storage', 'smartslider');

class N2PostBackgroundAnimationManager
{

    public static function init() {
        static $inited = false;
        if (!$inited) {

            N2Pluggable::addAction('afterApplicationContent', 'N2PostBackgroundAnimationManager::load');
            $inited = true;
        }
    }

    public static function load() {
        N2Base::getApplication('system')->getApplicationType('backend');
        N2Base::getApplication('smartslider')->getApplicationType('backend')->run(array(
            'useRequest' => false,
            'controller' => 'postbackgroundanimation',
            'action'     => 'index'
        ));
    }
}

N2PostBackgroundAnimationManager::init();
