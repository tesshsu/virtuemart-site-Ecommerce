<?php

if (!defined ('_JEXEC')) {
	die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');
}

/**
 * @version $Id$
 * @package VirtueMart
 * @subpackage Plugins -vmuserfield - istraxx_euvatchecker - 1.2.6
 * @author Max Milbers,Valérie Isaksen
 * @copyright  Copyright (C) 2012- 2015 iStraxx UG (haftungsbeschränkt). All rights reserved
 * @license istraxx_license.txt Proprietary License. This code belongs to iStraxx UG (haftungsbeschränkt) 
 * You are not allowed to distribute or sell this code. You bought only a license to use it for ONE virtuemart installation. 
 * You are not allowed to modify the core, but you may modify the layout
 */

if (!class_exists ('vmUserfieldPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmuserfieldtypeplugin.php');
}

class plgVmUserfieldIstraxx_euvatchecker extends vmUserfieldPlugin {

	var $varsToPush = array(
		'shoppergroup_vat'   	=> array(0, 'int'),
		'shoppergroup_nonvat'	=> array(0, 'int'),
		'country_consistency'	=> array(0, 'int'),
		'requiredForEurope' 	=> array(0, 'int'),
		'preferBT' 				=> array(0, 'int'),
		'keepTINValidDays'		=> array(1, 'int')
	);

	private $toAdd = null;
	private $toRemove = null;
	private $userId = null;

	function __construct (& $subject, $config) {

		parent::__construct ($subject, $config);

		$this->_loggable = TRUE;
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$this->setConfigParameterable ('params', $this->varsToPush);
		} else {
			$this->setConfigParameterable ('userfield_params', $this->varsToPush);
		}

