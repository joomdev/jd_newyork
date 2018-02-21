<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class ArchiveController extends acymailingController{

	function view(){

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->saveStats();

		$printEnabled = JRequest::getVar('print', 0);
		if($printEnabled){
			$js = "setTimeout(function(){
					if(document.getElementById('iframepreview')){
						document.getElementById('iframepreview').contentWindow.focus();
						document.getElementById('iframepreview').contentWindow.print();
					}else{
						window.print();
					}
				},2000);";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		JRequest::setVar('layout', 'view');
		return parent::display();
	}


}
