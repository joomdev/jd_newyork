<?php
/**
 * @version   $Id: init.php 4590 2012-10-27 02:19:03Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_PLATFORM') or die;


/**
 *
 */
class JFormFieldInit extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Init';

	/**
	 *
	 */
	public function __construct()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root() . 'plugins/editors/rokpad/assets/admin/css/fields.css');
		$doc->addScript(JURI::root() . 'plugins/editors/rokpad/assets/admin/js/RokPad.js');
		$doc->addScript(JURI::root() . 'plugins/editors/rokpad/assets/admin/js/Dropdowns.js');
		$doc->addScriptDeclaration("window.addEvent('domready', function() { new Dropdowns(); });");

	}

	/**
	 * @return mixed
	 */
	protected function getLabel()
	{
		return;
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		return;
	}

}
