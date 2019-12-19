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

use Joomla\Utilities\ArrayHelper;

require_once(JPath::clean(JPATH_ADMINISTRATOR.'/components/com_djevents/lib/controllerform.php'));

class DJEventsBaseControllerEvent extends DJEventsControllerForm {

	public $controller_type = null;

	function __construct($config = array())
	{
		$this->view_list = '';
		$this->view_item = '';

		parent::__construct($config);

		$this->unregisterTask('save2copy');
	}

	public function add()
	{
		$app = JFactory::getApplication();
		$context = "$this->option.edit.$this->context";

		// Access check.
		if (!$this->allowAdd())
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

	public function edit($key = null, $urlVar = null)
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel();

		$table = $model->getTable();
		$recordId   = $app->input->getInt('id', 0);
		$context = "$this->option.edit.$this->context";

		$key = 'id';

		//$checkin = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return false;
		}

		// Skipping checking-in

		// Attempt to check-out the new record for editing and redirect.
		/*if ($checkin && !$model->checkout($recordId))
		{
		throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
		return false;
		}
		else
		{
		// Check-out succeeded, push the new record id into the session.
		$this->holdEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		return true;
		}*/

		return true;
	}
	
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$id = JFactory::getApplication()->input->getInt('id', 0);
	
		if (!$id)
		{
			JLog::add(JText::_('COM_DJEVENTS_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
			return false;
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
				
			$cid = array($id);
			ArrayHelper::toInteger($cid);
	
			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::_('COM_DJEVENTS_ITEM_DELETED'));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
		
		return true;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();

		return (bool)($user->authorise('event.create', $this->option) || $user->authorise('core.create', $this->option));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowEdit($data = array(), $key = 'id') {
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

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

		return (bool) ($ownerId == $userId && $user->authorise('event.edit.own', $this->option));
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowSave($data, $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}
		else
		{
			return $this->allowAdd($data);
		}
	}

	public function getModel($name = 'EventForm', $prefix = 'DJEventsModel', $config = array('ignore_request' => true))
	{
		if (empty($name))
		{
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $app->input->post->get('jform', array(), 'array');

		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();
		$checkin = property_exists($table, 'checked_out');

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $app->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, true);

		if (!$form)
		{
			$this->setError($model->getError());

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
			
			$messages = array();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$this->setError($errors[$i]->getMessage());
				}
				else
				{
					$this->setError($errors[$i]);
				}
			}
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			return false;


		}
		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			
			return false;
		}

		$this->setMessage(JText::_('COM_DJEVENTS_EVENT_SUCCESSFULLY_ADDED'));

		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}
	
	protected function _postSaveHook($model, $validData = array()) {
		
		$subject = $validData['id']== 0 ? JText::_('COM_DJEVENTS_MAIL_EVENT_ADDED') : JText::_('COM_DJEVENTS_MAIL_EVENT_CHANGED');
		
		$body = JText::_('COM_DJEVENTS_MAIL_EVENT_HEADING').'<br /><br />';
		
		if (isset($validData['title'])) {
			$body .= JText::_('COM_DJEVENTS_TITLE').':<br />';
			$body .= $validData['title'] . '<br /><br />';
		}
				
		if (isset($validData['published'])) {
			if ($validData['published'] == 0) {
				$body .= JText::_('COM_DJEVENTS_MAIL_EVENT_NOT_PUBLISHED').'<br/>';
			} else {
				$body .= JText::_('COM_DJEVENTS_MAIL_EVENT_PUBLISHED').'<br />';
			}
		}
		
		$body .= '<a href="'.JURI::base().'administrator/index.php?option=com_djevents&amp;view=events&amp;filter_search='.urlencode('id:'.$model->getState('eventform.id')).'">'.JText::_('COM_DJEVENTS_MAIL_EVENT_BACK_END_LINK').'</a>';

		$this->sendNotification($subject, $body);
	}
	
	public function report() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
	
		die('NOT SUPPORTED YET');
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
		$sent = $mail->Send();
	
		return $sent;
	}
}