<?php

defined ('_JEXEC') or die();

/**
 * @version 2.4.3
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2016 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */
if (!class_exists ('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined ('VMPATH_ADMIN') or define ('VMPATH_ADMIN', VMPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart' );
defined ('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', VMPATH_ADMIN);

class plgVmpaymentAlatak_cybermut extends vmPSPlugin {

	// instance of class

	function __construct (& $subject, $config) {

		//if (self::$_this)
		//   return self::$_this;
		parent::__construct ($subject, $config);

		$this->_loggable = TRUE;
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$this->_tablepkey = 'id'; //virtuemart_cybermut_id';
		$this->_tableId = 'id'; //'virtuemart_cybermut_id';

		$varsToPush = $this->getVarsToPush ();

		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);

		//self::$_this = $this;
	}

	protected function getVmPluginCreateTableSQL () {

		return $this->createTableSQL ('Payment CyberMUT Table');
	}

	function getTableSQLFields () {

		$SQLfields = array(
			'id'                              => 'int(1) unsigned NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id'             => 'int(11) UNSIGNED',
			'order_number'                    => 'char(32)',
			'virtuemart_paymentmethod_id'     => 'mediumint(1) UNSIGNED',
			'payment_name'                    => 'varchar(5000)',
			'payment_order_total'             => 'decimal(15,5)',
			'payment_currency'                => 'char(3)',
			'cost_per_transaction'            => 'decimal(10,2)',
			'cost_percent_total'              => 'decimal(10,2)',
			'tax_id'                          => 'smallint(1)',
			'cybermut_custom'                 => 'varchar(255)  ',
			'cybermut_response_code-retour'   => 'char(15)',
			'cybermut_response_cvx'           => 'char(10)',
			'cybermut_response_reference'     => 'char(32)',
			'cybermut_response_montant'       => 'char(32)',
			'cybermut_response_date'          => 'char(32)',
			'cybermut_response_vld'           => 'char(20)',
			'cybermut_response_brand'         => 'char(20)',
			'cybermut_response_status3ds'     => 'smallint(1) ',
			'cybermut_response_numauto'       => 'char(50)',
			'cybermut_response_motifrefus'    => 'char(50)',
			'cybermut_response_originecb'     => 'char(10) ',
			'cybermut_response_bincb'         => 'char(10)',
			'cybermut_response_hpancb'        => 'char(128)',
			'cybermut_response_ipclient'      => 'char(64)',
			'cybermut_response_origintr'      => 'char(3)',
			'cybermut_response_veres'         => 'char(20)',
			'cybermut_response_pares'         => 'char(128)',
			'cybermut_response_montantech'    => 'char(20)',
			'cybermut_response_filtragecause' => 'smallint(1)',
			'cybermut_response_filtragevaleur'=> 'char(20)',
			'cybermutresponse_raw'            => 'text'
		);
		return $SQLfields;
	}

	function plgVmConfirmedOrder ($cart, $order) {

		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}
		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		$this->_debug = $method->debug;
		
		$this->logInfo ('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}
		if (!class_exists ('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');
		}

		//$usr = & JFactory::getUser();
		$new_status = '';

		$usrBT = $order['details']['BT'];
		$address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);

		$vendorModel = new VirtueMartModelVendor();
		$vendorModel->setId (1);
		$vendor = $vendorModel->getVendor ();
		$this->getPaymentCurrency ($method);
		$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
		$db = JFactory::getDBO ();
		$db->setQuery ($q);
		$currency_code_3 = $db->loadResult ();

		$paymentCurrency = CurrencyDisplay::getInstance ($method->payment_currency);
		$totalInPaymentCurrency = round ($paymentCurrency->convertCurrencyTo ($method->payment_currency, $order['details']['BT']->order_total, FALSE), 2);
		$cd = CurrencyDisplay::getInstance ($cart->pricesCurrency);
		if (!class_exists ('cybermut_api')) {
			require(JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'alatak_cybermut' . DS . 'alatak_cybermut' . DS . 'cybermut_api.php');
		}

		$tag = $this->getLang ();
		
		//FIX PORTAIL PAIEMENT BRUNO CAMELEON
		if($tag != 'FR') {
			$tag='EN';
		}

		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		$description = ""; // ???
		$cybermut_api = new cybermut_api();
		$fields = array(
			'version'       => $cybermut_api->getVersion (),
			'TPE'           => $method->tpe,
			'date'          => date ('d/m/Y:H:i:s'),
			'montant'       => round ($order['details']['BT']->order_total, 2) . $currency_code_3,
			'reference'     => $order['details']['BT']->order_number,
			'mail'          => $order['details']['BT']->email,
			'texte-libre'   => $order['details']['BT']->virtuemart_paymentmethod_id,
			'lgue'          => $tag,
			'societe'       => $method->societe,
			'url_retour'    => $this->_get_url_retour ($order['details']['BT']->virtuemart_paymentmethod_id,$order['details']['BT']->order_number),
			'url_retour_ok' => $this->_get_url_retour_ok ($order['details']['BT']->virtuemart_paymentmethod_id, $order['details']['BT']->order_number),
			'url_retour_err'=> $this->_get_url_retour_err ($order['details']['BT']->virtuemart_paymentmethod_id,$order['details']['BT']->order_number),
			'bouton'        => 'ButtonLabel'
		);
		//$fields['montant'] = sprintf('%.2f', $order['details']['BT']->order_total) . $method->payment_currency;
		$fields['MAC'] = cybermut_api::getMAC ($fields, $method->cle);

		// Prepare data that should be stored in the database
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['payment_name'] = $this->renderPluginName ($method, $order);
		$dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
		$dbValues['cybermut_custom'] = $return_context;
		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$dbValues['payment_currency'] = $method->payment_currency;
		$dbValues['payment_order_total'] = $totalInPaymentCurrency;
		$dbValues['tax_id'] = $method->tax_id;
		$this->storePSPluginInternalData ($dbValues);

		$cmcic_url = $cybermut_api->getCmcicUrl ($method);

		// add spin image
		$html = '<html><head><title>Redirection</title></head><body><div style="margin: auto; text-align: center;">';
		$html .= '<form action="' . $cmcic_url . '" method="post" name="vm_cybermut_form" >';
		$html .= '<input type="submit"  value="' . JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_REDIRECT_MESSAGE') . '" />';
		foreach ($fields as $name => $value) {
			$html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars ($value) . '" />';
		}
		$html .= '</form></div>';
		$html .= '<script type="text/javascript">';
		$html .= 'document.vm_cybermut_form.submit();';
		$html .= '</script></body></html>';

		// 	2 = don't delete the cart, don't send email and don't redirect
		return $this->processConfirmedOrderPaymentResponse (2, $cart, $order, $html, $dbValues['payment_name'], $new_status);
	}

	function plgVmgetPaymentCurrency ($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}
		$this->getPaymentCurrency ($method);
		$paymentCurrencyId = $method->payment_currency;
	}

	function plgVmOnPaymentResponseReceived (&$html) {

		$virtuemart_paymentmethod_id = JRequest::getVar ('pm');
		$order_number = JRequest::getVar ('on');

		$vendorId = 1;
		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return NULL;
		}

		if (!class_exists ('cybermut_api')) {
				require(JPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'alatak_cybermut' . DS . 'alatak_cybermut' . DS . 'cybermut_api.php');
		}
		if (!class_exists('VirtueMartCart')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		}
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}

		if (!class_exists('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}

		VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		

		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number))) {
			return NULL;
		}
		if (!($payments = $this->getDatasByOrderNumber($order_number))) {
			return '';
		}
		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($virtuemart_order_id);

		/*
		VmConfig::loadJLang('com_virtuemart', TRUE, $order['details']['BT']->order_language);
		VmConfig::loadJLang('com_virtuemart_shoppers', TRUE, $order['details']['BT']->order_language);
		VmConfig::loadJLang('com_virtuemart_orders', TRUE, $order['details']['BT']->order_language);
		VmConfig::setdbLanguageTag($order['details']['BT']->order_language);

		mail('bruno@cameleons.com', 'MAIL PAYMENT', $order['details']['BT']->order_language );
		*/

		if (!class_exists('CurrencyDisplay'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance('', $order['details']['BT']->virtuemart_vendor_id);
		$amountInCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $order['details']['BT']->order_currency);
		$cart = VirtueMartCart::getCart();
		$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
		//BRUNO CAMELEONS : Changement ID Order
		$html = $this->_getCybermutPaymentResponseHtml ($order['details']['BT']->virtuemart_order_id, $order['details']['BT']->order_pass,  $amountInCurrency['display']);

		if (!class_exists ('VirtueMartCart')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		}

		$cart = VirtueMartCart::getCart ();
		$cart->emptyCart ();

		return TRUE;
	}

	function plgVmOnUserPaymentCancel () {

		vmDebug ('plgVmOnPaymentResponseReceived', JRequest::get ('post'));
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}

		$vendorId = 0;
		$virtuemart_paymentmethod_id = JRequest::getVar ('pm');
		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return NULL;
		}
		if (JRequest::getVar ('TPE') != $method->tpe) {
			return NULL;
		}
		if (!$this->selectedThisByMethodId ($virtuemart_paymentmethod_id)) {
			return NULL;
		}
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}
		$order_number = JRequest::getVar ('on');

		vmInfo  ('VMPAYMENT_ALATAK_CYBERMUT_PAYMENT_CANCELLED_OR_REFUSED');

		return TRUE;
	}

	/*
		 *   plgVmOnPaymentNotification() - This event is fired by Offline Payment. It can be used to validate the payment data as entered by the user.
		 * Return:
		 * Parameters:
		 *  None
		 *  @author Valerie Isaksen
		 */

	 function plgVmOnPaymentNotification ( ) {
		$CMCIC_bruteVars = $_REQUEST;

		if (!isset($CMCIC_bruteVars['reference'])) {
			return;
		}
		 $virtuemart_paymentmethod_id = $CMCIC_bruteVars['texte-libre'];
		 $order_number = $CMCIC_bruteVars['reference'];

		$this->_currentMethod = $this->getVmPluginMethod($virtuemart_paymentmethod_id,false);
		if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
			return FALSE;
		}
		 if (!($payments = $this->getDatasByOrderNumber($order_number))) {
			 return FALSE;
		 }
		 if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number))) {
			 return FALSE;
		 }


		$CMCIC_Tpe = array("tpe" =>$this->_currentMethod->tpe, "soc" => $this->_currentMethod->societe, "key" => $this->_currentMethod->cle);
		 $accuseReception='';
		 $validHmac = $this->isValidHmac ($CMCIC_Tpe, $CMCIC_bruteVars, $accuseReception);
		 if (!$validHmac) {
			 return;
		 }
		 echo $accuseReception;
		$codeRetour = array('payetest',
			'paiement',
			'paiement_pf2',
			'paiement_pf3',
			'paiement_pf4',
			'annulation',
			'annulation_pf2',
			'annulation_pf3',
			'annulation_pf4');

		 $orderModel = VmModel::getModel('orders');
		 $order = $orderModel->getOrder($virtuemart_order_id);

			if (!($cyberMutInternalDatas = $this->getCyberMutInternalDatas ($order_number))) {
			return NULL;
		}

		$cybermut_data = $CMCIC_bruteVars;
		$code_retour =   $CMCIC_bruteVars['code-retour'] ;
		$new_status = "";
		$comment = "";
		if (!$this->getPaymentStatus ($code_retour, $new_status, $comment)) {
			return NULL;
		}

		// get all know columns of the table
		$db = JFactory::getDBO ();
		 $query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';
		$db->setQuery ($query);
		$columns = $db->loadColumn (0);
		foreach ($cybermut_data as $key => $value) {
			$table_key = 'cybermut_response_' . $key;
			if (in_array ($table_key, $columns)) {
				if ($table_key=='cybermut_response_hpancb') {
					continue;
				}
				$response_fields[$table_key] = $value;
			}
		}


		$response_fields['payment_name'] = $this->rendeCyberMutName ();
		$response_fields['cybermutresponse_raw'] =json_encode($cybermut_data);
		$return_context = $payments[0]->cybermut_custom;
		$response_fields['order_number'] = $order_number;
		$response_fields['virtuemart_order_id'] = $virtuemart_order_id;
		$response_fields['virtuemart_paymentmethod_id'] = $virtuemart_paymentmethod_id;
		//$preload=true   preload the data here too preserve not updated data
		 $this->storePSPluginInternalData($response_fields);

		// send the email only if payment has been accepted
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' . DS . 'orders.php');
		}

		$modelOrder = new VirtueMartModelOrders();

		$order['order_status'] = $new_status;
		$order['virtuemart_order_id'] = $virtuemart_order_id;
		$order['customer_notified'] = 1;
		$order['comments'] = $comment;

		if (!class_exists ('JComponentHelper')) {
			require(JPATH_SITE . DS . 'libraries' . DS . 'joomla' . DS . 'application' . DS . 'component' . DS . 'helper.php');
		}
		$modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, FALSE);

		//Génération de fichier SAP par BRUNO CAMELEON
		if($code_retour == 'paiement' || $code_retour == 'payetest' ){
			include_once(JPATH_ROOT.DS.'sap_xml_handler.php');
			$SAP_handler = new Sap_xml_handler();
			$SAP_handler->create_SAP_XML($virtuemart_order_id);
		}

		return TRUE;
	}

	/**
	 * @param        $virtuemart_order_id
	 * @param string $order_number
	 * @return mixed|string
	 */
	 function getCyberMutInternalDatas ($order_number ) {

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE  `order_number` = "' . $order_number . '"';

		$db->setQuery ($q);
		if (!($payments = $db->loadObjectList ())) {
			return '';
		}
		return $payments;
	}


	 function rendeCyberMutName () {


		return 'CyberMut';
	}

	function _getTablepkeyValue ($virtuemart_order_id) {

		$db = JFactory::getDBO ();
		$q = 'SELECT ' . $this->_tablepkey . 'FROM `' . $this->_tablename . '` '
			. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
		$db->setQuery ($q);

		if (!($pkey = $db->loadResult ())) {
			JError::raiseWarning (500, $db->getErrorMsg ());
			return '';
		}
		return $pkey;
	}


	/**
	 * Display stored payment data for an order
	 *
	 * @see components/com_virtuemart/helpers/vmPSPlugin::plgVmOnShowOrderBEPayment()
	 */
	function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $payment_method_id) {

		if (!$this->selectedThisByMethodId ($payment_method_id)) {
			return NULL; // Another method was selected, do nothing
		}
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}
		$order_number=VirtueMartModelOrders::getOrderNumber ($virtuemart_order_id);
		if (!($payments = $this->getCybermutInternalDatas ($order_number))) {
			return '';
		}

		$html = '<table class="adminlist table" width="50%">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$code = "cybermut_response_";
		$first = TRUE;
		foreach ($payments as $payment) {
			$html .= '<tr class="row1"><th>' . JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_DATE') . '</th><th align="left">' . $payment->created_on . '</th></tr>';
			// Now only the first entry has this data when creating the order
			if ($first) {
				$html .= $this->getHtmlRowBE ('ALATAK_CYBERMUT_PAYMENT_NAME', $payment->payment_name);
				// keep that test to have it backwards compatible. Old version was deleting that column  when receiving an IPN notification
				if ($payment->payment_order_total and  $payment->payment_order_total != 0.00) {
					$html .= $this->getHtmlRowBE ('ALATAK_CYBERMUT_PAYMENT_ORDER_TOTAL', $payment->payment_order_total . " " . shopFunctions::getCurrencyByID ($payment->payment_currency, 'currency_code_3'));
				}
				$first = FALSE;
			}
			foreach ($payment as $key => $value) {
				// only displays if there is a value or the value is different from 0.00 and the value
				if ($value) {
					if (substr ($key, 0, strlen ($code)) == $code) {
						$html .= $this->getHtmlRowBE ("ALATAK_" . $key, $value);
					}
				}
			}
		}
		$html .= '</table>' . "\n";
		return $html;

	}


	function _getCybermutPaymentResponseHtml ($order_number,$order_pass, $amountInCurrency) {

$html='
		<div class="response">'.
			  vmText::sprintf('VMPAYMENT_ALATAK_CYBERMUT_ORDER_DONE',   $order_number , $amountInCurrency)
.'</div>';
$order_link=JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $order_number . '&order_pass=' . $order_pass, false);

