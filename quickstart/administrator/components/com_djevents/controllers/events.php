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

jimport('joomla.application.component.controlleradmin');

class DJEventsControllerEvents extends JControllerAdmin
{
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unfeatured',	'featured');
	}
	
	public function getModel($name = 'Event', $prefix = 'DJEventsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		$app = JFactory::getApplication();
	
		$session	= JFactory::getSession();
		$registry	= $session->get('registry');
	
		// Get items to publish from the request.
		$cid	= $app->input->get('cid', array(), 'array');
		$task 	= $this->getTask();
		$value = ($task == 'featured') ? 1 : 0;
	
		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);
	
			// Publish the items.
			if (!$model->changeFeaturedState($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}
		$extension = $app->input->get('extension', null, 'cmd');
		$extensionURL = ($extension) ? '&extension=' . $app->input->get('extension', null, 'cmd') : '';
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$extensionURL, false));
	}
}
