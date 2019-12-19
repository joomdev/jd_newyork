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

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'modeladmin.php');

class DJEventsModelCategory extends DJEventsModelAdmin
{
	protected $text_prefix = 'COM_DJEVENTS';
	protected $form_name = 'category';

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function getTable($type = 'Categories', $prefix = 'DJEventsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_djevents.edit.'.$this->form_name.'.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	protected function _prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name		= htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias		= JFilterOutput::stringURLSafe($table->alias);
		
		if (empty($table->alias)) {
			$table->alias = JFilterOutput::stringURLSafe($table->name);
		}
	}

	protected function getReorderConditions($table = null)
	{
		$condition = array();
		return $condition;
	}

	public function delete(&$cid) {
		if (count( $cid ))
		{
			$cids = implode(',', $cid);

			$this->_db->setQuery("SELECT COUNT(*) FROM #__djev_events WHERE cat_id IN ( ".$cids." )");
			if ($this->_db->loadResult() > 0) {
				$this->setError(JText::_('COM_DJEVENTS_ERROR_RECORDS_HAVE_ITEMS_CATEGORY'));
				return false;
			}
		}
		
		return parent::delete($cid);
	}
}