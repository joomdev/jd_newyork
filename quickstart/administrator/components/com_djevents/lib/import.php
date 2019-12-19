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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

abstract class DJEventsImportHelper {
	
	private static $_limit = 50;
	
	/* function remove all data from component */
	public static function truncateAll() {
		
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		
		$db->setQuery("TRUNCATE #__djev_cats");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_cities");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_events");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_events_media");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_events_time");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_tags");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		$db->setQuery("TRUNCATE #__djev_tags_xref");
		if(!$db->execute()) {
			$app->enqueueMessage($db->getErrorMsg(), 'error');
			return false;
		}
		if(JFile::exists(JPATH_ROOT.'/administrator/components/com_falang/contentelements/jevents_vevdetail.xml')) {
			//remove DJ-Events translations from falang
			$db->setQuery("DELETE FROM #__falang_content WHERE reference_table IN ('djev_events','djev_cats','djev_cities')");
			if(!$db->execute()) {
				$app->enqueueMessage($db->getErrorMsg(), 'error');
				return false;
			}
		}
		
		return true;
	}
	
	public static function importJEvents() {
		
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
		
		/* import categories */
		$db->setQuery("SELECT * FROM #__categories WHERE extension LIKE 'com_jevents' ORDER BY id ASC");
		$categories = $db->loadObjectList();
		
		$table = JTable::getInstance('Categories', 'DJEventsTable');
		
		$table->icon_type = '';
		$table->icon_color = '#f5f5f5';
		$table->icon_bg = '#454545';
		
		if(count($categories)) foreach($categories as $item) {
			
			$table->id 			= 0;
			$table->name		= $item->title;
			$table->alias		= $item->alias;
			$table->description	= $item->description;
			$table->created		= $item->created_time;
			$table->created_by 	= $item->created_user_id ? $item->created_user_id : $user->id;
			
			if(!$table->store()) {
				$app->enqueueMessage($table->getErrorMsg(), 'error');
			}
			
			$db->setQuery("UPDATE #__djev_cats SET id=".$item->id." WHERE id=".$table->id);
			if(!$db->execute()) {
				$app->enqueueMessage($db->getErrorMsg(), 'error');
			}
			$db->setQuery("ALTER TABLE #__djev_cats AUTO_INCREMENT = ".$item->id);
			if(!$db->execute()) {
				$app->enqueueMessage($db->getErrorMsg(), 'error');
			}
		}
		
		/* import cities if JEvents Locations is installed */
		$locations 	= array();
		$cities		= array();
		if(JFile::exists(JPATH_ROOT.'/components/com_jevlocations/jevlocations.php')) {
			
			$db->setQuery("SELECT * FROM #__jev_locations WHERE published=1");
			$locations = $db->loadObjectList('loc_id');
			
			$table = JTable::getInstance('Cities', 'DJEventsTable');
			
			if(count($locations)) foreach($locations as $item) {
				
				$name = $item->city;
				
				if(!empty($name)) {
			
					$alias = JFilterOutput::stringURLSafe($name);
					if(trim(str_replace('-','',$alias)) == '') {
						$alias = 'city'.JFactory::getDate()->format("YmdHis");
					}
					
					if (!$table->load(array('name'=>$name,'alias'=>$alias))) {
		
						if ($table->load(array('alias'=>$alias))) {
							
							$tmp = explode('-', $alias);
							$no = array_pop($tmp);
							if(is_numeric($no)) {
								$alias = implode('-', $tmp).'-'.++$no;
							} else {
								$alias .= '-1';
							}
						}
		
						$table->id = 0;
						$table->name = $name;
						$table->alias = $alias;
						if(!$table->store()) {
							$app->enqueueMessage($item->getError(), 'error');
						}
					}
					
					$cities[$item->loc_id] = $table->id;
				}
			}
		}
		
		$app->setUserState('geoloc', null);
		$app->setUserState('locations', $locations);
		$app->setUserState('cities', $cities);
		
		/* import events */
		return self::importJEventsStep();
	}
	
	public static function importJEventsStep($step = 0) {
		
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		
		$start = $step * self::$_limit;
		$step++;
		
		$geoloc = $app->getUserState('geoloc');
		if(!is_array($geoloc)) $geoloc = array();
		$locations = $app->getUserState('locations');
		$cities = $app->getUserState('cities');
		$multidays = $app->getUserState('multidays');
		if(!is_array($multidays)) {
			$multidays = array('CHANGED'=>array(),'REVIEW'=>array());
		}
		
		$weekdays = array('MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6,'SU'=>7);
		
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
		$eventTable = JTable::getInstance('Events', 'DJEventsTable');
		$timeTable = JTable::getInstance('Time', 'DJEventsTable');
		$mediaTable = JTable::getInstance('Media', 'DJEventsTable');
		
		/* import events
		 * NOTE: JEvents allows to change each repetition of the event separately,
		 * but DJ-Events doesn't support this feature, so we ignore the exception table */
		$query 	= " SELECT e.*, ed.*, r.* FROM #__jevents_vevent e, #__jevents_vevdetail ed, #__jevents_rrule r "
				. " WHERE e.detail_id=ed.evdet_id AND e.ev_id=r.eventid ORDER BY e.ev_id ASC ";
		
		$db->setQuery($query, $start, self::$_limit);
		$events = $db->loadObjectList('ev_id');
		
		$db->setQuery($query);
		$db->execute();
		$total = $db->getNumRows();
		
		if(count($events)) {
			
			$ev_ids = implode(',',array_keys($events));
		
			$db->setQuery("SELECT * FROM #__jevents_repetition WHERE eventid IN (".$ev_ids.") ORDER BY eventid ASC, startrepeat ASC");
			$times = $db->loadObjectList();
			
			$plgFiles = JFile::exists(JPATH_ROOT.'/plugins/jevents/jevfiles/jevfiles.php');
			if($plgFiles) {
			
				$db->setQuery("SELECT * FROM #__jev_files WHERE filetype='file' AND filename<>'' AND ev_id IN (".$ev_ids.") ORDER BY ev_id ASC");
				$files = $db->loadObjectList();
				
				$db->setQuery("SELECT * FROM #__jev_files WHERE filetype='image' AND filename<>'' AND ev_id IN (".$ev_ids.") ORDER BY ev_id ASC");
				$images = $db->loadObjectList();
			}
			
			$falang = JFile::exists(JPATH_ROOT.'/administrator/components/com_falang/contentelements/jevents_vevdetail.xml');
			if($falang) {
				//JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_falang/tables');
				$falangTable = JTable::getInstance('Falang', 'DJEventsTable');
				$falangCheckTable = JTable::getInstance('Falang', 'DJEventsTable');
				$query = "SELECT * FROM #__falang_content WHERE reference_table='jevents_vevdetail' AND reference_id IN (".$ev_ids.") ORDER BY reference_id ASC";
				$db->setQuery($query);
				$trans = $db->loadObjectList();
				//print_r($trans);
				//die($query);
			}
			
			foreach($events as $id => $item) {
			
				// get the event dates
				$dates = array();
				foreach($times as $key => $time) {
					if($time->eventid != $id) continue;
					if(JFactory::getDate($time->startrepeat)->toUnix() <= JFactory::getDate($time->endrepeat)->toUnix()) {
						/* jevents can contain wrong data structure where event ends before it starts 
						 * we don't improt that kind of events, because they won't be displayed anyway */
						$dates[] = $time;
					}
					unset($times[$key]);
				}
				
				// if there is no dates for this event we don't want to import it
				if(!count($dates)) continue;
				
				// get images for gallery from files plugin
				$gallery = array();
				if($plgFiles) {
					foreach($images as $key => $img) {
						if($img->ev_id != $id) continue;
						$filepath = 'images/jevents/'.$img->filename;
						if(JFile::exists(JPATH_ROOT.'/'.$filepath)) {
							$gallery[] = $filepath;
						}
						unset($images[$key]);
					}
				}
				
				// get images for gallery from description
				$images_text = self::getImagesFromText($item->description);
				foreach($images_text as $key => $filepath) {
					if(JFile::exists(JPATH_ROOT.'/'.$filepath)) {
						$gallery[] = $filepath;
					}
					unset($images_text[$key]);
				}
				
				$eventTable->id = 0;
				$eventTable->cat_id = $item->catid;
				$eventTable->city_id = isset($cities[$item->location]) ? $cities[$item->location] : 0;
				$eventTable->title = $item->summary;
				$eventTable->alias = JFilterOutput::stringURLSafe($item->summary);
				if(trim(str_replace('-','',$eventTable->alias)) == '') {
					$eventTable->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
				}
				$eventTable->description = $item->description;
				$eventTable->intro = self::truncateText($item->description, 400);
				$eventTable->published = $item->state;
				$eventTable->external_url = $item->url;
				$eventTable->price = $item->contact;
				if(!empty($item->organizer)) $eventTable->price .= "\n\n";
				$eventTable->price .= $item->organizer;
				if(!empty($item->extra_info)) $eventTable->price .= "\n\n";
				$eventTable->price .= $item->extra_info;
				
				if(isset($locations[$item->location])) {
					
					$location = $locations[$item->location];
					
					$eventTable->location = $location->title;
					$eventTable->address = $location->street;
					$eventTable->post_code = $location->postcode;
					$eventTable->latitude = $location->geolat;
					$eventTable->longitude = $location->geolon;
					$eventTable->zoom = $location->geozoom;
				} else {
					
					$eventTable->location = $item->location;
					$eventTable->address = '';
					$eventTable->post_code = '';
					$eventTable->latitude = $item->geolat;
					$eventTable->longitude = $item->geolon;
					$eventTable->zoom = 15;
				}
				
				// update geolocation if it's not set or default
				if(	$eventTable->latitude == 0 && $eventTable->longitude == 0 ||
					$eventTable->latitude == -5.3 && $eventTable->longitude == -3.6) {
					
					$address = $eventTable->location;
					$address.= !empty($eventTable->address) ? ', '.$eventTable->address : '';
					$address.= !empty($eventTable->post_code) ? ', '.$eventTable->post_code : '';
					
					$key = md5($address);
					if(isset($geoloc[$key])) {
						$loc = $geoloc[$key];
					} else {
						$loc = self::getLocation($address);
						$loc['address'] = $address;
						$geoloc[$key] = $loc;
					}
					
					if($loc) {
						$eventTable->latitude = $loc['lat'];
						$eventTable->longitude = $loc['lng'];
						$eventTable->zoom = 15;
					}
				}
				
				$eventTable->created = $item->created ? $item->created : JFactory::getDate()->toSql();
				$eventTable->created_by = $item->created_by;
				
				$start = explode(' ',$dates[0]->startrepeat);
				$end = explode(' ',$dates[0]->endrepeat);
				
				/* this is fix for mugello */
				/*
				if(!$item->multiday) {
					if(count($dates) > 1) {
						// fix wrong setup for mugello
						$end[0] = $start[0];
						$multidays['CHANGED'][$id] = $item->summary;
					} else {
						$multidays['REVIEW'][$id] = $item->summary;
					}
				}
				*/
				/* end of fix for mugello */
				
				/* creating time rules and repetition */
				$time = new JRegistry();
				
				$time->set('start', $start[0]);
				if($start[1]=='00:00:00' && $end[1]=='23:59:59') {
					$timeTable->start_time = 0;
				} else {
					$time->set('start_time', substr($start[1],0,5));
					$timeTable->start_time = 1;
				}
				$use_end = 1;
				if($start[0] == $end[0] && $end[1] == '23:59:59') {
					$use_end = 0;
				}
				$time->set('use_end', $use_end);
				$time->set('end', $end[0]);
				if($end[1] != '23:59:59') {
					$time->set('end_time', substr($end[1],0,5));
					$timeTable->end_time = 1;
				} else {
					$timeTable->end_time = 0;
				}
				
				$repeat = count($dates) > 1 ? strtolower($item->freq) : '';
				
				$until = explode(' ', end($dates)->endrepeat);
				$until = $until[0];
				
				$time->set('repeat', $repeat);
				$time->set('repeat_interval', $item->rinterval);
				$time->set('repeat_until', $until);
				if($repeat=='weekly') {
					$time->set('weekday', 0);
					$byday = explode(',',$item->byday);
					$days = array();
					foreach($byday as $day) {
						$days[] = $weekdays[$day];
					}
					$time->set('weekly_weekdays', $days);
				} else {
					$time->set('weekday', 1);
				}
				if($repeat=='monthly') {
					$time->set('monthday', 0);
					$byday = explode(',',$item->byday);
					$weeks = array();
					$days = array();
					foreach($byday as $day) {
						$week = substr($day, 1, 1);
						if(!in_array($week, $weeks)) $weeks[] = $week;
						$d = substr($day, 2, 2);
						if(!in_array($weekdays[$d], $days)) $days[] = $weekdays[$d];
					}
					$time->set('weekno', $weeks);
					$time->set('monthly_weekdays', $days);
				} else {
					$time->set('monthday', 1);
				}
				$time->set('include', '');
				$time->set('exclude', '');
				
				$eventTable->time = (string) $time;
				
				if(!$eventTable->store()) {
					$app->enqueueMessage($eventTable->getErrorMsg(), 'error');
				}
				
				$db->setQuery("UPDATE #__djev_events SET id=".$id." WHERE id=".$eventTable->id);
				if(!$db->execute()) {
					$app->enqueueMessage($db->getErrorMsg(), 'error');
				}
				$db->setQuery("ALTER TABLE #__djev_events AUTO_INCREMENT = ".$id);
				if(!$db->execute()) {
					$app->enqueueMessage($db->getErrorMsg(), 'error');
				}
				
				$timeTable->event_id = $id;
				$timeTable->exclude = 0;
				
				// now we need to save the event dates
				foreach($dates as $date) {
					
					$timeTable->id = 0;
					$timeTable->start = $date->startrepeat;
					
					/* this is fix for mugello */
					/*
					if($use_end && !$item->multiday && count($dates) > 1) {
						// fix wrong setup for mugello
						$start = explode(' ',$date->startrepeat);
						$end = explode(' ',$date->endrepeat);
						
						$timeTable->end = $start[0].' '.$end[1];
					} else {
					/* end of fix for mugello */
						$timeTable->end = $use_end ? $date->endrepeat : $date->startrepeat;
					//}
					
					if(!$timeTable->store()) {
						$app->enqueueMessage($timeTable->getErrorMsg(), 'error');
					}
				}
				
				/* Save event images and videos */
				
				if(count($gallery)) {
					
					$mediaTable->title = $eventTable->title;
					$mediaTable->event_id = $id;
					
					foreach($gallery as $order => $src) {
							
						
						$mediaTable->id = 0;
						$mediaTable->image = $src;
						$mediaTable->poster = $order == 0 ? 1 : 0;
						$mediaTable->ordering = $order;
							
						if(!$mediaTable->store()) {
							$app->enqueueMessage($mediaTable->getError(), 'error');
						}
					}
					
					$mediaTable->reorder('event_id='.$id);
				}
				
				/* Import falang translations */ 
				if($falang) {
					
					foreach($trans as $key => $tran) {
						//print_r($tran->reference_id .' != '.$id . ($trans->reference_id != $id ? ' TRUE':' FALSE')); die();
						if($tran->reference_id != $id) continue;
						
						if($falangTable->load($tran->id)) {
							
							$falangTable->id = 0;
							$falangTable->reference_id = $id;
							$falangTable->reference_table = 'djev_events';
							switch($falangTable->reference_field) {
								case 'summary':
									$falangTable->reference_field = 'title';
									break;
								case 'url':
									$falangTable->reference_field = 'external_url';
									break;
								case 'contact':
								case 'organizer':
								case 'extra_info':
									
									if($falangCheckTable->load(array('language_id'=>$falangTable->language_id, 'reference_table'=>'djev_events', 'reference_field'=>'price'))) {
											
										if(!empty($falangCheckTable->original_text)) $falangCheckTable->original_text .= "\n\n";
										$falangCheckTable->original_text .= $falangTable->original_text;
										
										if(!empty($falangCheckTable->value)) $falangCheckTable->value .= "\n\n";
										$falangCheckTable->value .= $falangTable->value;
										
										if(!$falangCheckTable->store()) {
											$app->enqueueMessage($falangCheckTable->getError(), 'error');
										}
									} else {
										$falangTable->reference_field = 'price';
									}
									
									break;
								case 'description':
									
									self::getImagesFromText($falangTable->value);
									$falangTable->value = $falangTable->value;
									$falangTable->original_text = $item->description;
										
									if(!$falangTable->store()) {
										$app->enqueueMessage($falangTable->getError(), 'error');
									}
									$falangTable->id = 0;
									$falangTable->reference_field = 'intro';
									$falangTable->original_text = self::truncateText($falangTable->original_text, 400);
									$falangTable->value = self::truncateText($falangTable->value, 400);
									break;
								default:
									continue 2;
							}
							
							if(!$falangTable->store()) {
								$app->enqueueMessage($falangTable->getError(), 'error');
							}
						}
						
						unset($trans[$key]);
					}
				}
				
				/*
				self::debug($item);			
				self::debug($time);
				return true;
				*/
			}
			//self::debug($cities);
			//self::debug($geoloc);
			//return true;
			$app->setUserState('geoloc', $geoloc);
			$app->setUserState('multidays', $multidays);
		}
		
		if(count($events) == self::$_limit) {
			$progres = $step*self::$_limit*100 / $total;
			echo '<div class="container center">';
			echo '<h1>'.JText::_('COM_DJEVENTS_IMPORT_JEVENTS_HEADING').'</h1>';
			echo '<div class="progress progress-striped"><div class="bar" style="width: '.$progres.'%;"></div></div>';
			echo '<h2>'.JText::sprintf('COM_DJEVENTS_IMPORT_JEVENTS_PROGRESS', $step*self::$_limit, $total).'</h2>';
			echo '</div>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djevents&view=cpanel&task=import&source=jevents&step='.$step.'&t='.time());
			return false;
		}
		
		$db->setQuery("SELECT count(id) FROM #__djev_events");
		$count = $db->loadResult();
		if(!$count) $count = '0';
		
		//self::debug($multidays);
		$app->setUserState('multidays', null);
		
		$app->enqueueMessage(JText::sprintf('COM_DJEVENTS_IMPORT_JEVENTS_SUCCESS_MSG', $count, $total), 'message');
		return true;
	}
	
	public static function getLocation($address){
		
		$params = JComponentHelper::getParams('com_djevents');
		$api_key = $params->get('map_api_key','');
		if(!empty($api_key)) $api_key = '&key='.$api_key;
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false".$api_key."&address=".urlencode($address);
		
        $resp_json = self::curl_file_get_contents($url);
        $resp = json_decode($resp_json, true);

        if($resp['status']='OK' && isset($resp['results'][0])){
            return $resp['results'][0]['geometry']['location'];
        }else{
            return false;
        }
    }

	static private function curl_file_get_contents($URL){
		
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
	
	public static function getImagesFromText(&$text, $remove = true)
	{
		$images = array();
		if(preg_match_all("/<img [^>]*src=\"([^\"]*)\"[^>]*>/", $text, $matches)){
			if($remove) $text = preg_replace("/<img[^>]*>/", '', $text);
			$images = $matches[1];
		}
		
		return $images;
	}
	
	public static function truncateText($text, $limit) {
		
		$desc = strip_tags($text);
		
		if($limit && $limit - strlen($desc) < 0) {
			$desc = substr($desc, 0, $limit);
			// don't cut in the middle of the word unless it's longer than 20 chars
			if($pos = strrpos($desc, ' ')) {
				$limit = ($limit - $pos > 20) ? $limit : $pos;
				$desc = substr($desc, 0, $limit);
			}
			// cut text and add dots
			if(preg_match('/[a-zA-Z0-9]$/', $desc)) $desc.='&hellip;';
			
		} else { // no limit or limit greater than description
		}
		
		$desc = '<p>'.$desc.'</p>';

		return $desc;
	}
	
	public static function debug($msg) {
		
		$app = JFactory::getApplication();
		
		$app->enqueueMessage("<pre>".print_r($msg, true)."</pre>");
		
	}
}