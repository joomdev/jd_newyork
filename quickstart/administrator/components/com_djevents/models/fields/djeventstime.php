<?php
/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

defined('_JEXEC') or die();
defined('JPATH_BASE') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldDJEventsTime extends JFormField {
	
	protected $type = 'DJEventsTime';
	
	protected function getInput()
	{	
		$doc = JFactory::getDocument();
		
		// Initialize some field attributes.
		$attr = ' maxlength="5"';
		$attr.= ' class="input-mini ' . ($this->element['class'] ? (string) $this->element['class'] : '') . '"';
		
		// Initialize JavaScript field attributes.
		
		//$js = "";
		//$doc->addScriptDeclaration($js);
		
		$attr.= ' onchange="if(!/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(this.value)) this.value=\'\';"';
		$attr.= $this->value == '' ? ' disabled="disabled"' : '';
		$attr.= ' placeholder="HH:MM"';
		
		$html = '<div class="form-inline">';
		$html.= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $attr .'/>';
		
		$attr = ' onclick="document.getElementById(\'' . $this->id . '\').disabled = this.checked"';
		$attr.= $this->value == '' ? ' checked="checked"' : '';
		
		$html.= ' <label for="' . $this->id . '_off"><input type="checkbox" id="' . $this->id . '_off" value="1" ' . $attr .'/> '
				.JText::_('COM_DJEVENTS_TIME_OFF').'</label>';
		$html.= '</div>';
		
		return $html;		
	}
}
?>