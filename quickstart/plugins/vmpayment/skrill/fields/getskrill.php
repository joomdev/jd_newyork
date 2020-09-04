<?php
/**
 *
 * Realex payment plugin
 *
 * @author Valerie Isaksen
 * @version $Id$
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004 - 2019 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
class JFormFieldGetSkrill extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $type = 'getSkrill';

	protected function getInput() {
		vmJsApi::addJScript('adminskrill', '/plugins/vmpayment/skrill/assets/js/admin_skrill.js');
		vmJsApi::css('skrill', '/plugins/vmpayment/skrill/assets/css/');
		$logoSkrillUrl = JURI::root() . '/plugins/vmpayment/skrill/assets/images/skrill.png';
		$logoSkrillWidgetUrl = JURI::root() . '/plugins/vmpayment/skrill/assets/images/skrill_widget.png';
		$signUpLogo = JURI::root() . '/plugins/vmpayment/skrill/assets/images/signup.png';
		$verifyLogo = JURI::root() . '/plugins/vmpayment/skrill/assets/images/verify.png';
		$guideLogo = JURI::root() . '/plugins/vmpayment/skrill/assets/images/guide.png';
		$logoSkrillUrl = '<img alt="skrill logo" src="'.$logoSkrillUrl.'" class="skrill-logo" />';
		$logoWidgetUrl = '<img alt="skrill widget" src="'.$logoSkrillWidgetUrl.'" class="skrill-about-image" />';
		$html = '<div class="skrill-header-group">
					<div class="skrill-header-logo">
						' . $logoSkrillUrl . '
					</div>
					<div class="skrill-header-text">
					</div>
					<div class="clear"></div>
				</div>';
		$html .= '<div class="skrill-content-wrapper">
					<div class="skrill-content skrill-content-img">
					' . $logoWidgetUrl . '
					</div>
					<div class="skrill-content skrill-content-text">
						<div class="skrill-title-container"><span class="skrill-title">ABOUT SKRILL</span></div>
						<p>Trusted by millions - Skrill meets the needs of more than 156,000 businesses worldwide providing a convenient and secure way to send and receive money in nearly 200 countries, 18 languages and 40 currencies.</p>
						<p>Together with Virtuemart, Skrill offers a fully integrated payment solution, which can begin accepting payments within hours.</p>
						<br>
						<p>Skrill benefits for you & your customers: </p>
						<ul>
							<li>Free and quick set up</li>
							<li>Activate credit cards and 100+ local payment solutions with 1 easy integration</li>
							<li>Take advantage of the Skrill multicurrency account, giving you access to 40+ currencies</li>
							<li>High security standards and anti-fraud technology</li>
							<li>Seamless payment experience across mobile, tablet and desktop</li>
							<li>Connect with millions of Skrill account holders</li>
						</ul>
						<p>The Skrill fee structure is a competitive 1.9% + € 0.29 per transaction.</p>
						<p>*Fee applies to new merchants only. From 14th August 2017.</p>
						<br>
						<div class="skrill-footer-btn">
							<a href="https://signup.skrill.com/onboarding/#/?rdu=onboarding&rid=128963654&lang=ENa" target="_blank" class="skrill-btn-signup">
								sign up now
							</a>
						</div>
					</div>
				</div>';

		return $html;
	}

}