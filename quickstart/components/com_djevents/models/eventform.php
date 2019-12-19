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

require_once JPath::clean(JPATH_ADMINISTRATOR.'/components/com_djevents/models/event.php');

jimport('joomla.application.component.helper');

class DJEventsModelEventForm extends DJEventsModelEvent
{
	
	public function __construct($config = array()) {
		parent::__construct($config);
		JForm::addFormPath(__DIR__.JPath::clean('/forms'));
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
	
		// Get the form.
		$form = $this->loadForm('com_djevents.'.$this->form_name, $this->form_name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		$user = JFactory::getUser();
		if ($user->id > 0) {
			$form->setValue('created_by', null, $user->id);
		}
		
		return $form;
	}
	
	public function validate($form, $data, $group = null) {
		
		$user = JFactory::getUser();
		$params  = DJEventsHelper::getParams();
		$db = JFactory::getDbo();
		
		if (isset($data['id']) && $data['id'] > 0) {
			// if the event has been edited we don't change it's state
			unset($data['published']);
			
			if ($item = $this->getItem($data['id'])) {
				$data['featured'] = $item->featured;
				$data['created'] = $item->created;
				$data['created_by'] = $item->created_by;
			}
			
		} else {
			if ($user->authorise('event.autopublish', 'com_djevents') || $user->authorise('core.edit.state', 'com_djevents')){
				$data['published'] = 1;
			} else {
				$data['published'] = 0;
			}
			
			if ($user->id > 0) {
				$data['created_by'] = $user->id;
			}
			
			$data['featured'] = 0;
			$data['created'] = '';
		}
		
		return parent::validate($form, $data, $group);
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
	}
	
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		return (bool)($user->authorise('core.delete', 'com_djevents') 
				|| ($user->authorise('event.delete.own', 'com_djevents') && $user->id == $record->created_by));
	}
	
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$canEdit = $user->authorise('core.edit.state', $this->option);
		$canEditOwn = $user->authorise('event.autopublish', $this->option);
		
		return (($user->id == $record->created_by && $canEditOwn) || $canEdit) ? true : false;
	}
	
}