$html.='
<div class="vieworder">
	<a class="vm-button-correct" href="'.$order_link.'">'. vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER').'</a>
</div>';
		return $html;
	}

	function getCosts (VirtueMartCart $cart, $method, $cart_prices) {

		if (preg_match ('/%$/', $method->cost_percent_total)) {
			$cost_percent_total = substr ($method->cost_percent_total, 0, -1);
		} else {
			$cost_percent_total = $method->cost_percent_total;
		}
		return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
	}

	/**
	 * Check if the payment conditions are fulfilled for this payment method
	 *
	 * @author: Valerie Isaksen
	 *
	 * @param $cart_prices: cart prices
	 * @param $payment
	 * @return true: if the conditions are fulfilled, false otherwise
	 *
	 */
	protected function checkConditions ($cart, $method, $cart_prices) {

		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));

		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}
		// probably did not gave his BT:ST address
		if (!is_array ($address)) {
			$address = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		if (in_array ($address['virtuemart_country_id'], $countries) || count ($countries) == 0) {
			if ($amount_cond) {
				return TRUE;
			}
		}

		return FALSE;
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
	 * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
	 *
	 */
	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart) {

		return $this->OnSelectCheck ($cart);
	}

	/**
	 * plgVmDisplayListFEPayment
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
	 *
	 * @param object  $cart Cart object
	 * @param integer $selected ID of the method selected
	 * @return boolean True on succes, false on failures, null when this plugin was not selected.
	 * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
	 *
	 * @author Valerie Isaksen
	 * @author Max Milbers
	 */
	public function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		return $this->displayListFE ($cart, $selected, $htmlIn);
	}

	/*
		 * plgVmonSelectedCalculatePricePayment
		 * Calculate the price (value, tax_id) of the selected method
		 * It is called by the calculator
		 * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
		 * @author Valerie Isaksen
		 * @cart: VirtueMartCart the current cart
		 * @cart_prices: array the new cart prices
		 * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
		 *
		 *
		 */

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
	function plgVmOnCheckAutomaticSelectedPayment (VirtueMartCart $cart, array $cart_prices = array()) {

		return $this->onCheckAutomaticSelected ($cart, $cart_prices);
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
	 * @param array $_formData Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.

	public function plgVmOnUpdateOrderPayment(  $_formData) {
	return null;
	}
	 */
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
	function plgVmDeclarePluginParamsPayment ($name, $id, &$data) {

		return $this->declarePluginParams ('payment', $name, $id, $data);
	}

	function plgVmDeclarePluginParamsPaymentVM3(&$data) {
		return $this->declarePluginParams('payment', $data);
	}

	function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {

		return $this->setOnTablePluginParams ($name, $id, $table);
	}

	function _get_url_retour ($virtuemart_paymentmethod_id, $order_number) {

		return  JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=' . $virtuemart_paymentmethod_id.'&on='.$order_number ;
		//return $this->get_cmcic_url_retours ('alatak_cybermut_retour.php');
	}

	function _get_url_retour_ok ($virtuemart_paymentmethod_id, $order_number) {

		return JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=' . $virtuemart_paymentmethod_id .'&on='.$order_number ;
		//return $this->get_cmcic_url_retours ('alatak_cybermut_retour.php');
	}


	/**
	 * @param $virtuemart_paymentmethod_id
	 * @return The
	 */
	function _get_url_retour_err ($virtuemart_paymentmethod_id, $order_number) {

		return JURI::root () . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&pm=' . $virtuemart_paymentmethod_id.'&on='.$order_number  ;
		//return $this->get_cmcic_url_retours ('alatak_cybermut_retour_err.php');
	}


	function get_cmcic_url_retours ($url) {

		$lang = JFactory::getLanguage ();
		$tag = strtolower (substr ($lang->get ('tag'), 0, 2));

		$app = JFactory::getApplication ();
		$router = $app->getRouter ();
		$sef = ($router->getMode () == JROUTER_MODE_SEF) ? TRUE : FALSE;
		if ($sef) {
			return JURI::root () . $url . '/' . $tag;
		} else {
			return JURI::root () . $url . '?lang=' . $tag;
		}

	}

	/*
			  // function HtmlEncode
			  //
			  // IN:  chaine a encoder / String to encode
			  // OUT: Chaine encodée / Encoded string
			  //
			  // Description: Encode special characters under HTML format
			  //                           ********************
			  //              Encodage des caractères speciaux au format HTML
			 */

	function HtmlEncode ($data) {

		$SAFE_OUT_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890._-";
		$encoded_data = "";
		$result = "";
		for ($i = 0; $i < strlen ($data); $i++) {
			if (strchr ($SAFE_OUT_CHARS, $data{$i})) {
				$result .= $data{$i};
			} else {
				if (($var = bin2hex (substr ($data, $i, 1))) <= "7F") {
					$result .= "&#x" . $var . ";";
				} else {
					$result .= $data{$i};
				}
			}
		}
		return $result;
	}


	 private function getPaymentStatus ($code,  &$new_status, &$comment) {

		if ($this->_currentMethod->test_production == 'production' and  $code == 'payetest') {
			return FALSE;
		}
		switch (strtolower ($code)) {
			case 'payetest':
			case 'paiement':
				$new_status = $this->_currentMethod->order_success_status;
				$comment = JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_PAYMENT_CONFIRMED');
				//$comment = getLang();
				break;
			case 'paiement_pf2':
			case 'paiement_pf3':
			case 'paiement_pf4':
				$comment = JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_PAYMENT_CONFIRMED');
				//$comment = getLang();
				$new_status = $this->_currentMethod->order_success_echeance;
				break;
			case 'annulation':
				$comment = JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_PAYMENT_CANCELLED');
				$new_status = $this->_currentMethod->order_failure_status;
				break;
			case 'annulation_pf2':
			case 'annulation_pf3':
			case 'annulation_pf4':
				$comment = JText::_ ('VMPAYMENT_ALATAK_CYBERMUT_PAYMENT_CANCELLED');
				$new_status = $this->_currentMethod->order_failure_echeance;
				break;
		}
		return TRUE;
	}

	protected function getLang () {

		$language =JFactory::getLanguage ();
		$tag = strtolower (substr ($language->get ('tag'), 0, 2));
		return strtoupper ($tag);

	}

	// ----------------------------------------------------------------------------
// function TesterHmac
//
// IN: Paramètres du Tpe / Tpe parameters
//     Champs du formulaire / Form fields
// OUT: Résultat vérification / Verification result
// description: Vérifier le MAC et préparer la Reponse
//              Perform MAC verification and create Receipt
// ----------------------------------------------------------------------------
	function isValidHmac ($CMCIC_Tpe, $CMCIC_bruteVars, &$receipt ) {
		if (!class_exists ('cybermut_api')) {
			require(JPATH_SITE . DS.'plugins'.DS.'vmpayment'.DS.'alatak_cybermut'.DS.'alatak_cybermut'.DS.'cybermut_api.php');
		}
		$correctMAC = cybermut_api::handleResponseMAC ($CMCIC_bruteVars, $CMCIC_Tpe['key']);

		$returnedMAC = $CMCIC_bruteVars['MAC'];

		if ($returnedMAC != $correctMAC) {
			$receipt = cybermut_api::getNackResponse ();

			return FALSE;
		} else {
			$receipt = cybermut_api::getAckResponse ();

			return TRUE;
		}

	}

}

// No closing tag
