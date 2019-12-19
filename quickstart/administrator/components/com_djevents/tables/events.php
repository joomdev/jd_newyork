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

class DJEventsTableEvents extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__djev_events', 'id', $db);
	}
	public function bind($array, $ignore = '')
	{
		if (isset($array['time']) && is_array($array['time'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['time']);
			$array['time'] = (string)$registry;
		}
		
		if(empty($array['alias'])) {
			$array['alias'] = $array['title'];
		}
		$array['alias'] = JFilterOutput::stringURLSafe($array['alias']);
		if(trim(str_replace('-','',$array['alias'])) == '') {
			$array['alias'] = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		return parent::bind($array, $ignore);
	}
	
	public function check() {
		
		return parent::check();
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		if (!$this->id) {
			if (!intval($this->created)) {
				$this->created = $date->toSql();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}	
		
		$table = JTable::getInstance('Events', 'DJEventsTable');
		
		if ($table->load(array('title'=>$this->title)) && $app->input->get('task') == 'save2copy') {
			$this->title .= ' (copy)';
		}
		
		//if ($table->load(array('alias'=>$this->alias,'cat_id'=>$this->cat_id,'city_id'=>$this->city_id)) && ($table->id != $this->id || $this->id==0)) {
		if ($table->load(array('alias'=>$this->alias)) && ($table->id != $this->id || $this->id==0)) {
			
			$alias = explode('-', $this->alias);
			$no = array_pop($alias);
			if(is_numeric($no)) {
				$this->alias = implode('-', $alias).'-'.++$no;
			} else {
				$this->alias .= '-1';
			}
		}
		
		/*
		 $table = JTable::getInstance('Events', 'DJEventsTable');
		if ($table->load(array('alias'=>$this->alias,'cat_id'=>$this->cat_id, 'city_id'=>$this->city_id)) && ($table->id != $this->id || $this->id==0)) {
		$alias = end(explode('-', $this->alias));
		if(preg_match('/\d{14}/', $alias)) $this->alias = substr($string, 0, -15);
		$this->alias .= '-'.JFactory::getDate()->format("YmdHis");
		//JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DJMEDIATOOLS_ALBUM_SAVED_UNIQUE_ALIAS', $this->alias));
		/*
		if(preg_match('/\-(\d+)$/', $this->alias, $matches)) {
		print_r($matches);
		$no = (int) $matches[1];
		$this->alias = preg_replace('/\-\d+$/', '-'.($no++), $this->alias);
		}
		$this->alias .= '-1';
			
		}
		*/
		return parent::store($updateNulls);
	}
	
}