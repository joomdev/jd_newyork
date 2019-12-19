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

require_once(JPATH_ADMINISTRATOR.JPath::clean('/components/com_djevents/lib/modeladmin.php'));

class DJEventsModelEvent extends DJEventsModelAdmin
{
	protected $text_prefix = 'COM_DJEVENTS';
	protected $form_name = 'event';

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function getTable($type = 'Events', $prefix = 'DJEventsTable', $config = array())
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
	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		if($item === false) {
			return false;
		}
		
		if (property_exists($item, 'time'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->time);
			$item->time = $registry->toArray();
		}
	
		return $item;
	}
	
	protected function _prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JFilterOutput::stringURLSafe($table->alias);
		
		if(empty($table->alias)) {
			$table->alias = JFilterOutput::stringURLSafe($table->title);
		}
		
		if(empty($table->city_id)) {
			$table->city_id = 0;
		}
	}

	protected function getReorderConditions($table = null)
	{
		$condition = array();
		return $condition;
	}

	public function delete(&$cid) {
		
		if (count($cid)) {
			
			$this->_db->setQuery('DELETE FROM #__djev_events_media WHERE event_id IN ('.implode(',', $cid).')');
			if ($this->_db->execute() == false) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		
		$deleted = parent::delete($cid);
		
		return $deleted;
		
	}
	
	public function changeFeaturedState($pks, $value) {
		if (empty($pks)) {
			return false;
		}
		$ids = implode(',',$pks);
		$db = JFactory::getDbo();
		$db->setQuery('update #__djev_events set featured='.(int)$value.' where id in ('.$ids.')');
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	public function getImages() {
		
		$app = JFactory::getApplication();
		
		$this->_db->setQuery('SELECT * FROM #__djev_events_media WHERE event_id='.$app->input->getInt('id').' ORDER BY ordering asc');
		$images = $this->_db->loadObjectList();
		
		return $images;
	}
	
	public function getTags() {
		
		$app = JFactory::getApplication();
		
		$this->_db->setQuery('SELECT t.name FROM #__djev_tags t, #__djev_tags_xref x WHERE t.id = x.tag_id AND x.event_id='.$app->input->getInt('id').' ORDER BY t.name asc');
		$tags = $this->_db->loadColumn();
	
		return $tags;
	}
	
	public function save($data){
	
		$app = JFactory::getApplication();
		
		$update_time = true;
		
		/* Check should event time and repetition should be updated */
		if($data['id'] && $data['time']) {
			$registry = new JRegistry();
			$registry->loadArray($data['time']);
			$time = (string)$registry;
			$event = $this->getTable('Events');
			$event->load($data['id']);
			$update_time = md5($event->time) == md5($time) ? false : true;
		}

		/* Check if new City is added and record it in database */
		$rawdata = $app->input->post->get('jform','','raw');
		$name = trim($rawdata['city_id_new']);
		if(!empty($name)) {
			
			$alias = JFilterOutput::stringURLSafe($name);
			if(trim(str_replace('-','',$alias)) == '') {
				$alias = 'city'.JFactory::getDate()->format("YmdHis");
			}
			
			$item 	= $this->getTable('Cities');
			if (!$item->load(array('name'=>$name,'alias'=>$alias))) {

				if ($item->load(array('alias'=>$alias))) {
					
					$tmp = explode('-', $alias);
					$no = array_pop($tmp);
					if(is_numeric($no)) {
						$alias = implode('-', $tmp).'-'.++$no;
					} else {
						$alias .= '-1';
					}
				}

				$item->id = 0;
				$item->name = $name;
				$item->alias = $alias;
				if(!$item->store()) {
					$app->enqueueMessage($item->getError(), 'error');
				}
			}
			
			$data['city_id'] = $item->id;
		}
		
		if($saved = parent::save($data)) {
			
			$event_id = (int) $this->getState($this->getName().'.id');
			
			/* Update event time and repetition */
			
			if($update_time) {
				
				$item 	= $this->getTable('Time');
				$start 	= $data['time']['start'] .' '. (!empty($data['time']['start_time']) ? $data['time']['start_time'] : '00:00:00');
				$end	= !empty($data['time']['end_time']) ? $data['time']['end_time'] : '23:59:59';
				$end 	= $data['time']['use_end'] ? $data['time']['end'] .' '.$end : $start;
				
				// make sure event doesn't end before start
				if(JFactory::getDate($start)->toUnix() > JFactory::getDate($end)->toUnix()) {
					$this->setError(JText::_('COM_DJEVENTS_ERROR_EVENT_ENDS_BEFORE_START'));
					return false;
				}
				
				$item->event_id = $event_id;
				$item->start = $start;
				$item->start_time = !empty($data['time']['start_time']) ? 1 : 0;
				$item->end = $end;
				$item->end_time = !empty($data['time']['end_time']) ? 1 : 0;
				
				// first remove all event repetitions
				$query = 'DELETE FROM #__djev_events_time WHERE event_id='.$event_id;
				$this->_db->setQuery($query);
				$this->_db->execute();
								
				$repeat = $data['time']['repeat'];
				if($repeat) {
					$dates = array();
					
					$start = JFactory::getDate($start);
					$end = JFactory::getDate($end);
					$duration = $end->toUnix() - $start->toUnix();
					
					$include = array_map('trim', explode("\n", $data['time']['include']));
					$exclude = array_map('trim', explode("\n", $data['time']['exclude']));
					
					$interval 	= $data['time']['repeat_interval'];
					$interval 	= ' +' . $interval .' '. str_replace('ly', $interval > 1 ? 's' : '', $repeat); 
					$until		= JFactory::getDate($data['time']['repeat_until'].' 23:59:59')->toUnix();
					
					// make sure repeat until date is later than end event date
					if($start->toUnix() > $until) {
						$this->setError(JText::_('COM_DJEVENTS_ERROR_EVENT_UNTIL_BEFORE_START'));
						return false;
					}
					
					if($repeat == 'weekly' && !$data['time']['weekday']) {
						// weekly repetition with selected week day(s)						
						
						$weekdays = $data['time']['weekly_weekdays'];
						$from = $start->toUnix();
						$weekday = $start->format('N');
						// shift start date to the beginning of the week
						if($weekday != 1) $start = JFactory::getDate($start->format('Y-m-d H:i').' -'.($weekday - 1).' days');

						do {
							foreach($weekdays as $weekday) {
								$start2 = JFactory::getDate($start->format('Y-m-d H:i').' +'.($weekday - 1).' days');
								$end = JFactory::getDate($start2->format('Y-m-d H:i').' +'.$duration.' seconds');
								
								if($start2->toUnix() >= $from && $start2->toUnix() < $until) {
									
									$item->id = 0;
									$item->start = $start2->toSql();
									$item->end = $end->toSql();
									$item->exclude = (in_array($start2->format('Y-m-d'), $exclude) ? 1 : 0);
									if(!$item->store()) {
										$app->enqueueMessage($item->getError(), 'error');
									}
										
									$dates[] = $start2->format('Y-m-d');
								}
							}
							
							$start = JFactory::getDate($start->format('Y-m-d H:i').$interval);
							
						} while ($start->toUnix() < $until);						
						
					} else if ($repeat == 'monthly' && !$data['time']['monthday']) {
						// monthly repetition with selected
						
						$weekno = 	$data['time']['weekno'];
						$weekdays = $data['time']['monthly_weekdays'];
						$from = $start->toUnix();
						
						// shift start date to the beginning of the month
						$start = JFactory::getDate($start->format('Y-m').'-01'.$start->format('H:i'));
						//$app->enqueueMessage(print_r($start->format('Y-m-d H:i, t, N'), true));
						do {
							for($d = 1; $d <= $start->format('t'); $d++) {
								
								$which = ceil($d / 7);
								if(!in_array($which, $weekno)) continue;
								
								$start2 = JFactory::getDate($start->format('Y-m').'-'.($d < 10 ? '0':'').$d.' '.$start->format('H:i'));
								if(!in_array($start2->format('N'), $weekdays)) continue;
								
								$end = JFactory::getDate($start2->format('Y-m-d H:i').' +'.$duration.' seconds');
								
								if($start2->toUnix() >= $from && $start2->toUnix() < $until) {
								
									$item->id = 0;
									$item->start = $start2->toSql();
									$item->end = $end->toSql();
									$item->exclude = (in_array($start2->format('Y-m-d'), $exclude) ? 1 : 0);
									if(!$item->store()) {
										$app->enqueueMessage($item->getError(), 'error');
									}
								
									$dates[] = $start2->format('Y-m-d');
								}
							}
								
							$start = JFactory::getDate($start->format('Y-m-d H:i').$interval);
							
						} while ($start->toUnix() < $until);
						
					} else {
						// simple repetition based on interval only
						do {
							$item->id = 0;
							$item->start = $start->toSql();
							$item->end = $end->toSql();
							$item->exclude = (in_array($start->format('Y-m-d'), $exclude) ? 1 : 0);
							if(!$item->store()) {
								$app->enqueueMessage($item->getError(), 'error');
							}
							
							$dates[] = $start->format('Y-m-d');
							
							$start = JFactory::getDate($start->format('Y-m-d H:i').$interval);
							$end = JFactory::getDate($end->format('Y-m-d H:i').$interval);
							
						} while ($start->toUnix() < $until);
					}
					
					if(count($include)) foreach($include as $date) {
						
						$date = trim($date);
						
						if(!in_array($date, $dates) && $this->validateDate($date)) {
							
							$start = JFactory::getDate($date.' '.$start->format('H:i'));
							$end = JFactory::getDate($start->format('Y-m-d H:i').' +'.$duration.' seconds');
							
							$item->id = 0;
							$item->start = $start->toSql();
							$item->end = $end->toSql();
							$item->exclude = 0;
							if(!$item->store()) {
								$app->enqueueMessage($item->getError(), 'error');
							}
							
							$dates[] = $start->format('Y-m-d');
						}
					}
					
					if(!count($dates)) {
						$app->enqueueMessage(JText::_('COM_DJEVENTS_ERROR_EVENT_REPETITION_FAILD'), 'warning');
							
						if(!$item->store()) {
							$app->enqueueMessage($item->getError(), 'error');
						}
					}
					
				} else {
					
					if(!$item->store()) {
						$app->enqueueMessage($item->getError(), 'error');
					}
				}
			}
			
			
			/* Save event images and videos */
			
			// set folder if needed
			$data['folder'] = 'images/djevents';
			if(!$data['id']) {
				$event = $this->getTable('Events');
				$event->load($event_id);
				$data['folder'] .= '/' . $event->id . '-' . $event->alias;
			}
			//djdebug($this);
			$item = $this->getTable('Media');
			
			$poster = $app->input->post->get('item_poster',0,'string');
			$ids = $app->input->post->get('item_id',array(),'array');
			$titles = $app->input->post->get('item_title',array(),'array');
			$images = $app->input->post->get('item_image',array(),'array');
			ArrayHelper::toInteger($ids);
			
			$posters = array();
			foreach($images as $order => $val) {
				if($poster) {
					$posters[$order] = ($poster == $ids[$order] || $poster == $val ? 1 : 0);
				} else {
					$posters[$order] = $order ? 0 : 1;
				}
			}
			
			// first remove deleted images from the list
			$query = 'DELETE FROM #__djev_events_media WHERE event_id='.$event_id;
			if(count($ids)) $query.= ' AND id NOT IN ('.implode(',', $ids).')';
			$this->_db->setQuery($query);
			$this->_db->execute();
				
			if(count($ids)) {
				foreach($ids as $order => $id) {
					
					if($order == 0) continue; // skip album item template
					
					$item->reset();
					
					if($id) {
						$item->load($id);
						// continue if no changes made
						if($item->title == $titles[$order] && $item->ordering == $order && $item->poster == $posters[$order]) continue;
						
					} else {
						$item->id = 0;
						$item->image = $this->moveUploadedImage($images[$order], $data);
						if(is_null($item->image)) {
							// don't save if move uploaded image faild
							$app->enqueueMessage( JText::_('COM_DJEVENTS_ERROR_MOVE_UPLOADED_IMAGE'), 'error');
							continue;
						}
						$tmp = explode(';', $images[$order]);
						if(count($tmp) > 2) $item->video = $tmp[2];
					}
					
					$item->title = $titles[$order];
					$item->poster = $posters[$order];
					$item->event_id = $event_id;
					$item->ordering = $order;
						
					if(!$item->store()) {
						$app->enqueueMessage($item->getError(), 'error');
					}
				}
	
				$item->reorder('event_id='.$event_id);
			}
			
			
			/* Save event tags */
			$item 	= $this->getTable('Tags');
			
			// first remove all event tags
			$query = 'DELETE FROM #__djev_tags_xref WHERE event_id='.$event_id;
			$this->_db->setQuery($query);
			$this->_db->execute();
			
			$tags = $app->input->get('tags','','raw');
			$tags = explode(',',$tags);
			
			foreach($tags as $tag) {
				
				$tag = trim($tag);
				
				if(empty($tag)) continue;
				
				$alias = JFilterOutput::stringURLSafe($tag);
				if(trim(str_replace('-','',$alias)) == '') {
					$alias = 'tag'.JFactory::getDate()->format("YmdHis");
				}
				
				if (!$item->load(array('name'=>$tag,'alias'=>$alias))) {
						
					if ($item->load(array('alias'=>$alias))) {
				
						$tmp = explode('-', $alias);
						$no = array_pop($tmp);
						if(is_numeric($no)) {
							$alias = implode('-', $tmp).'-'.++$no;
						} else {
							$alias .= '-1';
						}
					}
						
					$item->id = 0;
					$item->name = $tag;
					$item->alias = $alias;
					if(!$item->store()) {
						$app->enqueueMessage($item->getError(), 'error');
					}
				}
				
				if($item->id) {
					// add reference between event and tag
					$query = 'INSERT INTO #__djev_tags_xref (event_id,tag_id) VALUE ('.$event_id.','.$item->id.')';
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
		}
		
		return $saved;
	}
	
	private function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
	
	private function moveUploadedImage($paths = null, $data = null) {
	
		$paths = explode(';', $paths);
		$lang = JFactory::getLanguage();
		$date = JFactory::getDate();
	
		if(count($paths) == 2) {
				
			$folder = $data['folder'] == 'images/djevents' ? $data['folder'] . '/' . $data['id'] . '-' . $data['alias'] : $data['folder'];
				
			$tmpPath = JPATH_ROOT . '/media/djevents/upload/' . $paths[0];
			$path = JPATH_ROOT . DS . str_replace('/', DS, $folder);
			JFolder::create($path);
				
			$filename = str_replace(' ', '_', $paths[1]);
			$filename = $lang->transliterate($filename);
			//$filename = strtolower($filename);
			$filename = JFile::makeSafe($filename);
				
			$name = JFile::stripExt(basename($filename));
			$ext = JFile::getExt($filename);
				
			if(empty($name)) {
				$name = $date->fomat('YmdHis');
				$filename = $name.'.'.$ext;
			}
				
			// prevent overriding the existing file with the same name
			if (JFile::exists($path.DS.$filename)) {
				$iterator = 1;
				$newname = $name.'.'.$iterator.'.'.$ext;
				while (JFile::exists($path.DS.$newname)) {
					$iterator++;
					$newname = $name.'.'.$iterator.'.'.$ext;
				}
				$filename = $newname;
			}
				
			if(JFile::move($tmpPath, $path . DS . $filename)) {
				return $folder . '/' .$filename;
			} else {
				return null;
			}
				
		} else {
				
			return $paths[0];
		}
	}
}