		$this->_userFieldName = 'eu_vat_id';
	}

	/**
	 * @return string
	 */
	public function getVmPluginCreateTableSQL () {

		$db = JFactory::getDBO();
		$query = 'SHOW TABLES LIKE "%' . str_replace('#__', '', $this->_tablename) . '"';
		$db->setQuery($query);
		$result = $db->loadResult();
		$app = JFactory::getApplication();
		$tablesFields = 0;
		if ($result) {
			$SQLfields = $this->getTableSQLFields();
			$loggablefields = $this->getTableSQLLoggablefields();
			$tablesFields = array_merge($SQLfields, $loggablefields);
			$update[$this->_tablename] = array($tablesFields, array(), array());
			vmdebug(get_class($this) . ':: VirtueMart2 update ' . $this->_tablename);
			if (!class_exists('GenericTableUpdater'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
			$updater = new GenericTableUpdater();
			$updater->updateMyVmTables($update);
			//	return FALSE;   //TODO enable this, when using vm version higher than 2.0.8F
		} else {
			return $this->createTableSQL ('Userfield iStraxx EUvatcker Table',$tablesFields);
		}

	}

	/**
	 * @return array
	 */
	function getTableSQLFields () {

		$SQLfields = array(
			'id'                                                       => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_user_id'                                       => 'int(1) UNSIGNED',
			'euvat_countryCode'                                        => 'char(2)',
			'euvat_vatNumber'                                          => 'varchar(20)',
			'euvat_valid'                                              => 'char(10)',
			'euvat_name'                                               => 'char(128)',
			'euvat_address'                                            => 'char(255)',
			'euvat_traderName'                                         => 'char(128)',
			'euvat_traderCompanyType'                                  => 'char(128)',
			'euvat_traderAddress'                                      => 'char(255)',
			'euvat_traderPostcode'                                     => 'char(64)',
			'euvat_traderCity'                                         => 'char(128)',
			'euvat_requestDate'                                        => 'char(64)',
			'euvat_requestIdentifier'                                  => 'char(128)',
			'euvat_faultstring'                                        => 'char(255)',
			'reason'                                                   => 'char(255)',
			'shoppergroup_remove'                                      => 'smallint(1)',
			'shoppergroup_added'                                       => 'smallint(1)',
			'manually_validated'                                      => 'smallint(1)',
		);
		return $SQLfields;
	}

	function plgVmDeclarePluginParamsUserfield ($type, $name, $id, &$data) {

		return $this->declarePluginParams ($type, $name, $id, $data);
	}

	function plgVmDeclarePluginParamsUserfieldVM3(&$data){
		return $this->declarePluginParams('userfield', $data);
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPluginTable ($jplugin_name) {

		return $this->onStoreInstallPluginTable ($jplugin_name);
	}

	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the shipment-specific data.
	 *
	 * @param integer $order_number The order Number
	 * @return mixed Null for shipments that aren't active, text (HTML) otherwise
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 */

	public function plgVmOnUserfieldDisplay ($_prefix, $field, $userId, &$return) {

		if ('plugin' . $this->_name != $field->type) {
			return;
		}

		$session = JFactory::getSession ();
		$session->set ('vm_shoppergroups_set.' . $userId, FALSE, 'vm');

		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$this->AddUserfieldParameter ($field->params);
		} else {

			$this->AddUserfieldParameter ($field->userfield_params);
			//$this->setConfigParameterable ('userfield_params', $this->varsToPush);
		}


		if($this->requiredForEurope){

			$required = '';

			$inEurope = $session->get ('vm_euvatid_inEuropa', TRUE, 'vm');

			if($inEurope){
				$required = ' class="required"';
				$return['fields'][$field->name]['required'] = 1;
			}
		} else {
			$required = $field->required ? ' class="required"' : '';
		}

		$return['fields'][$field->name]['formcode'] = '<input type="text" id="'
			. $_prefix . $field->type . '_field" name="' . $_prefix . $field->name . '" size="' . $field->size
			. '" value="' . $return['fields'][$field->name]['value'] . '" '
			//. ($field->required ? ' class="required"' : '')
			. $required
			. ($field->maxlength ? ' maxlength="' . $field->maxlength . '"' : '')
			. ($field->readonly ? ' readonly="readonly"' : '') . ' /> ';

		$isBE = !JFactory::getApplication ()->isSite ();
		if ($isBE) {
			$return['fields'][$field->name]['formcode'] .= $this->onShowUserDisplayBEUserfield ($userId, $return['fields'][$field->name]['value'], $field->name);
		}

	}

	public function plgVmPrepareUserfieldDataSave ($fieldType, $fieldName, $post, &$value, $params) {

		if ('plugin' . $this->_name != $fieldType) {
			return;
		}
		if(!empty($post['virtuemart_user_id'])){
			$this->userId = (int)$post['virtuemart_user_id'];
		} else {
			$this->userId = JFactory::getUser()->id;
		}

		$session = JFactory::getSession ();
		$session->set ('vm_shoppergroups_set', FALSE, 'vm');

		$this->AddUserfieldParameter ($params);
		$this->validateEuVat ((int)$post['virtuemart_country_id'], (int)$post['virtuemart_state_id'], $post['company'], $this->userId, $value, TRUE);

		vmdebug('plgVmPrepareUserfieldDataSave',$this->toAdd,$this->toRemove,$this->userId);
		$this->pushIntoGroups ($this->toAdd, $this->toRemove, $this->userId);
	}

	function plgVmInitialise(){
		$this->plgVmOnMainController();
	}


	function plgVmOnMainController () {

		static $executed = false;

		if($executed) return; else $executed = true;


		$session = JFactory::getSession ();

		$this->AddUserfieldParameterByPlgName ($this->_name);

		if (!class_exists ('VirtueMartCart')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		}
		$cart = VirtueMartCart::getCart ();

		if(empty($cart->BT)) {
			if(!class_exists('geoHelper')) {
				$geoLocatorPath = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php';
				if(file_exists( $geoLocatorPath )) {
					require($geoLocatorPath);
				}
			}

			if(class_exists('geoHelper')) {
				$virtuemart_country_code = geoHelper::getCountry2Code();
				if($virtuemart_country_code) {
					$countryM = VmModel::getModel( 'country' );
					$country = (array)$countryM->getCountryByCode( $virtuemart_country_code );
					vmdebug( 'plgVmOnMainController my country ', $country );
					if(!empty($country['virtuemart_country_id'])) {
						if(!is_array( $cart->BT )) $cart->BT = array();
						if(!is_array( $cart->ST )) $cart->ST = array();
						$virtuemart_country_id = $country['virtuemart_country_id'];
						$cart->BT['virtuemart_country_id'] = $virtuemart_country_id;
						$cart->ST['virtuemart_country_id'] = $virtuemart_country_id;
						$cart->setCartIntoSession(false,true);
						vmdebug('validateEuVat $virtuemart_country_id by geolocator '.$virtuemart_country_id);
						$c = $session->set('geolocator_country', $virtuemart_country_id,'vm');
						//mail('bruno@cameleons.com','IP seen',$virtuemart_country_id);
					}
				}
			} else {
				vmWarn('Geolocator component not found in '.$geoLocatorPath);
			}
		}

		if(vRequest::getCmd('updatecart',false)){
			$STsameAsBT = vRequest::getInt('STsameAsBT', vRequest::getInt('STsameAsBTjs',false));
			if($STsameAsBT){
				$cart->STsameAsBT = $STsameAsBT;
			}
			$cart->saveCartFieldsInCart();
		}

		if (empty($this->shoppergroup_vat) and empty($this->shoppergroup_nonvat)) {
			vmdebug('plgVmOnMainController both shoppergroups are not set');
			return FALSE;
		}

		//if(empty($userId) and !empty($cart->BT['virtuemart_country_id'])){
		//Maybe the data is just coming from a form, so we must check if we must take it from the form or the cart
		//dirty fix which should be avoided, but cant because maincontroller is called before the store commands
		$company = vRequest::getString('company',0);
		if($company == 0 and isset($cart->BT['company'])){
			$company = $cart->BT['company'];
		}

		//$virtuemart_country_id = vRequest::getInt('virtuemart_country_id',0);
		//$virtuemart_state_id = vRequest::getInt('virtuemart_state_id',0);
		if($this->preferBT or $cart->STsameAsBT){
			$prio = $cart->BT;
			$secundo = $cart->ST;
		} else {
			$prio = $cart->ST;
			$secundo = $cart->BT;
		}

		$virtuemart_country_id = 0;
		$virtuemart_state_id = 0;
		//vmdebug('plgVmOnMainController  (int)$prio[virtuemart_country_id] ',$prio,$secundo);

		if(!empty($prio['virtuemart_country_id'])){
			$virtuemart_country_id = (int)$prio['virtuemart_country_id'];
			if(isset($prio['virtuemart_state_id'])){
				$virtuemart_state_id = (int)$prio['virtuemart_state_id'];
			} else {
				$virtuemart_state_id = 0;
			}

		} else if(!empty($secundo['virtuemart_country_id'])){
			$virtuemart_country_id = (int)$secundo['virtuemart_country_id'];
			if(isset($prio['virtuemart_state_id'])){
				$virtuemart_state_id = (int)$secundo['virtuemart_state_id'];
			} else {
				$virtuemart_state_id = 0;
			}
		}


		$vat_number = vRequest::getString($this->_userFieldName,0);
		if ($vat_number == 0){
			if( isset($cart->BT[$this->_userFieldName])) {
				$vat_number = $cart->BT[$this->_userFieldName];
			}
		}

		$this->userId = JFactory::getUser ()->id;
		$hash = $this->userId.$virtuemart_country_id.$vat_number.$cart->STsameAsBT;
		$alreadyValidated = $session->get ('vm_eu_vat_validated.' . $hash, FALSE, 'vm');
		vmdebug('plgVmOnMainController $alreadyValidated '.$hash,$alreadyValidated);
		if(!$alreadyValidated){

			$this->validateEuVat ($virtuemart_country_id, $virtuemart_state_id,$company, $this->userId, $vat_number,false,$hash);
			vmdebug('validateEuVat ',$virtuemart_country_id, $company, $this->userId, $vat_number);
			$session->set ('istraxx_euvat_sgp_add.'.$hash, $this->toAdd, 'vm');
			$session->set ('istraxx_euvat_sgp_remove.'.$hash, $this->toRemove, 'vm');
			$session->set ('vm_eu_vat_validated.' . $hash, TRUE, 'vm');
			vmdebug('plgVmOnMainController to add, to remove',$this->toAdd, $this->toRemove);
			$this->pushIntoGroups ($this->toAdd, $this->toRemove, $this->userId, true);
		} else {
			$this->toAdd = $session->get ('istraxx_euvat_sgp_add.'.$hash, $this->toAdd, 'vm');
			$this->toRemove = $session->get ('istraxx_euvat_sgp_remove.'.$hash, $this->toRemove, 'vm');
			vmdebug('plgVmOnMainController to add, to remove',$this->toAdd, $this->toRemove);
			$this->pushIntoGroups ($this->toAdd, $this->toRemove, $this->userId, false);
		}



	}

	static $european_countries_2_code = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'MC', 'GB', 'GR', 'HU','HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');

	function validateEuVat ($virtuemart_country_id, $virtuemart_state_id,$company, $userId, $vat_number, $store,$hash=0) {

		$taxfree = TRUE;

		if (is_numeric ($virtuemart_country_id)) {
			$country_2_code = ShopFunctions::getCountryByID ($virtuemart_country_id, 'country_2_code');
		} else {
			$country_2_code = $virtuemart_country_id;
			$virtuemart_country_id = ShopFunctions::getCountryIDByName ($virtuemart_country_id);
		}
		//vmdebug('validateEuVat',$country_2_code,$virtuemart_country_id);
		if (empty($country_2_code)) {
			$taxfree = FALSE;
			$inEuropa = FALSE;
		} else {
			// Check if usercountry are in europa
			$inEuropa = in_array ($country_2_code, self::$european_countries_2_code);

			//citizen in states not paying tax are http://en.wikipedia.org/wiki/Special_member_state_territories_and_the_European_Union
			//Check for states
			if($inEuropa){

				if( $country_2_code=='CY' ){
					//Northern Cyprus
				//} else if( $country_2_code=='DK' ){		//they all are listed as own countries, so no action here
					//Greenland, Faroe Islands
				} else if( $country_2_code=='FI' ){
					//Åland Islands
				//} else if( $country_2_code=='FR' ){	//they all are listed as own countries, so no action here
					//French Guiana
					//Guadeloupe , Martinique, Réunion, Mayotte, Saint Martin, Saint Barthélemy,
					//Saint Pierre and Miquelon,Wallis and Futuna,French Polynesia, New Caledonia
					// "French Southern and Antarctic Lands"
				} else if( $country_2_code=='DE' ){
					//Helgoland
					//Büsingen am Hochrhein
				} else if( $country_2_code=='GR' ){
					//Mount Athos
				} else if( $country_2_code=='IT' ){
					// Campione d'Italia and Livigno

				//} else if( $country_2_code=='NL' ){ 	//they all are listed as own countries, so no action here
					//Bonaire, Saba, Curaçao, seem not listed in vm, this are treat as own countries Sint Maarten, Aruba, Sint Eustatius,
				} else if( $country_2_code=='ES' ){
					//Canary Islands, Ceuta, Melilla

				//} else if( $country_2_code=='UK' ){ //they all are listed as own countries, so no action here
					//Gibraltar, Saint Helena, "Ascension and Tristan da Cunha", Falkland Islands	Minimal
					//"South Georgia and the South Sandwich Islands", "British Antarctic Territory", Bermuda
					//"Cayman Islands", Anguilla, Montserrat, "British Virgin Islands", "Turks and Caicos Islands", "Pitcairn Islands"
					// Jersey, Guernsey
				}
			}
		}

		$euvat_result = array();

		$session = JFactory::getSession ();
		$session->set ('vm_euvatid_inEuropa', $inEuropa, 'vm');

		$app = JFactory::getApplication();

		$forceValidation = vRequest::getBool('euvatid_forceValidation',FALSE);

		$dbValues = array();

		$manually_validated = 0;
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			if($app->isAdmin()){
				$manually_validated = vRequest::getInt('manually_validated',false);
			}
		} else {
			if(vmAccess::manager('user.edit')){
				$manually_validated = vRequest::getInt('manually_validated',false);
			}
		}

		if(!empty($userId)){
			$euvatdata = $this->_getEuVatInternalData ($userId);
			if($euvatdata){
				$dbValues = get_object_vars($euvatdata[0]);
				vmdebug('_getEuVatInternalData ',$dbValues);
			}
		}


		if(!defined('VM_VERSION') or VM_VERSION < 3){
			if($app->isAdmin() and $manually_validated!==false){
				$dbValues['manually_validated'] = (int)$manually_validated;
			} else if(!isset($dbValues['manually_validated'])){
				$dbValues['manually_validated'] = (int)$manually_validated;
			}
		} else {
			if(vmAccess::manager('user.edit') and $manually_validated!==false){
				$dbValues['manually_validated'] = (int)$manually_validated;
			} else if(!isset($dbValues['manually_validated'])){
				$dbValues['manually_validated'] = (int)$manually_validated;
				vmdebug('manually_validated not isset ',$dbValues['manually_validated']);
			}
		}

		vmdebug('_getEuVatInternalData ',$dbValues['manually_validated']);
		if((int)$dbValues['manually_validated']==-1){
			//The user should not be touched.
			$this->toAdd = array();
			$this->toRemove = array();// $this->shoppergroup_vat;
			//The session still keeps the shoppergroup for anonymous people, we need to remove it
			$session->set('vm_shoppergroups_add',array(),'vm');
			$session->set('vm_shoppergroups_remove',array(),'vm');

			$session->set ('istraxx_euvat_sgp_add.'.$hash, $this->toAdd, 'vm');
			$session->set ('istraxx_euvat_sgp_remove.'.$hash, $this->toRemove, 'vm');
			$session->get ('vm_eu_vat_validated.' . $hash, true, 'vm');
			session_write_close();
			session_start();
			$dbValues['shoppergroup_remove'] = 0;
			$dbValues['shoppergroup_added'] = 0;
			$dbValues['euvat_vatNumber'] = $vat_number;
			vmdebug('I do manual and return',$dbValues);
			$this->storePluginInternalData ($dbValues);
			return;
			//if(!$forceValidation) return ;
		}

		$new = $forceValidation;
		$oldReason = isset($dbValues['reason'])? $dbValues['reason']: '';
		if ($inEuropa or $forceValidation) {
			vmdebug('in europe');
			$taxfree = FALSE;

			$vendormodel = VmModel::getModel ('vendor');
			$vendorAdress = $vendormodel->getVendorAdressBT (1);

			if(empty($vendorAdress->eu_vat_id)){
				vmError('The vendor has not set any euvatid, use as fallback the vendor country');
				$vendor_country_id = $vendorAdress->virtuemart_country_id;
			} else {
				$vendor_shop_name = self::transformToISOCountry2Code(substr($vendorAdress->eu_vat_id,0,2));
				$vendor_country_id = ShopFunctions::getCountryIDByName ($vendor_shop_name);
			}

			if ($virtuemart_country_id == $vendor_country_id) {
				$dbValues['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_SAME_COUNTRY';
				$taxfree = FALSE;
			}


			//if($forceValidation and empty($dbValues['manually_validated'])){
			//if((!$taxfree or $forceValidation) and empty($dbValues['manually_validated'])){
				// first check if the EU VAT number is correct;
				$validEuVatNumber = $this->validateEuVatNumber ($vat_number, $virtuemart_country_id, $company, $this->country_consistency, $euvat_result);

				if ($validEuVatNumber) {
					//$fieldName = $this->_userFieldName;
					//$taxfree = $this->validateEUVatVies ($vat_number, $vendorAdress->$fieldName, $euvat_result);
					vmdebug('$validEuVatNumber = TRUE',$vat_number,$euvat_result);
					$taxfree = $this->validateEUVatVies ($vat_number, 0, $euvat_result);
					if ($virtuemart_country_id == $vendor_country_id) {
						$dbValues['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_SAME_COUNTRY';
						$taxfree = FALSE;
					}
				} else {
					vmdebug('$validEuVatNumber = FALSE',$euvat_result);
					$taxfree = FALSE;
				}
				if (!empty($euvat_result)) {
					// Prepare data that should be stored in the database
					$sqlFields = $this->getTableSQLFields ();
					foreach ($euvat_result as $key=> $result) {
						$key = 'euvat_' . str_replace ("-", "_", $key);
						if (array_key_exists ($key, $sqlFields)) {
							$dbValues[$key] = $result;
						}
					}
				}

			if (isset($euvat_result['reason'])) {
				$dbValues['reason'] = $euvat_result['reason'];
			}
		}

		if (isset($euvat_result['faultstring'])) {
			$taxfree = FALSE;
		}

		if($dbValues['manually_validated']==1){
			$taxfree = true;
		}

		if ($taxfree and $manually_validated!=-1) {
			$this->toAdd = $this->shoppergroup_nonvat;
			$this->toRemove = $this->shoppergroup_vat;
			$dbValues['shoppergroup_remove'] = $this->toRemove;
			$dbValues['shoppergroup_added'] = $this->toAdd;
			vmdebug ('validateEuVat taxfree $toAdd ' . $this->toAdd . ' $toRemove ' . $this->toRemove. ' $vat_number '.$vat_number);
		} else if($manually_validated!=-1) {
			$this->toAdd = $this->shoppergroup_vat;
			$this->toRemove = $this->shoppergroup_nonvat;
			$dbValues['shoppergroup_remove'] = $this->toRemove;
			$dbValues['shoppergroup_added'] = $this->toAdd;
			vmdebug ('validateEuVat paytax $toAdd ' . $this->toAdd . ' $toRemove ' . $this->toRemove. ' $vat_number '.$vat_number);
		} else {
			$this->toAdd = array();
			$this->toRemove = array();
			vmdebug('I do manual and return');

			$dbValues['shoppergroup_remove'] = 0;
			$dbValues['shoppergroup_added'] = 0;
		}

		$dbValues['virtuemart_user_id'] = $userId;

		if ($store and !empty($dbValues['virtuemart_user_id'])) {
			if(isset($dbValues['modified_on']) and !$new){
				$date = JFactory::getDate ();
				$today = $date->toSQL ();
				$days = 1;
				$intTocday = strtotime ($today);
				$d = $intTocday - (86400 * $days); //3600 * 24 (1 day);
				if (strtotime ($dbValues['modified_on']) > $d) {
					$new = true;
				}
			}
			$newReason = isset($dbValues['reason'])? $dbValues['reason']: '';

			if($new or $oldReason!=$newReason){
				unset($dbValues['id']);
			}

			$this->storePluginInternalData ($dbValues);
			vmdebug ('I store this as result ', $dbValues);
		}

	}

	function alreadyValidEntry ($userId, $vat_number) {

		$euvatdata = $this->_getEuVatInternalData ($userId);

		if ( empty($euvatdata)) return false;

		$stored_vat_number = strtoupper ($euvatdata[0]->euvat_countryCode) . $euvatdata[0]->euvat_vatNumber;
		$vat_number = str_replace (array(' ', '.', '-', ',', ', '), '', $vat_number);

		if (empty($stored_vat_number) or $vat_number != $stored_vat_number) return false;

		//vmdebug ('alreadyValidEntry $stored_vat_number ' . $stored_vat_number . '   keepTINValidDays ' . $this->keepTINValidDays);
		if(!empty($this->keepTINValidDays)){
			$date = JFactory::getDate ();
			$today = $date->toSQL ();
			$intTocday = strtotime ($today);
			$d = $intTocday - (86400 * $this->keepTINValidDays); //3600 * 24 (1 day);
			if (strtotime ($euvatdata[0]->modified_on) > $d) {
				//The user is logged in and was lately (x days ago) already checked.
				//vmdebug('Return cached');
				return $euvatdata[0];
			}
		}

		return FALSE;
	}

	function pushIntoGroups ($toAdd, $toRemove, $userId, $store=true) {

		//vmdebug('pushIntoGroups',$toAdd,$toRemove, $userId);

		$session = JFactory::getSession ();
		//if (empty($userId)) {

		if(empty($toAdd)){
			$toAdd = array();
		} else {
			if(!is_array($toAdd)) $toAdd = (array) $toAdd;
		}

		if(empty($toRemove)) {
			$toRemove = array();
		} else {
			if(!is_array($toRemove)) $toRemove = (array) $toRemove;
		}

		$add = $session->get ('vm_shoppergroups_add',array(),'vm');
		if(!empty($add)){
			if(!is_array($add)) $add = (array)$add;
			$toAdd = array_merge ($add, $toAdd);
			$toAdd = array_unique($toAdd);
		}
		if(!empty($toRemove)){
			//vmdebug('pushIntoGroups add',$toAdd,$toRemove);
			$toAdd = array_diff($toAdd,$toRemove);
		}
		vmdebug('pushIntoGroups add',$toAdd);
		$session->set ('vm_shoppergroups_add', $toAdd, 'vm');

		$remove = $session->get ('vm_shoppergroups_remove',array(),'vm');

		if(!empty($remove)){
			if(!is_array($remove)) $remove = (array)$remove;
			$toRemove = array_merge ($remove, $toRemove);
			$toRemove = array_unique($toRemove);
		}
		if(!empty($toAdd)){
			$toRemove = array_diff($toRemove,$toAdd);
		}
		vmdebug('pushIntoGroups remove',$toRemove);
		$session->set ('vm_shoppergroups_remove', $toRemove, 'vm');
		$session->set ('vm_shoppergroups_set.' . $userId, TRUE, 'vm');

		//} else {
		if (!empty($userId)) {
			if (!class_exists ('TableVmuser_shoppergroups')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'vmuser_shoppergroups.php');
			}
			$db = JFactory::getDBO ();
			//$user_shoppergroups_table = new TableVmuser_shoppergroups($db);
			$userModel = VmModel::getModel('user');
			$user_shoppergroups_table= $userModel->getTable('vmuser_shoppergroups');
			$shoppergroupData['virtuemart_shoppergroup_id'] = $user_shoppergroups_table->load ($userId);
			$shoppergroupData['virtuemart_user_id'] = $userId;

			if (!empty($toAdd)) {
				if (empty($shoppergroupData['virtuemart_shoppergroup_id'])) {
					$shoppergroupData['virtuemart_shoppergroup_id'] = $toAdd;
					//$shoppergroupData['virtuemart_user_id'] = $userId;
					// 					vmdebug('plgVmPrepareUserfieldDataSave adde '.$toAdd);
				} else {
					//if (!in_array ($toAdd, $shoppergroupData['virtuemart_shoppergroup_id']))
					$shoppergroupData['virtuemart_shoppergroup_id'] = array_merge ($shoppergroupData['virtuemart_shoppergroup_id'], $toAdd);
					$shoppergroupData['virtuemart_shoppergroup_id'] = array_unique($shoppergroupData['virtuemart_shoppergroup_id']);
					//$shoppergroupData['virtuemart_shoppergroup_id'][] = $toAdd;
					//$shoppergroupData['virtuemart_user_id'] = $userId;
					// 					vmdebug('plgVmPrepareUserfieldDataSave adde '.$toAdd);
					//}
				}
			}

			if (!empty($toRemove) and !empty($shoppergroupData['virtuemart_shoppergroup_id']) ){
				$shoppergroupData['virtuemart_shoppergroup_id'] = array_unique($shoppergroupData['virtuemart_shoppergroup_id']);
				$toRemove = array_unique($toRemove);
				$shoppergroupData['virtuemart_shoppergroup_id'] = array_diff($shoppergroupData['virtuemart_shoppergroup_id'],$toRemove);
				/*$key = array_search ($toRemove, $shoppergroupData['virtuemart_shoppergroup_id']);
				vmdebug('my key to remove',$key,$toRemove, $shoppergroupData['virtuemart_shoppergroup_id']);
				if ($key !== FALSE) {
					unset($shoppergroupData['virtuemart_shoppergroup_id'][$key]);
					//$shoppergroupData['virtuemart_user_id'] = $userId;
					// 					vmdebug('plgVmPrepareUserfieldDataSave remove '.$toRemove,$key);
				}*/
			}
			// 			vmdebug('plgVmPrepareUserfieldDataSave',$shoppergroupData);

			if ($store and !empty($shoppergroupData['virtuemart_user_id'])) {
				vmdebug('Storing to new shoppergroup ',$shoppergroupData);
				$shoppergroupData = $user_shoppergroups_table->bindChecknStore ($shoppergroupData);
				$userModel->_data=null;	//in case the user was already loaded per model, we delete the cached values
			}
			$session->set ('vm_shoppergroups_set.' . $userId, TRUE, 'vm');
		}

	}

	function onShowUserDisplayBEUserfield ($userId, $vatNumber, $fieldName) {

		if (!($euVatTable = $this->_getEuVatInternalData ($userId))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		if (empty($euVatTable)) {
			return "";
		}
		$display_fields = array('euvat_countryCode', 'euvat_vatNumber', 'euvat_valid', 'euvat_name', 'euvat_address',
			'euvat_traderCompanyType',
			'euvat_traderName',
			'euvat_traderAddress',
			'euvat_traderPostcode',
			'euvat_traderCity',
			'euvat_requestDate', 'euvat_requestIdentifier', 'reason', 'shoppergroup_remove', 'shoppergroup_added');


		$js = '
		jQuery(document).ready(function( $ ) {
			$("#istraxx_euvat_show_hide").hide();
			jQuery("#istraxx_euvat_link").click( function() {
				 if ( $("#istraxx_euvat_show_hide").is(":visible") ) {
				  $("#istraxx_euvat_show_hide").hide("slow");
			        $("#istraxx_euvat_link").html("' . addslashes (vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_SHOW_EUVAT')) . '");
				} else {
				 $("#istraxx_euvat_show_hide").show("slow");
			       $("#istraxx_euvat_link").html("' . addslashes (vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_HIDE_EUVAT')) . '");
			    }
		    });
		});
';

		$doc = JFactory::getDocument ();
		$doc->addScriptDeclaration ($js);
		if(!class_exists('VmHTML')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
		$html = '<br />'.vmText::_('VMUSERFIELD_ISTRAXX_EUVATCHECKER_FORCE').' '.VmHTML::checkbox('euvatid_forceValidation',0);
		$html .= '<a href="#" id="istraxx_euvat_link" ">' . vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_SHOW_EUVAT') . '</a>';

		$html .= '<div id="istraxx_euvat_show_hide" >';
		$html .= '<table  class="adminlist" width="50%">' . "\n";
		$code = "euvat";
		$i = 1;
		$shoppergroup_model = VmModel::getModel ('shoppergroup');

		$first= true;
		foreach ($euVatTable as $euVatEntry) {
			$class = 'class="row' . $i . '"';
			$html .= '<tr class="row1"><td>' . vmText::_ ('COM_VIRTUEMART_DATE') . '</td><td align="left">' . $euVatEntry->created_on . '</td></tr>';
			//foreach ($euVatEntry as $key => $value) {
			foreach ($display_fields as $display_field) {
				$complete_key = strtoupper ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_' . $display_field);

				$value = $euVatEntry->$display_field;
				$key_text = vmText::_ ($complete_key);
				if ((strpos ($display_field, 'shoppergroup') !== FALSE) and (!empty($value))) {
					$shoppergroup_model->setId ($value);
					$shopperGroupInfo = $shoppergroup_model->getShopperGroup ();
					$value .= ' (' . $shopperGroupInfo->shopper_group_name;
					if (!empty($shopperGroupInfo->shopper_group_desc)) {
						$value .= " / " . $shopperGroupInfo->shopper_group_desc;
					}
					$value .= ')';


				} else if($display_field=='euvat_valid'){
					if($value){
						$value = vmText::_('VMUSERFIELD_ISTRAXX_EUVATCHECKER_EUVAT_VALID');
					} else {
						$value = vmText::_('Invalid');
					}
					if($first){
						$radios = array();
						$radios[] = JHTML::_('select.option',0,vmText::_('default'));
						$radios[] = JHTML::_('select.option',1,vmText::_('taxfree'));
						$radios[] = JHTML::_('select.option',-1,vmText::_('manual'));
						$value .= '<br>'.JHTML::_('select.radiolist', $radios, 'manually_validated', '', 'value', 'text', $euVatEntry->manually_validated);
						//$successful = $this->toggle($euVatEntry->manually_validated, $row['id'],$row['virtuemart_order_item_id']);
						//$value .= VmHTML::checkbox('manually_validated',$euVatEntry->manually_validated);
						$first = false;
					}

				} else {
					$value = vmText::_ ($value);
				}

				if (!empty($value)) {
					$html .= "<tr>\n<td>" . $key_text . "</td>\n <td align='left'>" . $value . "</td>\n</tr>\n";
				}
			}

		}

		$html .= '</table>' . "\n";
		$html .= '</div>' . "\n";
		return $html;
	}


	final protected function getJoomlaPluginParams () {

		$db = JFactory::getDBO ();

		if (JVM_VERSION === 1) {
			$q = 'SELECT j.`params` AS c FROM #__plugins AS j
					WHERE j.element = "' . $this->_name . '" AND j.folder = "' . $this->_type . '"';
		} else {
			$q = 'SELECT j.`params` AS c FROM #__extensions AS j
					WHERE j.element = "' . $this->_name . '" AND j.`folder` = "' . $this->_type . '"';
		}

		$db->setQuery ($q);
		$params = $db->loadResult ();
		if (!$params) {
			vmError ('getJoomlaPluginParams ' . $db->getErrorMsg ());
			return FALSE;
		} else {
			return $params;
		}
	}

	/**
	 * @param        $virtuemart_order_id
	 * @param string $order_number
	 * @return mixed|string
	 */
	function _getEuVatInternalData ($userId) {

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';
		$q .= ' `virtuemart_user_id` = "' . $userId . '"';
		$q .= ' ORDER BY `modified_on` DESC ';

		$db->setQuery ($q);
		return $db->loadObjectList ();

	}

	function  plgVmOnBeforeUserfieldSave ($plgName, &$data, &$tableClass) {

		if ($this->_name != $plgName) {
			return;
		}
		$vars = array();
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			foreach ($this->varsToPush as $key => $var) {
				$vars[$key] = array($data['params'][$key], $var[1]);
			}
			$tableClass->setParameterable ('params', $vars);
			vmdebug('Whats going on here vm2');
		} else {
			foreach ($this->varsToPush as $key => $var) {
				$vars[$key] = array($data['params'][$key], $var[1]);
			}
			$tableClass->setParameterable ('userfield_params', $vars);
			vmdebug('Whats going on here vm3');
		}


	}

	static function validateEuVatNumberSensitive (&$vat_number, $virtuemart_country_id, $company, $county_consistency, &$result) {

		if (empty($vat_number)) {
			return FALSE;
		}

		$vat_number = str_replace (array(' ', '.', '-', ',', ', '), '', $vat_number);
		$vatCountryCode = strtoupper (substr ($vat_number, 0, 2));
		$vatNumber = substr ($vat_number, 2);

		//No error output, because usually this are just people who do not need to enter any vatid
		if (strlen ($vatCountryCode) != 2 ) {
			return FALSE;
		} else if (is_numeric (substr ($vatCountryCode, 0, 1))) {
			$vatCountryCode = shopFunctions::getCountryByID ($virtuemart_country_id, 'country_2_code');
			if ($vatCountryCode == 'GR') {
				$vatCountryCode = 'EL';
			} else if ($vatCountryCode == 'AT'){
				$vatCountryCode = 'ATU';
			}
			$vat_number = $vatCountryCode.$vatNumber;
		}
		if (!isset($virtuemart_country_id)) {
			//$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_NO_COUNTRYCODE';
			return FALSE;
		}

		$addressCountryCode = shopFunctions::getCountryByID ($virtuemart_country_id, 'country_2_code');
		$inEuropa = in_array ($addressCountryCode, self::$european_countries_2_code);
		if(!$inEuropa){
			return false;
		}
		if ($county_consistency) {

			$vatCountryCodeVM = self::transformToISOCountry2Code ($vatCountryCode);
			if ($addressCountryCode != $vatCountryCodeVM) {
				$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COUNTRYCODE_INCONSISTENT';
				vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_COUNTRYCODE_INCONSISTENT');
				return FALSE;
			}
		}
		if (isset($company) and (empty($company))) {
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED';
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED');
			return FALSE;
		}

		if (!self::is_valid_EUVAT_number ($vatNumber, $vatCountryCode)) {
			$format_country = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_FORMAT_' . $vatCountryCode;
			$invalid_format = vmText::sprintf ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT', vmText::_ ($format_country));
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT_REASON';
			vmInfo ($invalid_format);
			return FALSE;
		}
		return TRUE;

	}

	static function validateEuVatNumber (&$vat_number, $virtuemart_country_id, $company, $county_consistency, &$result) {

		if (empty($vat_number)) {
			return FALSE;
		}
		$vat_number = str_replace (array(' ', '.', '-', ',', ', '), '', $vat_number);
		$vatCountryCode = strtoupper (substr ($vat_number, 0, 2));
		$vatNumber = substr ($vat_number, 2);

		//if (strlen ($vatCountryCode) != 2 || is_numeric (substr ($vatCountryCode, 0, 1)) || is_numeric (substr ($vatCountryCode, 1, 2))) {
		if (strlen ($vatCountryCode) != 2 ) {
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_COUNTRYCODE';
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_COUNTRYCODE');
			return FALSE;
		} else if (is_numeric (substr ($vatCountryCode, 0, 1))) {
			$vatCountryCode = shopFunctions::getCountryByID ($virtuemart_country_id, 'country_2_code');

			if ($vatCountryCode == 'GR') {
				$vatCountryCode = 'EL';
			} else if ($vatCountryCode == 'AT'){
				$vatCountryCode = 'ATU';
			}
			$vat_number = $vatCountryCode.$vatNumber;
		}
		$result['vatNumber'] = $vat_number;

		if (!isset($virtuemart_country_id)) {
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_NO_COUNTRYCODE';
			return FALSE;
		}

		$addressCountryCode = shopFunctions::getCountryByID ($virtuemart_country_id, 'country_2_code');
		if ($county_consistency) {

			$vatCountryCodeVM = self::transformToISOCountry2Code ($vatCountryCode);
			if ($addressCountryCode != $vatCountryCodeVM) {
				$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COUNTRYCODE_INCONSISTENT';
				vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_COUNTRYCODE_INCONSISTENT');
				return FALSE;
			}
		}
		$result['countryCode'] = $addressCountryCode;

		if (isset($company) and (empty($company))) {
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED';
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_REQUIRED');
			return FALSE;
		}

		if (!self::is_valid_EUVAT_number ($vatNumber, $vatCountryCode)) {
			$format_country = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_FORMAT_' . $vatCountryCode;
			$invalid_format = vmText::sprintf ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT', vmText::_ ($format_country));
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID_FORMAT_REASON';
			vmInfo ($invalid_format);
			return FALSE;
		}

		return TRUE;
	}

	static function transformToISOCountry2Code ($vatCountryCode) {

		if ($vatCountryCode == 'EL') {
			$vatCountryCodeVM = 'GR';
		} else {
			$vatCountryCodeVM = $vatCountryCode;
		}
		return $vatCountryCodeVM;
	}

	/* Algorithm to check if valid VAT number
	* The first seven digits of the VAT registration number are listed vertically.
	* Each digit is multiplied by a number, starting with 8 and decreasing to 2.
	* The sum of the multiplications is calculated.
	* 97 is subtracted from the sum as many times as is necessary to arrive at a negative number.
	* The negative number should be the same as the last 2 digits of the VAT registration number if it is valid.
	*/

	function validateEUVatVies ($vat_number, $vendor_vat_number, &$result) {

		// vmdebug('vat NUMBER',$vat_number);
		if (empty($vat_number)) {
			return FALSE;
		}
		$vat_number = strtoupper(str_replace (array(' ', '.', '-', ',', ', '), '', $vat_number));
		$vatCountryCode = substr ($vat_number, 0, 2);
		$vatNumber = substr ($vat_number, 2);

		if (!empty($vendor_vat_number)) {
			$vendor_vat_number = str_replace (array(' ', '.', '-', ',', ', '), '', $vendor_vat_number);
			$vendor_vatCountryCode = substr ($vendor_vat_number, 0, 2);
			$vendor_vatNumber = substr ($vendor_vat_number, 2);
			$call = 'checkVatApprox';
			$params = array('countryCode'          => $vatCountryCode, 'vatNumber' => $vatNumber,
				'requesterCountryCode' => $vendor_vatCountryCode, 'requesterVatNumber' => $vendor_vatNumber
			);
		} else {
			$call = 'checkVat';
			$params = array('countryCode' => $vatCountryCode, 'vatNumber' => $vatNumber);
		}
		$viesUrl = "http://ec.europa.eu/taxation_customs/vies/services/checkVatService?wsdl";
		if (!class_exists ('nusoap_client')) {
			require('istraxx_euvatchecker' . DS . 'libraries' . DS . 'nusoap' . DS . 'nusoap.php');
		}

		$timeout = 15;
		$response_timeout = 10;
		$client = new nusoap_client($viesUrl, TRUE, FALSE, FALSE, FALSE, FALSE, $timeout, $response_timeout);

		$error = $client->getError ();
		if ($error) {
			vmError ("nusoap_client error " . $viesUrl);
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_SERVICE_UNAVAILABLE';
			vmInfo (vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_SERVICE_UNAVAILABLE'));
			return FALSE;
		}

		// Set the parameters to send to the WebService
		$client->soap_defencoding = 'UTF-8';
		$client->decodeUTF8 (FALSE);
		$result = $client->call ($call, $params, '');

		if (!(is_array ($result))) {
			$result['reason'] = 'VMUSERFIELD_ISTRAXX_EUVATCHECKER_NO_RESULT';
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID');
			vmdebug('VMUSERFIELD_ISTRAXX_EUVATCHECKER Result was not an array ',$result);
			return FALSE;
		}
		// error handling is done according to this document http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl
		if (isset($result['faultstring'])) {
			if ((preg_match ('/SERVICE_UNAVAILABLE/i', $result['faultstring']) or
				preg_match ('/TIMEOUT/i', $result['faultstring'])  or preg_match ('/SERVER_BUSY/i', $result['faultstring']))
			) {
				vmInfo (vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_SERVICE_UNAVAILABLE'));
				return FALSE;
			} elseif (preg_match ('/MS_UNAVAILABLE/i', $result['faultstring'])) {
				vmInfo (vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_MS_UNAVAILABLE'));
			} elseif ($result['faultstring']) {
				vmInfo ($result['faultstring']);
				return FALSE;
			}
		}

		if (!empty($result['valid']) && ($result['valid'] == 1 || $result['valid'] == 'true')) {
			/* all countries do not return the company name info
			   if ($this->company_consistency) {
				   $addressCompany = $address['company'];
				   if ($addressCompany != $vatCountryCode) {
					   vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_COMPANYNAME_INCONSISTENT');
					   return FALSE;
				   }
			   }
			   */
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_VALID');
			return TRUE;
		} else {
			vmInfo ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_INVALID');
			vmdebug('VMUSERFIELD_ISTRAXX_EUVATCHECKER Result was invalid ',$result);
			return FALSE;
		}

	}

	/**
	 * based on http://codeigniter.com/wiki/European_Vat_Checker/
	 * Based on rules in http://ec.europa.eu/taxation_customs/vies/faqvies.do
	 * check if nif is valid
	 *
	 * @param string $vat
	 * @param string $country_iso
	 * @return boolean
	 */

	static function is_valid_EUVAT_number ($vat, $country_iso) {

		$country_iso = strtoupper ($country_iso);
		$regex = '';

		switch ($country_iso) {
			// Austria: 'AT'+9 characters, the first position following the prefix is always "U" – e.g. ATU99999999
			case 'AT':
				$regex = '/^U[0-9]{8}$/';
				break;
			// Belgium: 'BE'+10 digits, the first digit following the prefix is always zero ("0") – e.g. BE0999999999
			case 'BE':
				$regex = '/^0[0-9]{9}$/';
				break;
			//Bulgaria: 9–10 digits – e.g. BG999999999
			case 'BG':
				$regex = '/^[0-9]{9,10}$/';
				break;
			// Cyprus: 9 characters – e.g. CY99999999L
			case 'CY':
				$regex = '/^[0-Z]{9}$/';
				break;
			// Czech Republic: 8, 9 or 10 digits
			case 'CZ':
				$regex = '/^[0-9]{8,10}$/';
				break;
			// Germany: 9 digits
			case 'DE':
				$regex = '/^[0-9]{9}$/';
				break;
			// Denmark: 8 digits, 4 blocks of 2 – e.g. DK99 99 99 99, last digit is check digit
			case 'DK':
				$regex = '/^[0-9]{8}$/';
				break;
			// Estonia: 9 digits
			case 'EE':
				$regex = '/^[0-9]{9}$/';
				break;
			// Greece: 9 digits, last one is a check digit
			case 'EL':
				$regex = '/^[0-9]{9}$/';
				break;
			// Spain: 'ES'+ 1 block of 9 characters, The first and last characters may be alpha or numeric; but they may not both be numeric. – e.g. ESX9999999X
			case 'ES':
				$regex = '/^[0-9][0-Z]{7}[A-Z]|[A-Z][0-Z]{7}[A-Z]|[A-Z][0-Z]{7}[0-9]$/';
				break;
			// Finland: 8 digits
			case 'FI':
				$regex = '/^[0-9]{8}$/';
				break;
			// France: 'FR'+ 2 digits (as valitation key ) + 9 digits (as SIREN) , the first and/or the second value can also be a character – e.g. FRXX999999999
			case 'FR':
				$regex = '/^[0-Z]{2}[0-9]{9}$/';
				break;
			/* United Kingdom and Isle of Man:
			* Country code GB followed by either:
			* standard: 9 digits (block of 3, block of 4, block of 2 – e.g. GB999 9999 73)
			* branch traders: 12 digits (as for 9 digits, followed by a block of 3 digits)
			* government departments: the letters GD then 3 digits from 000 to 499 (e.g. GBGD001)
			* health authorities: the letters HA then 3 digits from 500 to 999 (e.g. GBHA599)
			*/
			case 'GB':
				//$regex = '/^([0-9]{9}|[0-9]{12})~(GD|HA)[0-9]{3}$/';
				$regex = '/^([0-9]{9}|[0-9]{12}|(GD|HA)[0-9]{3})$/';
				break;
			// Hungary: 8 digits – e.g. HU12345678
			case 'HU':
				$regex = '/^[0-9]{8}$/';
				break;
			// Croatia 1 block of 11 digits
			case 'HR':
				$regex = '/^[0-9]{11}$/';
				break;
			// Ireland: IE'+8 digits, the second can be a character and the last one must be a character – e.g. IE9S99999L (S = letter/digit/"+"/"*", L = letter)
			case 'IE':
				//$regex = '/^[0-9][A-Z0-9\\+\\*][0-9]{5}[A-Z]$/';
				$regex = '/^[0-Z]{8}|[0-Z]{9}$/';	//IE-Ireland	IE9S99999L	IE9999999WI	1 block of 8 characters or 1 block of 9 characters
				break;
			// Italy: 11 digits (the first 7 digits is a progressive number, the following 3 means the province of residence, the last digit is a check number)
			case 'IT':
				$regex = '/^[0-9]{11}$/';
				break;
			// Lithuania: 9 or 12 digits
			case 'LT':
				$regex = '/^([0-9]{9}|[0-9]{12})$/';
				break;
			// Luxembourg: 8 digits
			case 'LU':
				$regex = '/^[0-9]{8}$/';
				break;
			// Latvia: 11 digits
			case 'LV':
				$regex = '/^[0-9]{11}$/';
				break;
			// Malta: 8 digits
			case 'MT':
				$regex = '/^[0-9]{8}$/';
				break;
			// Netherlands: 'NL'+12 characters – e.g. NL999999999B99 The 10th position following the prefix is always "B".
			case 'NL':
				$regex = '/^[0-Z]{9}[B][0-Z]{2}$/';
				break;
			// Poland:10 digits, the last one is a check digit; for convenience the digits are separated by hyphens (xxx-xxx-xx-xx), but formally the number consists only of digits
			case 'PL':
				$regex = '/^[0-9]{10}$/';
				break;
			// Portugal: 9 digits
			case 'PT':
				$regex = '/^[0-9]{9}$/';
				break;
			// Romania: 2–10 digits
			case 'RO':
				$regex = '/^[0-9]{2,10}$/';
				break;
			// Sweden: 12 digits, of which the last two are always 01.
			case 'SE':
				$regex = '/^[0-9]{12}$/';
				break;
			// Slovenia: 8 digits
			case 'SI':
				$regex = '/^[0-9]{8}$/';
				break;
			// Slovakia: 10 digits
			case 'SK':
				$regex = '/^[0-9]{10}$/';
				break;
			default:
				return FALSE;
				break;
		}

		$vat = str_replace ($country_iso, '', $vat);
		return (preg_match ($regex, $vat));
	}

}

// No closing tag
