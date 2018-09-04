<?php
/**
 * @version   $Id: break.php 9506 2013-04-19 20:55:29Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_PLATFORM') or die;


/**
 *
 */
class JFormFieldBreak extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Break';

	/**
	 * @return string
	 */
	protected function getLabel()
	{
		$doc     = JFactory::getDocument();
		$version = new JVersion();
            $doc->addStyleDeclaration("body label.rok-break, body div.rok-break {border-bottom:1px solid #eee;font-size:16px;color:#222;margin-top:15px;margin-bottom: 10px;padding:2px 0;width:100%;min-width:inherit;max-width:inherit;} body label.rok-break.top {margin-top: 0;}");

		if (isset($this->element['label']) && !empty($this->element['label'])) {
			$label   = JText::_((string)$this->element['label']);
			$css     = (string)$this->element['class'];
			$version = new JVersion();
			if (version_compare($version->getShortVersion(), '3.0', '>=')) {
				return '<div class="rok-break ' . $css . '">' . $label . '</div>';
			} else {
				return '<label class="rok-break ' . $css . '">' . $label . '</label>';
			}
		} else {
			return;
		}
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		return;
	}

}
