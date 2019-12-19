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

jimport( 'joomla.application.component.view');
jimport( 'joomla.application.categories');
jimport('joomla.html.pane');

class DJEventsViewCpanel extends JViewLegacy
{
	protected $_name = 'cpanel';
	
	function display($tpl = null)
	{
		//$model = $this->getModel();
		//$model->performChecks();
		
		JToolBarHelper::title( JText::_('COM_DJEVENTS'));
		JToolBarHelper::preferences('com_djevents', '450', '900');

		if (class_exists('JHtmlSidebar')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		$this->classes = DJEventsHelper::getBSClasses();
		
		parent::display($tpl);
	}
}
