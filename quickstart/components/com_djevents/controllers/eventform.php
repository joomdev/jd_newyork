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

defined('_JEXEC') or die;

require_once(JPath::clean(JPATH_ADMINISTRATOR.'/components/com_djevents/lib/controllerform.php'));

class DJEventsControllerEventForm extends DJEventsControllerForm {
	
	function __construct($config = array())
	{
		$this->view_list = 'myevents';
		$this->view_item = 'eventform';
		
		parent::__construct($config);
		
		$this->unregisterTask('save2copy');
		
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$app = JFactory::getApplication();
		$tmpl   = $app->input->get('tmpl');
		
		// got rid of edit layout
		$layout = $app->input->get('layout');
		$append = '';
	
		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}
	
		if ($layout)
		{
			$append .= '&layout=' . $layout;
		}
	
		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}
	
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$app = JFactory::getApplication();
		$tmpl = JFactory::getApplication()->input->get('tmpl');
		
		$append = '';
	
		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}
		
		$needles = array(
				'myevents' => array(0),
				'eventslist' => array(0)
		);
		
		if ($item = DJEventsHelperRoute::_findItem($needles)) {
			$append .= '&Itemid='.$item;
		}
		
		return $append;
	}
	
	protected function allowEdit($data = array(), $key = 'id') {
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		//$asset		= 'com_djevents.item.'.$recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $this->option)) {
			return true;
		}
		
		$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
		if (empty($ownerId) && $recordId) {
			// Need to do a lookup from the model.
			$record		= $this->getModel()->getItem($recordId);

			if (empty($record)) {
				return false;
			}

			$ownerId = $record->created_by;
		}

		if ($ownerId == $userId && ($user->authorise('core.edit.own', $this->option) || $user->authorise('event.edit.own', $this->option))) {
			return true;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		
		$authorised =  ($user->authorise('core.create', $this->option) || $user->authorise('event.create', $this->option));
		
		return $authorised;
	}
	
	public function add() {
		$user = JFactory::getUser();
		
		if ((bool)$user->guest && !$this->allowAdd()) {
			$return_url = base64_encode(DJEventsHelperRoute::getEventsListRoute().'&task=eventform.add');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login&return='.$return_url, false), JText::_('COM_DJEVENTS_PLEASE_LOGIN'));
			return true;
		}
		
		return parent::add();
	}
	
	protected function _postSaveHook($model, $validData = array()) {
		
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_djevents');
		$user = JFactory::getUser();
		
		if ((empty($validData['id']) || $validData['id'] == 0) && $user->guest) {
			$this->setRedirect(JRoute::_(DJEventsHelperRoute::getEventsListRoute(),false));
		}
		
		$subject = $validData['id']== 0 ? JText::_('COM_DJEVENTS_MAIL_EVENT_ADDED') : JText::_('COM_DJEVENTS_MAIL_EVENT_CHANGED');
		
		$body = JText::sprintf('COM_DJEVENTS_MAIL_EVENT_HEADING', $user->username).'<br /><br />';
		
		if (isset($validData['title'])) {
			$body .= JText::_('COM_DJEVENTS_TITLE').':<br />';
			$body .= $validData['title'] . '<br /><br />';
		}
		
		if (isset($validData['published'])) {
			if ($validData['published'] == 0) {
				$item = $model->getItem();
				$host = trim(str_replace(JUri::base(true), '', JUri::base()), '/');
				$link = $host.JRoute::_(DJEventsHelperRoute::getEventRoute($item->id.':'.$item->alias, $item->time['start'], $item->cat_id, $item->city_id));
				$link = '<a href="'.$link.'">'.$link.'</a>';
				$this->setMessage(JText::sprintf('COM_DJEVENTS_WAITING_FOR_PUBLICATION_MESSAGE', $link));
				$body .= JText::_('COM_DJEVENTS_MAIL_EVENT_NOT_PUBLISHED').'<br/>';
			} else {
				$this->setMessage(JText::_('COM_DJEVENTS_EVENT_SUCCESSFULLY_ADDED'));
				$body .= JText::_('COM_DJEVENTS_MAIL_EVENT_PUBLISHED').'<br />';
			}
		}
		
		$body .= '<a href="'.JURI::base().'administrator/index.php?option=com_djevents&amp;view=events&amp;filter_search='.urlencode('id:'.$model->getState('eventform.id')).'">'.JText::_('COM_DJEVENTS_MAIL_EVENT_BACK_END_LINK').'</a>';
		
		$this->sendNotification($subject, $body);
	}
	
	protected function sendNotification($subject, $body)
	{
		$app		= JFactory::getApplication();
		$params 	= JComponentHelper::getParams('com_djevents');
		$config 	= JFactory::getConfig();
		
		$mailfrom	= $config->get('mailfrom');
		$fromname	= $config->get('fromname');
		$sitename	= $config->get('sitename');
		
		$contact_list = $params->get('contact_list', false);
		$recipient_list = array();
		if ($contact_list !== false) {
			$recipient_list = explode(PHP_EOL, $params->get('contact_list', ''));
		}
			
		$list_is_empty = true;
		foreach ($recipient_list as $r) {
			if (strpos($r, '@') !== false) {
				$list_is_empty = false;
				break;
			}
		}
	
		if ($list_is_empty) {
			$recipient_list[] = $mailfrom;
		}
			
		$recipient_list = array_unique($recipient_list);
		
		$mail = JFactory::getMailer();
		
		foreach ($recipient_list as $recipient) {
			$mail->addBCC(trim($recipient));
		}
			
		$mail->setSender(array($mailfrom, $fromname));
			
		$mail->setSubject($sitename. ': '. $subject);
		$mail->setBody($body);
		$mail->isHtml(true);
		
		try {
		
			$sent = $mail->Send();
		
		} catch(Exception $e) {
			
			$app->enqueueMessage($e->getMessage(), 'warning');
		}
		
		return $sent;
	}
	
}
?>
