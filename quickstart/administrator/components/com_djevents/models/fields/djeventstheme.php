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

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

class JFormFieldDJEventsTheme extends JFormField {
	
	protected $type = 'DJEventsTheme';
	
	protected function getInput()
	{
		$attr = '';

		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		
		$themes = JFolder::listFolderTree(JPATH_SITE.DS.'components'.DS.'com_djevents'.DS.'themes','',1);
		$default = $this->element['default'];
		$options = array();
		if ($default == '') {
			$options[] = JHTML::_('select.option', '', JText::_('JGLOBAL_USE_GLOBAL'));
		}
		foreach ($themes as $theme) {
			$options[] = JHTML::_('select.option', $theme['name'], $theme['name']);
		}			
		$out = JHTML::_('select.genericlist', $options, $this->name, null, 'value', 'text', $this->value);
		
		return ($out);
		
	}
}
?>