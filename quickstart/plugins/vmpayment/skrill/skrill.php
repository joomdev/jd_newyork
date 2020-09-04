<?php

/*
* @author Skrill Holdings Ltd.
* @version $Id: SKRILL.php 7487 2013-12-17 15:03:42Z alatak $
* @package VirtueMart
* @subpackage payment
* @copyright Copyright (c) 2014 - 2019 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

defined ('_JEXEC') or die('Restricted access');
if (!class_exists ('vmPSPlugin')) {
	require(VMPATH_PLUGINLIBS .'/vmpsplugin.php');
}

class plgVmpaymentSkrill extends vmPSPlugin {

	/**
	 * payment method
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $paymentMethod;

	/**
	 * plugin config
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $pluginConfig;

	/**
	 * Constructor
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 	1.0.0
	 */
	function __construct (&$subject, $config) {

		parent::__construct ($subject, $config);

		if (!class_exists('SkrillPaymentCore'))
		{
			$path = VMPATH_ROOT .'/plugins/vmpayment/skrill/helpers/core.php';
			if(file_exists($path)) require $path;
		}

		// unique filelanguage for all SKRILL methods
		$jlang = JFactory::getLanguage ();
		$jlang->load ('plg_vmpayment_skrill', JPATH_ADMINISTRATOR, NULL, TRUE);
		$this->_loggable = TRUE;
		$this->_debug = TRUE;
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$this->_tablepkey = 'id'; //virtuemart_SKRILL_id';
		$this->_tableId = 'id'; //'virtuemart_SKRILL_id';

		$varsToPush = $this->getVarsToPush();
		$this->addVarsToPushCore($varsToPush, 1);
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);
	}

	/**
	 * Get the parameters of a plugin
	 * This function extends from vmPSPlugin class in VirtueMart
	 *
	 * @return  array
	 * @since   1.0.3
	 */
	public function getVarsToPush()
	{
		$varsToPush = parent::getVarsToPush();
		$varsToPush['hide_login'] = array(0, 'int');
		$varsToPush['logourl'] = array('', 'char');
		return $varsToPush;
	}

	/**
	 * Plugin create table SQL
	 *
	 * @return  boolean
	 * @since   1.0.0
	 */
	public function getVmPluginCreateTableSQL () {

		return $this->createTableSQL ('Payment SKRILL Table');
	}

	/**
	 * Process Status
	 *
	 * @param $mb_data array
	 * @param $method object
	 * @return  srting|boolean
	 * @since   1.0.0
	 */
	function _processStatus (&$mb_data, $method) {

		switch ($mb_data['status']) {
			case 2 :
				$mb_data['payment_status'] = 'Completed';
				break;
			case 0 :
				$mb_data['payment_status'] = 'Pending';
				break;
			case -1 :
				$mb_data['payment_status'] = 'Cancelled';
				break;
			case -2 :
				$mb_data['payment_status'] = 'Failed';
				break;
			case -3 :
				$mb_data['payment_status'] = 'Chargeback';
				break;
		}

		$md5data = $method->skrill_general_merchant_id . $mb_data['transaction_id'] .
			strtoupper (md5 (trim($method->secret_word))) . $mb_data['mb_amount'] . $mb_data['mb_currency'] .
			$mb_data['status'];

		$calcmd5 = md5 ($md5data);
		if (strcmp (strtoupper ($calcmd5), $mb_data['md5sig'])) {
			return "MD5 checksum doesn't match - calculated: $calcmd5, expected: " . $mb_data['md5sig'];
		}

		return FALSE;
	}

	/**
	 * Get payment response HTML
	 *
	 * @param $paymentTable object
	 * @param $payment_name string
	 * @return  object
	 * @since   1.0.0
	 */
	function _getPaymentResponseHtml ($paymentTable, $payment_name) {
		vmLanguage::loadJLang('com_virtuemart');

		$html = '<table>' . "\n";
		$html .= $this->getHtmlRow ('COM_VIRTUEMART_PAYMENT_NAME', $payment_name);
		if (!empty($paymentTable)) {
			$html .= $this->getHtmlRow (vmText::_('VMPAYMENT_SKRILL_ORDER_NUMBER'), $paymentTable->order_number);
		}
		$html .= '</table>' . "\n";

		return $html;
	}

	/**
	 * Get Internal Data
	 *
	 * @param $virtuemart_order_id int
	 * @param $order_number int
	 * @return  object
	 * @since   1.0.0
	 */
	function _getInternalData ($virtuemart_order_id, $order_number = '') {

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';
		if ($order_number) {
			$q .= " `order_number` = '" . $order_number . "'";
		} else {
			$q .= ' `virtuemart_order_id` = ' . $virtuemart_order_id;
		}

		$db->setQuery ($q);
		if (!($paymentTable = $db->loadObject ())) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		return $paymentTable;
	}

	/**
	 * Get store Internal Data
	 *
	 * @param $method object
	 * @param $mb_data array
	 * @param $virtuemart_order_id int
	 * @return  void
	 * @since   1.0.0
	 */
	function _storeInternalData ($method, $mb_data, $virtuemart_order_id) {

		// get all know columns of the table
		$db = JFactory::getDBO ();
		$query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';
		$db->setQuery ($query);
		$columns = $db->loadColumn (0);

		$post_msg = '';
		foreach ($mb_data as $key => $value) {
			$post_msg .= $key . "=" . $value . "<br />";
			$table_key = 'mb_' . $key;
			if (in_array ($table_key, $columns)) {
				$response_fields[$table_key] = $value;
			}
		}

		$response_fields['payment_name'] = vmText::sprintf('SKRILL_BACKEND_PM_'.$mb_data['payment_type'], $virtuemart_order_id);
		$response_fields['payment_method'] = 'skrill_'.strtolower($mb_data['payment_type']);
		$response_fields['mbresponse_raw'] = $post_msg;
		$response_fields['order_number'] = $mb_data['transaction_id'];
		$response_fields['mb_transaction_id'] = $mb_data['mb_transaction_id'];
		$response_fields['virtuemart_order_id'] = $virtuemart_order_id;
		$response_fields['virtuemart_paymentmethod_id'] = $method->virtuemart_paymentmethod_id;
		$this->storePSPluginInternalData ($response_fields, 'virtuemart_order_id', TRUE);
	}

	/**
	 * Get Table SQL Fields
	 *
	 * @return  array
	 * @since   1.0.0
	 */
	function getTableSQLFields () {

		$SQLfields = array('id'                     => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
		                   'virtuemart_order_id'    => 'int(1) UNSIGNED',
		                   'order_number'           => ' char(64)',
		                   'virtuemart_paymentmethod_id'
		                                             => 'mediumint(1) UNSIGNED',
		                   'payment_name'            => 'varchar(5000)',
		                   'payment_method'            => 'varchar(5000)',
		                   'payment_order_total'     => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
		                   'payment_currency'        => 'char(3) ',
		                   'cost_per_transaction'    => 'decimal(10,2)',
		                   'cost_percent_total'      => 'decimal(10,2)',
		                   'tax_id'                  => 'smallint(1)',

		                   'user_session'            => 'varchar(255)',

			// status report data returned by SKRILL to the merchant
		                   'mb_pay_to_email'         => 'varchar(50)',
		                   'mb_pay_from_email'       => 'varchar(50)',
		                   'mb_merchant_id'          => 'int(10) UNSIGNED',
		                   'mb_transaction_id'       => 'varchar(15)',
		                   'mb_rec_payment_id'       => 'int(10) UNSIGNED',
		                   'mb_rec_payment_type'     => 'varchar(16)',
		                   'mb_amount'               => 'decimal(19,2)',
		                   'mb_currency'             => 'char(3)',
		                   'mb_status'               => 'tinyint(1)',
		                   'mb_md5sig'               => 'char(32)',
		                   'mb_sha2sig'              => 'char(64)',
		                   'mbresponse_raw'          => 'varchar(512)');

		return $SQLfields;
	}

	/**
	 * Displays payment widget view
	 *
	 * @param   object $cart 	cart
	 * @param   object $order 	order
	 * @return  boolean|null
	 * @since   1.0.0
	 */
	function plgVmConfirmedOrder ($cart, $order) {

		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return NULL;
		} // Another method was selected, do nothing

		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}

		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		$this->logInfo ('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

		if (!class_exists ('VirtueMartModelOrders')) {
			require(VMPATH_ADMIN .'/models/orders.php');
		}
		if (!class_exists ('VirtueMartModelCurrency')) {
			require(VMPATH_ADMIN .'/models/currency.php');
		}

		$usrBT = $order['details']['BT'];
		$address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);

		if (!class_exists ('TableVendors')) {
			require(VMPATH_ADMIN .'/tables/vendors.php');
		}
		$this->getPaymentCurrency ($method);

		$totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total,$method->payment_currency);
		$cartCurrency = CurrencyDisplay::getInstance($cart->pricesCurrency);

		if ($totalInPaymentCurrency['value'] <= 0) {
			vmInfo (vmText::_ ('VMPAYMENT_SKRILL_PAYMENT_AMOUNT_INCORRECT'));
			return FALSE;
		}

		$lang = JFactory::getLanguage ();
		$tag = substr ($lang->get ('tag'), 0, 2);
		$postVariables = $this->getPostParameters($method, $address, $order, $tag, $totalInPaymentCurrency);
		
		if (!$postVariables) {
			return FALSE;
		}

		// Prepare data that should be stored in the database
		$dbValues['user_session'] = $return_context;
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['payment_name'] = $this->renderPluginName ($method, $order);
		$dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$dbValues['payment_currency'] = $method->payment_currency;
		$dbValues['payment_order_total'] = $totalInPaymentCurrency['value'];
		$dbValues['tax_id'] = $method->tax_id;
		$this->storePSPluginInternalData ($dbValues);

		try {
			$sid = SkrillPaymentCore::getSid($postVariables);
		} catch (Exeption $e) {
			vmInfo (vmText::_ ('ERROR_GENERAL_REDIRECT'));
			return FALSE;
		}

		if (!$sid) {
			vmInfo (vmText::_ ('ERROR_GENERAL_REDIRECT'));
			return FALSE;	
		}

		$skrillPaymentUrl = SkrillPaymentCore::getSkrillPaymentUrl($sid);

		if ($method->skrill_general_display != 'IFRAME') {
			$application = JFactory::getApplication();
			$application->redirect($skrillPaymentUrl);
		}

		$height = $method->hide_login ? 720 : 500;
		$html = '<html><head><title></title>
				<style>.vm-wrap iframe {height: 1100px;}</style>
				<script type="text/javascript">
                	jQuery(document).ready(function () {
                    	jQuery(\'#main h3\').css("display", "none");
                	});
                </script></head><body>';
		$html .= '<iframe src="'.$skrillPaymentUrl.'" scrolling="yes" style="x-overflow: none;"
                frameborder="0" height="' . (string)$height . 'px" width="650px"></iframe>';

		$cart->_confirmDone = FALSE;
		$cart->_dataValidated = FALSE;
		$cart->setCartIntoSession ();
		vRequest::setVar ('html', $html);
	}

	/**
     * Get Post Parameters
     *
     * @param $method object
     * @param $address object
     * @param $order array
     * @param $tag string
     * @param $totalInPaymentCurrency array
     * @return array
     */
	private function getPostParameters($method, $address, $order, $tag, $totalInPaymentCurrency)
	{
		$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' .
			$method->payment_currency . '" ';
		$db = JFactory::getDBO ();
		$db->setQuery ($q);
		$currency_code_3 = $db->loadResult ();

		$merchant_email = $method->pay_to_email;
		if (empty($merchant_email)) {
			vmInfo (vmText::_ ('VMPAYMENT_SKRILL_MERCHANT_EMAIL_NOT_SET'));
			return FALSE;
		}

		$vendorModel = VmModel::getModel ('Vendor');
		$vendorModel->setId (1);
		$vendor = $vendorModel->getVendor ();
		$vendorModel->addImages ($vendor, 1);

		$postParameters = array();
		$paymentMethodKey = strtoupper(vRequest::getVar('skrill_paymentmethod', ''));
		if ($paymentMethodKey == "SKRILL_ACC") {
			$postParameters['payment_methods'] = "VSA, MSC";
		} elseif ($paymentMethodKey != 'SKRILL_APM') {
			$postParameters['payment_methods'] = str_replace("SKRILL_", "", $paymentMethodKey);
		}

		$postParameters['pay_to_email'] = $merchant_email;
		$postParameters['pay_from_email'] = $address->email;
		$postParameters['recipient_description'] = $vendorModel->getVendorName();
		$postParameters['transaction_id'] = $order['details']['BT']->order_number;
		$postParameters['return_url'] = JURI::root () .
			                        'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on=' .
			                        $order['details']['BT']->order_number .
			                        '&pm=' .
			                        $order['details']['BT']->virtuemart_paymentmethod_id .
		                            '&Itemid=' . vRequest::getInt ('Itemid') .
								    '&lang='.vRequest::getCmd('lang','');
		$postParameters['cancel_url'] = JURI::root () .
							'index.php?option=com_virtuemart&view=cart&Itemid=' . vRequest::getInt('Itemid');
		$postParameters['status_url'] = JURI::root () .
			                        'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&lang='.vRequest::getCmd('lang','');
		$postParameters['merchant_fields'] = 'platform';			                        
		$postParameters['platform'] = '128963654';
		$postParameters['hide_login'] = $method->hide_login;
		$postParameters['prepare_only'] = 1;
		$postParameters['logo_url'] = $method->logourl;
		$postParameters['language'] = strtoupper ($tag);
		$postParameters['firstname'] = $address->first_name;
		$postParameters['lastname'] = $address->last_name;
		$postParameters['address'] = $address->address_1;
		$postParameters['address2'] = isset($address->address_2) ? $address->address_2 : '';
		$postParameters['phone_number'] = $address->phone_1;
		$postParameters['postal_code'] = $address->zip;
		$postParameters['city'] = $address->city;
		$postParameters['state'] = isset($address->virtuemart_state_id) ? ShopFunctions::getStateByID($address->virtuemart_state_id, 'state_2_code') : '';
		$postParameters['country'] = ShopFunctions::getCountryByID($address->virtuemart_country_id, 'country_3_code');
		$postParameters['amount'] = $totalInPaymentCurrency['value'];
		$postParameters['currency'] = $currency_code_3;
		$postParameters['detail1_description'] = vmText::_('VMPAYMENT_SKRILL_ORDER_NUMBER') . ': ';
		$postParameters['detail1_text'] = $order['details']['BT']->order_number;

	
		return $postParameters;
	}

	/**
     * Get payment Currency
     *
     * @param $virtuemart_payment_method_id int
     * @param $paymentCurrencyId int
     * @return void|boolean|null
     */
	function plgVmgetPaymentCurrency ($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL;
		} // Another method was selected, do nothing

		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}

		$this->getPaymentCurrency ($method);
		$paymentCurrencyId = $method->payment_currency;
	}

	/**
	 * Displays plugin list when checkout payment
	 * This function extends from vmPSPlugin class in VirtueMart
	 *
	 * @param   string  $plugin 			plugin
	 * @param   string  $selectedPlugin 	selected plugin
	 * @param   string  $pluginSalesPrice 	plugin sales price
	 * @return  string
	 */
	protected function getPluginHtml($plugin, $selectedPlugin, $pluginSalesPrice)
	{
		$this->setPluginConfig($plugin);
		$session = JFactory::getSession();
		$skrillSession = $session->get('skrill_pm', 0, 'vm');
		$skrillData['skrill_paymentmethod'] = false;

		if ($skrillSession)
		{
			$skrillData = json_decode($skrillSession, true);
		}

		$pluginMethodId = $this->_idName;
		$pluginName = $this->_psType . '_name';

		if (!class_exists('CurrencyDisplay'))
		{
			require VMPATH_ADMIN .'/helpers/currencydisplay.php';
		}

		$currency = CurrencyDisplay::getInstance();
		$costDisplay = "";

		if ($pluginSalesPrice)
		{
			$costDisplay = $currency->priceDisplay($pluginSalesPrice);
			$costDisplay = '<span class="' . $this->_type . '_cost"> (' .
				vmText::_('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') . $costDisplay . ")</span>";
		}

		$dynUpdate = '';

		if (VmConfig::get('oncheckout_ajax', false))
		{
			$dynUpdate = ' data-dynamic-update="1" ';
		}

		$checked = '';
		$html = '<div style="display:none"><input type="radio"' . $dynUpdate . ' name="' . $pluginMethodId .
			'" id="' . $this->_psType . '_id_' . $plugin->$pluginMethodId . '"   value="' .
			$plugin->$pluginMethodId . '" ' . $checked . ">"
			. '<label for="' . $this->_psType . '_id_' . $plugin->$pluginMethodId . '">' .
			'<span class="' . $this->_type . '">' .
			$plugin->$pluginName . $costDisplay . "</span></label></div>";

		$paymentMethods = SkrillPaymentCore::$paymentMethods;
		$sortedPluginMethods = array();

		foreach (array_keys($paymentMethods) as $pm)
		{
			$sortedPluginMethods[$pm] = $this->getPluginConfig($pm . '_sort_order');
		}

		$keys   = array_keys($sortedPluginMethods);
		$values = array_values($sortedPluginMethods);
		array_multisort($values, $keys);
		$sortedPaymentMethod = $keys;

		$billingCountryCode = $this->getBillingCountryCode();

		foreach ($sortedPaymentMethod as $pm) {
			$this->setPaymentMethod($pm);
			$pmActive = $this->getPluginConfig($pm . '_active');
			if ( $pm == 'skrill_vsa'
				|| $pm == 'skrill_amx'
				|| $pm == 'skrill_msc'
			) {
				$pmAccActive = $this->getPluginConfig('skrill_acc_active');
				$pmAccShowSeparately = $this->getPluginConfig('skrill_acc_separately');
				if ($pmAccShowSeparately && $pmAccActive) {
					$pmActive = false;
				}
			}
			if ($pmActive
				&& $this->isPaymentMethodSupportCountry($pm, $billingCountryCode)
				&& $this->isShowSeparately($pm)
			) {
				$checkedPaymentMethod = '';

				if ($selectedPlugin == $plugin->$pluginMethodId
					&& $pm == $skrillData['skrill_paymentmethod'])
				{
					$checkedPaymentMethod = 'checked="checked"';
				}

				$html .= '<div class="skrill-payment-selection" style="position: relative;">
					<input style="position: absolute; top: 15px;" onclick="document.getElementById(\'payment_id_'
					. $plugin->$pluginMethodId . '\').click();" type="radio"
					id="skrill_paymentmethod" name="skrill_paymentmethod" value="'
					. $pm . '" ' . $checkedPaymentMethod . '/>'
					. $this->getLogoHTML($pm, $billingCountryCode)
					. '<span class="' . $this->_type . '">' . $costDisplay . "</span></div>";
			}
		}

		$html .= '<script type="text/javascript">
			var paymentSelection = document.querySelectorAll(".skrill-payment-selection");
			[].forEach.call(paymentSelection, function(elm){
				elm.addEventListener("click", function(){
					this.querySelector("input").click();
				});
			});
		</script>';

		return $html;
	}

	/**
	 * Check if the payment conditions are fulfilled for this payment method
	 * @param VirtueMartCart $cart
	 * @param int $activeMethod
	 * @param array $cart_prices
	 * @return bool
	 */
	protected function checkConditions($cart, $activeMethod, $cart_prices) {
		return parent::checkConditions($cart, $activeMethod, $cart_prices);
	}

	/**
     * is Show separately
     *
     * @param $paymentMethod string
     * @return boolean
     */
	protected function isShowSeparately($paymentMethod)
	{
		if ($paymentMethod == 'skrill_acc') {
			return $this->getPluginConfig('skrill_acc_separately');
		}

		return true;
	}

	/**
     * Get logo HTML
     *
     * @param $paymentMethod string
     * @param $billingCountryCode string
     * @return string
     */
	protected function getLogoHTML($paymentMethod, $billingCountryCode)
	{
		$paymentBrand = str_replace('skrill_', '', $paymentMethod);
		if (strtoupper($paymentBrand) == 'AOB' || strtoupper($paymentBrand) == 'ADB' || strtoupper($paymentBrand) == 'ACI') {
			$bankOfCountries = SkrillPaymentCore::$paymentMethods[$paymentMethod]['allowedCountries'][$billingCountryCode];
			$logoHtml = '';
			foreach ($bankOfCountries as $logo) {
				$logoHtml .= '<div style="margin: 5px 5px 5px 25px;">
				<img src="plugins/vmpayment/skrill/assets/images/' . $logo
					. '" style="float:left; margin: 5px; height:35px !important;"/>
				</div>';
			}
			return $logoHtml.'<div style="clear: both;"></div>';
		} else {
			return '<img src="plugins/vmpayment/skrill/assets/images/' . $paymentBrand
					. '.png" style="margin: 5px 5px 5px 25px; height:35px !important;"/>';
		}
	}

	/**
     * Get Billing Country code
     *
     * @return string
     */
	protected function getBillingCountryCode() {
		$billingAddress = $this->getAddress('BT');
    	return  ShopFunctions::getCountryByID($billingAddress['virtuemart_country_id'], 'country_3_code');
	}

	/**
     * check whether payment method support country
     *
     * @return array
     */
    protected function isPaymentMethodSupportCountry($paymentMethod, $billingCountryCode)
    {
    	$supportPayment = SkrillPaymentCore::getSupportedPaymentsByCountryCode($billingCountryCode);
    	if (!in_array($paymentMethod, $supportPayment)) {
    		return false;
    	}

        return true;
    }

    /**
	 *	return shipping address if $type ST and billing address if $type is BT
	 *
	 * @param 	string $type type
	 * @return  array
	 * @since   1.1.07
	 */
	protected function getAddress($type)
	{
		$cart = VirtueMartCart::getCart();

		if ($type == 'BT')
		{
			return $cart->BT;
		}

		return $cart->ST;
	}

	/**
	 * Set payment method
	 *
	 * @param   string  $paymentMethod paymet method
	 * @return  void
	 * @since   1.0.0
	 */
	public function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}

	/**
	 * Set plugin config
	 *
	 * @param   string  $pluginConfig plugin config
	 * @return  void
	 * @since   1.0.0
	 */
	public function setPluginConfig($pluginConfig)
	{
		$this->pluginConfig = $pluginConfig;
	}

	/**
	 * Get plugin config
	 *
	 * @param   string  $key key
	 * @return  string
	 * @since   1.0.0
	 */
	protected function getPluginConfig($key)
	{
		return $this->pluginConfig->$key;
	}

	/**
     * Get Skrill Redirect Url by $skrillPrepareUrl and sid
     *
     * @param $sid
     * @return string
     */
	function getSkrillRedirectUrl($sid) {
		$skrillRedirectUrl = 'https://pay.skrill.com?sid='.$sid;
        return $skrillRedirectUrl;
	}

	/**
	 * process Payment Response Received
	 *
	 * @param   string $html html
	 * @return  boolean|null|string
	 * @since   1.0.0
	 */
	function plgVmOnPaymentResponseReceived (&$html) {

		if (!class_exists ('VirtueMartCart')) {
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		}
		if (!class_exists ('shopFunctionsF')) {
			require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		if (!class_exists ('VirtueMartModelOrders')) {
			require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
		}

		vmLanguage::loadJLang('com_virtuemart_orders', TRUE);
		$mb_data = vRequest::getPost();


		// the payment itself should send the parameter needed.
		$virtuemart_paymentmethod_id = vRequest::getInt ('pm', 0);
		$order_number = vRequest::getString ('on', 0);
		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL;
		} // Another method was selected, do nothing

		if (!$this->selectedThisElement ($method->payment_element)) {
			return NULL;
		}

		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($order_number))) {
			return NULL;
		}

		if (!($paymentTable = $this->getDataByOrderId ($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		vmLanguage::loadJLang('com_virtuemart');
		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($virtuemart_order_id);

		vmdebug ('SKRILL plgVmOnPaymentResponseReceived', $mb_data);
		$payment_name = $this->renderPluginName ($method);
		$html = $this->_getPaymentResponseHtml ($paymentTable, $payment_name);
		$link=	JRoute::_("index.php?option=com_virtuemart&view=orders&layout=details&order_number=".$order['details']['BT']->order_number."&order_pass=".$order['details']['BT']->order_pass, false) ;

		$html .='<br />
		<a class="vm-button-correct" href="'.$link.'">'.vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER').'</a>';

		$cart = VirtueMartCart::getCart ();
		$cart->emptyCart ();
		return TRUE;
	}

	/**
	 * process User Payment Cancel
	 *
	 * @return  boolean|null
	 * @since   1.0.0
	 */
	function plgVmOnUserPaymentCancel () {

		if (!class_exists ('VirtueMartModelOrders')) {
			require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
		}

		$order_number = vRequest::getString ('on', '');
		$virtuemart_paymentmethod_id = vRequest::getInt ('pm', '');
		if (empty($order_number) or
			empty($virtuemart_paymentmethod_id) or
			!$this->selectedThisByMethodId ($virtuemart_paymentmethod_id)
		) {
			return NULL;
		}

		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($order_number))) {
			return NULL;
		}

		if (!($paymentTable = $this->getDataByOrderId ($virtuemart_order_id))) {
			return NULL;
		}

		VmInfo (vmText::_ ('VMPAYMENT_SKRILL_PAYMENT_CANCELLED'));
		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		if (strcmp ($paymentTable->user_session, $return_context) === 0) {
			$this->handlePaymentUserCancel ($virtuemart_order_id);
		}

		return TRUE;
	}

	/**
	 * process Payment Notification Status URL
	 *
	 * @return  boolean|null
	 * @since   1.0.0
	 */
	function plgVmOnPaymentNotification () {

		if (!class_exists ('VirtueMartModelOrders')) {
			require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
		}

		$statusUrlResponse = vRequest::getRequest();
		$mb_data = vRequest::getPost();
		$this->logInfo ('plgVmOnPaymentNotification mb_data: ' . print_r($mb_data, true), 'message');
		if ($statusUrlResponse['action'] == 'refund') {
			$this->paymentrefundNotification();
		}


		if (!isset($mb_data['transaction_id'])) {
			return;
		}

		$order_number = $mb_data['transaction_id'];
		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($mb_data['transaction_id']))) {
			return;
		}

		if (!($payment = $this->getDataByOrderId ($virtuemart_order_id))) {
			return;
		}

		$method = $this->getVmPluginMethod ($payment->virtuemart_paymentmethod_id);
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}

		if (!$payment) {
			$this->logInfo ('getDataByOrderId payment not found: exit ', 'ERROR');
			return NULL;
		}
		$this->_storeInternalData ($method, $mb_data, $virtuemart_order_id);

		$modelOrder = VmModel::getModel ('orders');
		$vmorder = $modelOrder->getOrder ($virtuemart_order_id);
		$order = array();
		$error_msg = $this->_processStatus ($mb_data, $method);

		if ($error_msg) {
			$order['customer_notified'] = 0;
			$order['order_status'] = $method->status_canceled;
			$order['comments'] = 'process IPN ' . $error_msg;
			$modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);
			$this->logInfo ('process IPN ' . $error_msg, 'ERROR');
		} else {
			$this->logInfo ('process IPN OK', 'message');
		}

		if (empty($mb_data['payment_status']) ||
			($mb_data['payment_status'] != 'Completed' &&
				$mb_data['payment_status'] != 'Pending')
		) { // can't get status or payment failed
			//return false;
		}
		$order['customer_notified'] = 1;

		if (strcmp ($mb_data['payment_status'], 'Completed') == 0) {
			$order['order_status'] = $method->status_success;
			$order['comments'] = vmText::sprintf ('VMPAYMENT_SKRILL_PAYMENT_STATUS_CONFIRMED', $order_number);
		} elseif (strcmp ($mb_data['payment_status'], 'Pending') == 0) {
			$order['comments'] = vmText::sprintf ('VMPAYMENT_SKRILL_PAYMENT_STATUS_PENDING', $order_number);
			$order['order_status'] = $method->status_pending;
		}
		else {
			$order['order_status'] = $method->status_canceled;
		}

		$this->logInfo ('plgVmOnPaymentNotification return new_status:' . $order['order_status'], 'message');

		$modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);

		//// remove vmcart
		$this->emptyCart ($payment->user_session, $mb_data['transaction_id']);
	}

	/**
	 * Display stored payment data for an order
	 *
	 * @param   string $virtuemartOrderId 	virtuemart order id
	 * @param   string $virtuemartPaymentId virtuemart payment id
	 * @return  string
	 * @since   1.0.0
	 */
	public function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $payment_method_id) {

		if (!$this->selectedThisByMethodId ($payment_method_id)) {
			return NULL;
		} // Another method was selected, do nothing

		if (!($paymentTable = $this->_getInternalData ($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}

		$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' .
			$paymentTable->payment_currency . '" ';
		$db = JFactory::getDBO ();
		$db->setQuery ($q);
		$currency_code_3 = $db->loadResult ();
		$html = '<table class="adminlist table">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$html .= $this->getHtmlRowBE ('PAYMENT_NAME', $paymentTable->payment_name);

		$code = "mb_";
		foreach ($paymentTable as $key => $value) {
			if (substr ($key, 0, strlen ($code)) == $code) {
				if ($key == 'mb_pay_to_email' || $key == 'mb_transaction_id' || $key == 'PAYMENT_NAME') {
					$html .= $this->getHtmlRowBE (vmText::_ (strtoupper($key)), $value);
				}
			}
		}
		$html .= '</table>' . "\n";
		return $html;
	}




	/**
	 * We must reimplement this triggers for joomla 1.7
	 */

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPaymentPluginTable ($jplugin_id) {

		return $this->onStoreInstallPluginTable ($jplugin_id);
	}

	/**
	 * This event is fired after the payment method has been selected. It can be used to store
	 * additional payment info in the cart.
	 *
	 * @author Max Milbers
	 * @author Valérie isaksen
	 *
	 * @param VirtueMartCart $cart: the actual cart
	 * @return null if the payment was not selected, true if the data is valid, error message if the data is not valid
	 *
	 */
	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart,  &$msg)
	{

		$session = JFactory::getSession();
		$skrillSession = new stdClass;
		$skrillPaymentMethod = vRequest::getVar('skrill_paymentmethod', '');
		$skrillSession->virtuemart_payment_method_id = $cart->virtuemart_paymentmethod_id;
		$skrillSession->skrill_paymentmethod = $skrillPaymentMethod;
		$session->set('skrill_pm', json_encode($skrillSession), 'vm');

		return $this->OnSelectCheck($cart);
	}

	/**
	 * plgVmDisplayListFEPayment
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
	 *
	 * @param object  $cart Cart object
	 * @param integer $selected ID of the method selected
	 * @return boolean True on success, false on failures, null when this plugin was not selected.
	 * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
	 *
	 * @author Valerie Isaksen
	 * @author Max Milbers
	 */
	public function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {
		if ($this->getPluginMethods($cart->vendorId) === 0) {
			if (empty($this->_name)) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(vmText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));
				return false;
			} else {
				return false;
			}
		}
		$method_name = $this->_psType . '_name';
		$idN = 'virtuemart_'.$this->_psType.'method_id';

		foreach ($this->methods as $this->_currentMethod) {
			if ($this->checkConditions($cart, $this->_currentMethod, $cart->cartPrices)) {
				$html = '';
				$cartPrices=$cart->cartPrices;
				if (isset($this->_currentMethod->cost_method)) {
					$cost_method=$this->_currentMethod->cost_method;
				} else {
					$cost_method=true;
				}
				$methodSalesPrice = $this->setCartPrices($cart, $cartPrices, $this->_currentMethod, $cost_method);

				$this->_currentMethod->payment_currency = $this->getPaymentCurrency($this->_currentMethod);
				$this->_currentMethod->$method_name = $this->renderPluginName($this->_currentMethod);

				$html .= $this->getPluginHtml($this->_currentMethod, $selected, $methodSalesPrice);
				$htmlIn[$this->_psType][$this->_currentMethod->$idN] =$html;
			}
		}

		return true;
	}


	public function plgVmonSelectedCalculatePricePayment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

		return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);
	}

	/**
	 * plgVmOnCheckAutomaticSelectedPayment
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 *
	 * @author Valerie Isaksen
	 * @param VirtueMartCart cart: the cart object
	 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 *
	 */
	function plgVmOnCheckAutomaticSelectedPayment (VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {

		return parent::onCheckAutomaticSelected ($cart, $cart_prices, $paymentCounter);
	}

	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the method-specific data.
	 *
	 * @param integer $order_id The order ID
	 * @return mixed Null for methods that aren't active, text (HTML) otherwise
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 */
	public function plgVmOnShowOrderFEPayment ($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {

		$this->onShowOrderFE ($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
	}

	/**
	 * This event is fired during the checkout process. It can be used to validate the
	 * method data as entered by the user.
	 *
	 * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
	 * @author Max Milbers

	public function plgVmOnCheckoutCheckDataPayment($psType, VirtueMartCart $cart) {
	return null;
	}
	 */

	/**
	 * This method is fired when showing when priting an Order
	 * It displays the the payment method-specific data.
	 *
	 * @param integer $_virtuemart_order_id The order ID
	 * @param integer $method_id  method used for this order
	 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	function plgVmonShowOrderPrintPayment ($order_number, $method_id) {

		return $this->onShowOrderPrint ($order_number, $method_id);
	}

	/**
	 * Save updated order data to the method specific table
	 *
	 * @param   array 	$order 			order
	 * @param   string 	$oldOrderStatus old order status
	 * @return  boolean|null
	 * @since   1.0.0
	 */
	public function plgVmOnUpdateOrderPayment(&$order, $oldOrderStatus) {
		if (!($method = $this->getVmPluginMethod($order->virtuemart_paymentmethod_id)))
		{
			// Another method was selected, do nothing
			return null;
		}

		if (!$this->selectedThisElement($method->payment_element))
		{
			return null;
		}

		// Load the payments
		if (!($payments = $this->getDatasByOrderId($order->virtuemart_order_id)))
		{
			return null;
		}

		$payment = $payments[0];
		$this->setPluginConfig($method);
		$this->setPaymentMethod($payment->payment_method);

		$merchant_email = $method->pay_to_email;
		if (empty($merchant_email)) {
			vmInfo (vmText::_ ('VMPAYMENT_SKRILL_MERCHANT_EMAIL_NOT_SET'));
			return FALSE;
		}
		$postVariables = array();
		$postVariables['email'] = $merchant_email;
		$postVariables['password'] = md5($method->skrill_general_api_password);
		$postVariables['transaction_id'] = $payment->order_number;
		$postVariables['mb_transaction_id'] = $payment->mb_transaction_id;
		$postVariables['amount'] = $payment->mb_amount;
		$postVariables['refund_status_url'] = JURI::root () .
			                        'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&lang='.vRequest::getCmd('lang','')
			                        . '&action=refund';

		if ($oldOrderStatus == 'C' && $order->order_status == 'R')
		{
			$result = SkrillPaymentCore::doRefund($postVariables);

			if ($result === 'CANNOT_LOGIN') {
				vmError (vmText::_ ('ERROR_UPDATE_MQI_BACKEND'));
				return FALSE;
		    } elseif ($result === 'ACCOUNT_LOCKED') {
		    	vmError (vmText::_ ('ERROR_UPDATE_LOCKED_BACKEND'));
				return FALSE;
		    } elseif ($result === 'GENERAL_ERROR') {
		        vmError (vmText::_ ('ERROR_GENERAL_REFUND_PAYMENT'));
				return FALSE;
		    } elseif ($result->error) {
		        vmError (vmText::_ ('ERROR_GENERAL_REFUND_PAYMENT'));
				return FALSE;
		    } else {
				return TRUE;
			}
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * process Payment Refund Notification Status URL
	 *
	 * @return  boolean|null
	 * @since   1.0.0
	 */
	public function paymentrefundNotification() {

		if (!class_exists ('VirtueMartModelOrders')) {
			require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
		}

		$statusURL = vRequest::getRequest();

		$this->logInfo ('paymentrefundNotification response from status URL: ' . print_r($statusURL, true), 'message');

		if (!isset($statusURL['transaction_id'])) {
			return;
		}

		$orderNumber = $statusURL['transaction_id'];
		$this->logInfo ('paymentrefundNotification order number: ' . $orderNumber, 'message');
		
		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($statusURL['transaction_id']))) {
			return;
		}
		$this->logInfo ('paymentrefundNotification virtuemart order id: ' . $virtuemart_order_id, 'message');

		if (!($payment = $this->getDataByOrderId ($virtuemart_order_id))) {
			return;
		}
		// $this->logInfo ('paymentrefundNotification payment: ' . print_r($payment, true), 'message');

		$method = $this->getVmPluginMethod ($payment->virtuemart_paymentmethod_id);
		// $this->logInfo ('paymentrefundNotification method: ' . print_r($method, true), 'message');
		
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}

		if (!$payment) {
			$this->logInfo ('getDataByOrderId payment not found: exit ', 'ERROR');
			return NULL;
		}

		$this->updateDeleteOldOrderStatus($payment->virtuemart_order_id, $method->status_refunded);

		$modelOrder = VmModel::getModel ('orders');
		$vmorder = $modelOrder->getOrder ($virtuemart_order_id);
		$order = array();

		$error_msg = $this->_processStatus ($statusURL, $method);
		$this->logInfo ('paymentrefundNotification error message: ' . $error, 'message');

		if ($error_msg) {
			$order['customer_notified'] = 0;
			$order['order_status'] = $method->status_canceled;
			$order['comments'] = 'process Refund ' . $error_msg;
			$modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);
			$this->logInfo ('process Refund ' . $error_msg, 'ERROR');
		} else {
			$this->logInfo ('process Refund OK', 'message');
		}

		$order['customer_notified'] = 1;

		if (strcmp ($statusURL['payment_status'], 'Completed') == 0) {
			$order['order_status'] = $method->status_refunded;
			$order['comments'] = vmText::sprintf ('VMPAYMENT_SKRILL_PAYMENT_STATUS_REFUNDED', $orderNumber);
		} elseif (strcmp ($statusURL['payment_status'], 'Pending') == 0) {
			$order['comments'] = vmText::sprintf ('VMPAYMENT_SKRILL_PAYMENT_STATUS_PENDING', $orderNumber);
			$order['order_status'] = $method->status_pending;
		}
		else {
			$order['order_status'] = $method->status_canceled;
		}

		$this->logInfo ('plgVmOnPaymentNotification return new_status:' . $order['order_status'], 'message');

		$modelOrder->updateStatusForOneOrder($virtuemart_order_id, $order, TRUE);
		exit;
	}

	/**
	 * Remove the old order status
	 *
	 * @param   int $orderId	order number
	 * @param   string $historyStatus history status
	 * @return  void
	 * @since   1.0.0
	 */
	protected function updateDeleteOldOrderStatus($orderId, $historyStatus)
	{
		$db = JFactory::getDBO();
		$query = "DELETE from `#__virtuemart_order_histories`";
		$query .= " WHERE virtuemart_order_id = '" . $orderId . "' and order_status_code = '". $historyStatus ."'";
		$this->logInfo ('updateDeleteOldOrderStatus query:' . $query, 'message');
		$db->setQuery($query);
		$db->execute();
	}
	
	/**
	 * Save updated orderline data to the method specific table
	 *
	 * @param array $_formData Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.

	public function plgVmOnUpdateOrderLine(  $_formData) {
	return null;
	}
	 */
	/**
	 * plgVmOnEditOrderLineBE
	 * This method is fired when editing the order line details in the backend.
	 * It can be used to add line specific package codes
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise

	public function plgVmOnEditOrderLineBE(  $_orderId, $_lineId) {
	return null;
	}
	 */

	/**
	 * This method is fired when showing the order details in the frontend, for every orderline.
	 * It can be used to display line specific package codes, e.g. with a link to external tracking and
	 * tracing systems
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise

	public function plgVmOnShowOrderLineFE(  $_orderId, $_lineId) {
	return null;
	}
	 */
	function plgVmDeclarePluginParamsPaymentVM3( &$data) {
		return $this->declarePluginParams('payment', $data);
	}

	function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {

		return $this->setOnTablePluginParams ($name, $id, $table);
	}

} // end of class plgVmpaymentSkrill

// No closing tag
