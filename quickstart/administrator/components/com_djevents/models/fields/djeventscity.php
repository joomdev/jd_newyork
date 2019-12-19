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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');


class JFormFieldDJEventsCity extends JFormField {

	protected $type = 'DJEventsCity';

	protected function getInput()
	{
		$readonly = false;
		if (!empty($this->element['readonly']) && $this->element['readonly'] == 'true') {
			$readonly = true;
			$this->element['class'] = $this->element['class'].' readonly';
		}
		
		$attr = '';
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->required ? ' required="true" aria-required="true"' : '';
		$attr .= $this->multiple ? ' multiple="true"' : '';
				
		$showFirst = (empty($this->element['skip_default'])) ? true : false;
		$showNew  = (empty($this->element['skip_new'])) ? true : false;
		
		$user = JFactory::getUser();
		$db	= JFactory::getDBO();

		$query = "SELECT * FROM #__djev_cities ORDER BY name ASC";

		$db->setQuery($query);
		$cities = $db->loadObjectList();

		$options = array();
		$text_value = '';
		$id_value = 0;
		if ($showFirst){
			$options[] = JHTML::_('select.option', '',JText::_('COM_DJEVENTS_SELECT_OPTION_CITY'));
		}
		foreach($cities as $city){
			if ($city->id == $this->value) {
				$text_value = $city->name;
				$id_value = $city->id;
			}
			$options[] = JHTML::_('select.option', $city->id, $city->name);

		}
		$out = '';
		if ($readonly && $this->value > 0) {
			$out = '<input type="text" '.trim($attr).' value="'.$text_value.'" id="'.$this->id.'" readonly="readonly" />';
			$out.= '<input type="hidden" name="'.$this->name.'" value="'.$id_value.'"/>';
		} else {
			$out = JHTML::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
			if($showNew) $out.= '<input type="text" class="input-medium form-control" name="'.str_replace(']', '_new]', $this->name).'" value="" id="'.$this->id.'_new" placeholder="'.JText::_('COM_DJEVENTS_NEW_CITY').'" />';
		}
		
		return '<div class="form-inline input-group">'.$out.'</div>';
	}
}