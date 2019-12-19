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

class DJEventsController extends JControllerLegacy
{
	protected $default_view = 'cpanel';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/djevents.php';
		DJEventsHelper::addSubmenu(JFactory::getApplication()->input->getCmd('view', 'cpanel'));
		
		//JHtml::_('behavior.framework');
		//JFactory::getDocument()->addScript(JUri::base().'components/com_djevents/assets/js/script.js');
		JFactory::getDocument()->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		
		parent::display($cachable, $urlparams);
	}
	
	public function getvideo() {
	
		$app = JFactory::getApplication();
	
		// decode passed video url
		$link = urldecode($app->input->get('video','','raw'));
		
		// get video object
		$video = DJEventsVideoHelper::getVideo($link);
	
		// clear the buffer from any output
		@ob_clean();
	
		// return the JSON representation of $video object
		echo json_encode($video);
	
		// exit application
		$app->close();
	}
	
	public function upload() {
	
		// todo: secure upload from injections
		$user = JFactory::getUser();
		if (!$user->authorise('event.create', 'com_djevents') && !$user->authorise('core.create', 'com_djevents')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
	
		DJEventsUploadHelper::upload();
	
		return true;
	}
	
	public function import() {
		
		jimport('joomla.filesystem.file');
		jimport('joomla.application.component.helper');
		
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if (!$user->authorise('core.admin', 'com_djevents')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
		
		$source = $app->input->getVar('source');
		
		ini_set('display_errors', 1);
		error_reporting(E_ALL && E_STRICT);
	
		// Remove php's time limit
		if(function_exists('ini_get') && function_exists('set_time_limit')) {
			if(!ini_get('safe_mode') ) {
				@set_time_limit(0);
			}
		}
		// Increase php's memory limit
		if(function_exists('ini_set')) {
			@ini_set('memory_limit', '512M');
		}
		
		switch($source) {
			
			case 'jevents':
				
				if(!JFile::exists(JPATH_ROOT.'/components/com_jevents/jevents.php')) {
					
					$this->setRedirect(JRoute::_('index.php?option=com_djevents'), JText::_('COM_DJEVENTS_IMPORT_JEVENTS_NO_COMPONENT_MSG'), 'error');
					return false;
				}
				
				$step = $app->input->getInt('step');
				
				if($step) {
					if(!DJEventsImportHelper::importJEventsStep($step)) return true;
				} else if(DJEventsImportHelper::truncateAll()) {
					if(!DJEventsImportHelper::importJEvents()) return true;
				}
				
				break;
			default:
				
				$this->setRedirect(JRoute::_('index.php?option=com_djevents'), 'Unknown import source', 'error');
				return false;
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_djevents'));
		return true;
	}
}

