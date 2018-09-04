<?php
N2Loader::import('libraries.splittextanimation.storage', 'smartslider');

class N2SplitTextAnimationManager
{

    public static function init() {
        static $inited = false;
        if (!$inited) {

            N2Pluggable::addAction('afterApplicationContent', 'N2SplitTextAnimationManager::load');
            $inited = true;
        }
    }

    public static function load() {
        N2Base::getApplication('system')->getApplicationType('backend');
        N2Base::getApplication('smartslider')->getApplicationType('backend')->run(array(
            'useRequest' => false,
            'controller' => 'splittextanimation',
            'action'     => 'index'
        ));
    }
}

N2SplitTextAnimationManager::init();