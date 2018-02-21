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

class SendController extends acymailingController{

	function sendready(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		JRequest::setVar('layout', 'sendconfirm');
		return parent::display();
	}

	function send(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		JRequest::checkToken() or die('Invalid Token');

		JRequest::setVar('tmpl', 'component');
		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) exit;

		$user = JFactory::getUser();
		$time = time();
		$queueClass = acymailing_get('class.queue');
		$queueClass->onlynew = JRequest::getInt('onlynew');
		$queueClass->mindelay = JRequest::getInt('mindelay');
		$totalSub = $queueClass->queue($mailid, $time);

		if(empty($totalSub)){
			acymailing_display(JText::_('NO_RECEIVER'), 'warning');
			return;
		}

		$mailObject = new stdClass();
		$mailObject->senddate = $time;
		$mailObject->published = 1;
		$mailObject->mailid = $mailid;
		$mailObject->sentby = $user->id;
		$db = JFactory::getDBO();
		$db->updateObject(acymailing_table('mail'), $mailObject, 'mailid');

		$config =& acymailing_config();
		$queueType = $config->get('queue_type');
		if($queueType == 'onlyauto'){
			$messages = array();
			$messages[] = JText::sprintf('ADDED_QUEUE', $totalSub);
			$messages[] = JText::_('AUTOSEND_CONFIRMATION');
			acymailing_display($messages, 'success');
			return;
		}else{
			JRequest::setVar('totalsend', $totalSub);
			$app = JFactory::getApplication();
			$app->redirect(acymailing_completeLink('send&task=continuesend&mailid='.$mailid.'&totalsend='.$totalSub, true, true));
			exit;
		}
	}

	function continuesend(){
		$config = acymailing_config();

		if(acymailing_level(1) && $config->get('queue_type') == 'onlyauto'){
			JRequest::setVar('tmpl', 'component');
			acymailing_display(JText::_('ACY_ONLYAUTOPROCESS'), 'warning');
			return;
		}


		$newcrontime = time() + 120;
		if($config->get('cron_next') < $newcrontime){
			$newValue = new stdClass();
			$newValue->cron_next = $newcrontime;
			$config->save($newValue);
		}

		$mailid = acymailing_getCID('mailid');

		$totalSend = JRequest::getVar('totalsend', 0, '', 'int');
		$alreadySent = JRequest::getVar('alreadysent', 0, '', 'int');

		$helperQueue = acymailing_get('helper.queue');
		$helperQueue->mailid = $mailid;
		$helperQueue->report = true;
		$helperQueue->total = $totalSend;
		$helperQueue->start = $alreadySent;
		$helperQueue->pause = $config->get('queue_pause');
		$helperQueue->process();

		JRequest::setVar('tmpl', 'component');



	}


	function spamtest(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		$config = acymailing_config();
		ob_start();
		$urlSite = trim(base64_encode(preg_replace('#https?://(www\.)?#i', '', ACYMAILING_LIVE)), '=/');
		$url = ACYMAILING_SPAMURL.'spamTestSystem&component=acymailing&level='.strtolower($config->get('level', 'starter')).'&urlsite='.$urlSite;
		$spamtestSystem = acymailing_fileGetContent($url, 30);

		$warnings = ob_get_clean();

		if(empty($spamtestSystem) || $spamtestSystem === false || !empty($warnings)){
			acymailing_display('Could not load your information from our server'.((!empty($warnings) && defined('JDEBUG') && JDEBUG) ? $warnings : ''), 'error');
			return;
		}
		$decodedInformation = json_decode($spamtestSystem, true);
		if(!empty($decodedInformation['messages']) || !empty($decodedInformation['error'])){
			$msgError = (!empty($decodedInformation['messages'])) ? $decodedInformation['messages'].'<br />' : '';
			$msgError .= (!empty($decodedInformation['error'])) ? $decodedInformation['error'] : '';
			acymailing_display($msgError, 'error');
			return;
		}
		if(empty($decodedInformation['email'])){
			acymailing_display('Missing test mail address', 'error');
			return;
		}

		$receiver = new stdClass();
		$receiver->subid = 0;
		$receiver->email = $decodedInformation['email'];
		$receiver->name = $decodedInformation['name'];
		$receiver->html = 1;
		$receiver->confirmed = 1;
		$receiver->enabled = 1;

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkPublished = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;
		$mailerHelper->report = false;

		if(!$mailerHelper->sendOne($mailid, $receiver)){
			acymailing_display($mailerHelper->reportMessage, 'error');
			return;
		}

		$app = JFactory::getApplication();
		$app->redirect($decodedInformation['displayURL']);

		return;
	}
}
