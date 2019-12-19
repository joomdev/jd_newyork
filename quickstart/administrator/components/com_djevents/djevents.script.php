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

class com_djeventsInstallerScript {
	
	function postflight( $type, $parent ) {
		
		// move shared code
		$src = JPath::clean(JPATH_ROOT.'/media/djevents/djextensions');
		$dst = JPath::clean(JPATH_ROOT.'/media/djextensions');
		
		JFolder::create($dst);
		
		$folders = JFolder::folders($src);
		
		foreach($folders as $folder) {
			JFolder::move($src.DIRECTORY_SEPARATOR.$folder, $dst.DIRECTORY_SEPARATOR.$folder);
		}
		
		@JFolder::delete($src);
	}
}