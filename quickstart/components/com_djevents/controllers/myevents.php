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

defined('_JEXEC') or die( 'Restricted access' );

use \Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.controlleradmin');

class DJEventsControllerMyEvents extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unpublish', 'publish');
	}
	
	public function &getModel($name = 'EventForm', $prefix = 'DJEventsModel', $config = array())
	
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
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
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
			
			$cid = array($id);
			ArrayHelper::toInteger($cid);
	
			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::_('COM_DJEVENTS_EVENT_DELETED'));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
	
		$this->setRedirect(JRoute::_(DJEventsHelperRoute::getMyEventsRoute(), false));
	}
	
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
		
		$app = JFactory::getApplication();
		
		// Get items to publish from the request.
		$id = JFactory::getApplication()->input->getInt('id', 0);
		$data = array('publish' => 1, 'unpublish' => 0);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
	
		if (!$id)
		{
			JLog::add(JText::_('COM_DJEVENTS_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			$cid = array($id);
			ArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value))
			{
				JLog::add($model->getError(), JLog::WARNING, 'jerror');
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_DJEVENTS_EVENT_PUBLISHED';
				}
				else
				{
					$ntext = 'COM_DJEVENTS_EVENT_UNPUBLISHED';
				}
				
				$this->setMessage(JText::_($ntext));
			}
		}
		$this->setRedirect(JRoute::_(DJEventsHelperRoute::getMyEventsRoute(), false));
	}
	
}