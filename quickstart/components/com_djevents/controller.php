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

jimport('joomla.application.component.controller');

class DJEventsController extends JControllerLegacy
{

	function __construct($config = array())
	{
		parent::__construct($config);
	}

	function display($cachable = true, $urlparams = null)
	{
		$app = JFactory::getApplication();
		
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = $app->input->get('view', 'eventslist');
		$viewLayout = $app->input->get('layout', 'default', 'string');
		
		$view = $this->getView($viewName, $viewType, 'DJEventsView', array('base_path' => $this->basePath, 'layout' => $viewLayout));

		$noncachable = array('eventform','myevents');

		if (in_array($viewName, $noncachable)) {
			$cachable = false;
		}
		
		$urlparams = array(
				'format' => 'WORD',
    			'option' => 'WORD',
    			'view'   => 'WORD',
    			'layout' => 'WORD',
    			'tpl'    => 'CMD',
    			'id'     => 'INT',
				'day' 	=> 'STRING',
				'search' => 'STRING',
				'from' => 'STRING',
				'to' => 'STRING',
				'cid' => 'STRING',
				'city' => 'STRING',
				'tag' => 'STRING',
				'return' => 'BASE64',
				'limitstart' => 'UINT',
				'limit' => 'UINT'
		);
		
		return parent::display($cachable, $urlparams);
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
	
	function search() {
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$post = $app->input->getArray($_POST);
		$params = array();
		foreach($post as $key => $value) {
			if ($key != 'task' && $key != 'option' && $key != 'view' && $key != 'Itemid') {
				if ($key == 'search') {
					$params[] = $key.'='.urlencode($value);
				}
				else if (is_array($value)) {
					foreach ($value as $k => $v) {
						if (is_numeric($k)) {
							$params[] = $key.'[]='.$v;
						} else {
							$params[] = $key.'['.$k.']='.$v;
						}
					}
				}
				else {
					$params[] = $key.'='.$value;
				}
			}
		}
		
		$uri = DJEventsHelperRoute::getEventsListRoute($post['cid'], $post['city']);
		if (strpos($uri,'?') === false ) {
			$get = (count($params)) ? '?'.implode('&',$params) : '';
		} else {
			$get = (count($params)) ? '&'.implode('&',$params) : '';
		}
		$app->redirect( JRoute::_($uri.$get, false).'#sr' );
	}
	
	public function getDays() {
		
		$app = JFactory::getApplication();
		
		$lastDate = $app->input->getString('last_date', false);
		
		if (!$lastDate) {
			$app->close();
		}
		
		$d = DateTime::createFromFormat('Y-m-d', $lastDate);
		if (!($d && $d->format('Y-m-d') === $lastDate)) {
			$app->close();
		}
		
		$days = (int) $app->input->get('days', 3);
		if(!$days) $days = 3;
		
		if ($app->input->getInt('direction', 1) > 0) {
			$lastDate = JFactory::getDate($lastDate.' +1 days')->format('Y-m-d');
		} else {
			$lastDate = JFactory::getDate($lastDate .' -'.($days+1).' days')->format('Y-m-d');
		}
		
		// Include the syndicate functions only once
		require_once (JPATH_BASE.DS.'modules'.DS.'mod_djevents_calendar'.DS.'helper.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'route.php');
		
		$lang = JFactory::getLanguage();
		
		$lang->load('mod_djevents_calendar', JPATH_BASE, null, false, true) ||
		$lang->load('mod_djevents_calendar', JPATH_BASE.'/modules/mod_djevents_calendar', null, false, true);
		
		$params = new JRegistry;
		
		$params->set('layout', 'scroll');
		$params->set('start_date', $lastDate);
		$params->set('days', $days);
		
		$items = modDJEventsCalendarHelper::getItems($params);
		
		ob_clean();
		require JModuleHelper::getLayoutPath('mod_djevents_calendar', $params->get('layout'));
		
		$app->close();
		
	}
	
	public function getMonth() {
		
		$app = JFactory::getApplication();
		
		$lastDate = $app->input->getString('last_date', false);
		
		if (!$lastDate) {
			$app->close();
		}
		
		$lastDate .= '-01';
		
		$d = DateTime::createFromFormat('Y-m-d', $lastDate);
		if (!($d && $d->format('Y-m-d') === $lastDate)) {
			$app->close();
		}
		
		if ($app->input->getInt('direction') > 0) {
			$lastDate = JFactory::getDate($lastDate . ' +1 month');
		} else {
			$lastDate = JFactory::getDate($lastDate . ' -1 month');
		}
		
		// Include the syndicate functions only once
		require_once (JPATH_BASE.DS.'modules'.DS.'mod_djevents_calendar'.DS.'helper.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'route.php');
		
		$lang = JFactory::getLanguage();
		
		$lang->load('mod_djevents_calendar', JPATH_BASE, null, false, true) ||
		$lang->load('mod_djevents_calendar', JPATH_BASE.'/modules/mod_djevents_calendar', null, false, true);
		
		$params = null;
		$module = $app->input->getInt('module');
		
		if($module > 0) {
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__modules WHERE id='.$module);
			$params = new JRegistry($db->loadResult());
		} else {
			$params = new JRegistry;
		}
		
		$params->set('layout', 'month');
		$params->set('start_date', $lastDate);
		$params->set('module', $module);
		
		$items = modDJEventsCalendarHelper::getItems($params);
		
		ob_clean();
		require JModuleHelper::getLayoutPath('mod_djevents_calendar', $params->get('layout'));
		
		$app->close();
	}
}

if(!function_exists('dd')) {
	function dd($msg, $exit = false) {
		if($exit) {
			echo "<pre>".print_r($msg, true)."</pre>"; die();
		} else {
			JFactory::getApplication()->enqueueMessage("<pre>".print_r($msg, true)."</pre>");
		}
	}
}