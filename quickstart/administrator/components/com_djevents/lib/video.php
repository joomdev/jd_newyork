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

abstract class DJEventsVideoHelper {
	
	private static $video = array();
	
	public static function getVideo($link) {
		
		$key = md5($link);
		
		if(!isset(self::$video[$key])) {
				
			self::$video[$key] = self::parseVideoLink($link);
				
		}
		
		return self::$video[$key];
		
	}
	
	/* Parsing the passed video url and create object with require information */
	private static function parseVideoLink($link) {
		
		$info = self::getInfoFromUrl($link);
		$video = new stdClass();
		//return $info;
		switch($info->provider) {
			
			case 'dailymotion':
				
				$api_url = 'https://api.dailymotion.com/video/'.$info->id.'?fields=title,embed_url,thumbnail_url,access_error';
				$data = self::getOembedObject($api_url);
				
				//$video->api_url = $api_url;
				$video->title = $data->title;
				$video->embed = $data->embed_url;
				$video->thumbnail = $data->thumbnail_url;
				$video->error = isset($data->error) ? $data->error : $data->access_error;
				break;
				
			case 'metacafe':
				
				$api_url = 'http://www.metacafe.com/api/item/'.$info->id;
				$data = self::getOembedObject($api_url, 'xml');
				
				$content = $data->media_content->attributes();
				$thumb = $data->media_thumbnail->attributes();
				
				$video->title = (string) $data->title;
				$video->embed = (string) $content['url'];
				$video->thumbnail = (string) $thumb['url'];
				//$video->error = isset($data->error) ? $data->error : $data->access_error;
				break;
			
			case 'liveleak':
				
				$data = self::getOembedObject($link, 'og');
				
				$video->title = str_replace('LiveLeak.com - ', '', $data->title);
				$video->thumbnail = $data->image;
				
				if(preg_match('/\/([0-9a-zA-Z]+)_[\w\.]+$/', $data->image, $match)) {
					$video->embed = 'http://www.liveleak.com/ll_embed?f='.$match[1];
				}
				break;
				
			case 'yahoo_movies':
				
				$data = self::getOembedObject($link, 'og');
				
				$video->title = $data->title;
				$video->thumbnail = $data->twitter_image;
				$video->embed = @$data->twitter_player[0];
				
				break;
				
			default:
				
				$api_url = 'https://noembed.com/embed?url='.urlencode($link);
				$data = self::getOembedObject($api_url);
				
				if(!in_array($data->type, array('video', 'rich'))) {
					$video->error = JText::_('COM_DJEVENTS_NOT_SUPPORTED_VIDEO_LINK');
				}
				
				preg_match('/<iframe [^>]*src="([^"]+)"/', $data->html, $match);
				
				if($match) {
					$video->embed = str_replace('http:', '', urldecode($match[1]));
				} else if(!$data->embed) {
					$video->error = JText::_('COM_DJEVENTS_NOT_SUPPORTED_VIDEO_LINK');
				} else {
					$video->embed = $data->embed;
				}
				
				$video->title = $data->title;
				$video->thumbnail = $data->thumbnail_url;
				break;
		}
		
		return $video;
	}
	
	private static function getInfoFromUrl($url) {
		
		$info = new stdClass();
		$info->provider = null;
		
		if(strstr($url, 'dailymotion.com/video') !== false) {
			//http://www.dailymotion.com/video/x34ftdo_need-for-speed-2015-gameplay-innovations-five-ways-to-play-official-street-racing-game-2015_videogames
			
			if(preg_match('/dailymotion\.com\/video\/([^_]+)\_/', $url, $match)) {
				$info->provider = 'dailymotion';
				$info->id = $match[1];
			}
		} else if(strstr($url, 'metacafe.com/watch') !== false) {
			//http://www.metacafe.com/watch/11006706/call_of_duty_ghosts_astronauts_child_of_light_announced_ps_vita_tv_more_destructoid/
			
			if(preg_match('/metacafe\.com\/watch\/(\d+)\/(\w+)\//', $url, $match)) {
				$info->provider = 'metacafe';
				$info->id = $match[1];
			}
		} else if(strstr($url, 'liveleak.com/view') !== false) {
			//http://www.liveleak.com/view?i=ca7_1448277689
			
			if(preg_match('/liveleak\.com\/view\?i=(\w+)/', $url, $match)) {
				$info->provider = 'liveleak';
				$info->id = $match[1];
			}
		} else if(strstr($url, 'yahoo.com/movies') !== false) {
			//https://www.yahoo.com/movies/fifty-shades-of-black-trailer-spoofs-171925287.html
			
			if(preg_match('/yahoo\.com\/movies\/[\w\-]+(\d+)\.html/', $url, $match)) {
				$info->provider = 'yahoo_movies';
				$info->id = $match[1];
			}
		}
		
		return $info;
	}
	
	private static function getOembedObject($url, $format = 'json') {
		
		// use curl to get video oembed information
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		$res = curl_exec($ch);
		
		// curl faild, we try to get video oembed info with file_get_contents
		if(!$res) {
			$res = file_get_contents($url);
		}
		
		if($format == 'xml') {
			
			$xml = simplexml_load_string(str_replace(array("<media:", "</media:"), array("<media_", "</media_"), $res));
			
			if (!$xml)
			{
				$res = '{"error": "Wrong XML format"}';
			} else {
				$res = $xml->channel->item;
			}
		} else if($format == 'og') {
			
			$og = self::getOpenGraph($res);
			//print_r(json_encode((object)$og)); die();
			if(!$og) {
				$res = '{"error": "Open Graph tags missing"}';
			} else {
				$res = (object) $og;
			}
		}
		
		if(is_object($res)) {
			$json = $res;
		} else {
			$json = json_decode($res);
		}
		
		if(!$res && curl_errno($ch))
		{
			$json->error = 'API CALL ERROR ['.$url.']: '.curl_error($ch);
		}
		
		curl_close($ch);
		
		return $json;
	}
	
	public static function getOpenGraph($content) {
		
		$doc = new DOMDocument();
		@$doc->loadHTML($content);
			
		$interested_in = array('og', 'fb', 'twitter'); // Open graph namespaces we're interested in (open graph + extensions)
			
		$ogp = array();
			
		$metas = $doc->getElementsByTagName('meta');
		if (!empty($metas)) {
			for ($n = 0; $n < $metas->length; $n++) {
					
				$meta = $metas->item($n);
					
				foreach (array('name','property') as $name) {
					$meta_bits = explode(':', $meta->getAttribute($name));
					if (isset($meta_bits[1]) && in_array($meta_bits[0], $interested_in)) {
						$key = ($meta_bits[0] != 'og' ? $meta_bits[0].'_':'').$meta_bits[1];
						// If we're adding to an existing element, convert it to an array
						if (isset($ogp[$key]) && (!is_array($ogp[$key])))
							$ogp[$key] = array($ogp[$key], $meta->getAttribute('content'));
						else if (isset($ogp[$key]) && (is_array($ogp[$key])))
							$ogp[$key][] = $meta->getAttribute('content');
						else
							$ogp[$key] = $meta->getAttribute('content');
							
					}
				}
			}
		}
			
		return $ogp;
	}
}