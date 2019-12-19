<?php
/**
 * @package   JD Progress Bar
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license   GNU/GPLv2 and later
 */
// no direct access
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//jimport('joomla.form.formfield');

class JFormFieldJDSwitch extends JFormField {

   protected $type = 'jdswitch';

   public function getInput() {
   
      $layout = new JLayoutFile('jdswitch', JPATH_ROOT . '/modules/mod_jdprogressbar/fields/layouts');
      return $layout->render(['field' => $this,'element'=> $this->element]);

   }
   
   
}
