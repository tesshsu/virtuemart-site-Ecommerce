<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.model');

class VMMigrateModelVirtuemart extends VMMigrateModelBase {

	public $isPro = true;
	
	var $vendorModel;
	var $vendor;
	var $mediaModel;
	var $baseLanguageTable;
	
    function __construct($config = array()) {
        parent::__construct($config);

		if (!self::isInstalledDest('com_virtuemart')) {
			return false;
		}

		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		if(!class_exists('Permissions') && JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php');
		
		
		$this->baseLanguageTable = $this->getLanguageTableSuffix($this->baseLanguage);

		$this->logDebug($this->baseLanguage,'Base languages');
		$this->logDebug($this->additionalLanguages,'Additional languages');

		VmConfig::loadConfig();
		$this->limit = 25;
		$this->getVmTablePrefix();
    }
	
	private function getLanguageTableSuffix($lang) {
		return strtolower(str_replace('-','_',$lang));
	}

	public function getSrcVersion() {
		return $this->getSrcVersionPrivate();
	}
	
	public function getSrcVersionPrivate() {
		
		if ($this->source_filehelper->FileExists('/administrator/components/com_virtuemart/version.php' )) { 
			$buffer = $this->source_filehelper->ReadFile('/administrator/components/com_virtuemart/version.php');
			try {
				if (!class_exists('vmVersionSrc')) {
					$buffer = str_replace('vmVersion','vmVersionSrc',$buffer);
					$buffer = str_replace('<?php','',$buffer);
					$buffer = str_replace('?>','',$buffer);
					if (version_compare(phpversion(), '7', '>=')) {
						//php7: New objects cannot be assigned by reference
						$buffer = str_replace('=&','=',$buffer);
						$buffer = str_replace('= &','=',$buffer);
					}
					eval($buffer);
				}
				$VMVERSION = new vmVersionSrc();
				if (isset($VMVERSION->RELEASE)) {
					$version = $VMVERSION->RELEASE;
				} else {
					$version = vmVersionSrc::$RELEASE;
				}
				if (version_compare($version,'2','>')) {
					$version='2.0';
				}
				return $version;
			} catch (Exception $e) {
				$this->logError($e->getMessage());
				return null;
			}
		} else {
			return 0;
		}
	}
	

	private function getVmTablePrefix() {
		$this->vmTablePrefix = 'vm';
		if (!$this->loadOldConfig()) {
			return 'vm';
		}
		$this->vmTablePrefix = VM_TABLEPREFIX;
		return $this->vmTablePrefix;
	}
	
	public function getDstVersion() {
		return $this->getVmVersion();
	}

	private function getVmVersion() {
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php' )) { 
			include_once (JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php' );
			$VMVERSION = new vmVersion();
			if (isset($VMVERSION->RELEASE)) {
				$version = $VMVERSION->RELEASE;
			} else {
				$version = vmVersion::$RELEASE;
			}
			return $version;
		} else {
			return 0;
		}
	}
	
	public static function getMessages() {
		$messages = array();
		$messages['error'] = array();
		$messages['warning'] = array();
		$messages['info'] = array();
		$messages['success'] = array();
		
		if (!self::isInstalledSource('com_virtuemart')) {
			$messages['error'][] = JText::sprintf('VMMIGRATE_EXTENSION_X_NOT_FOUND_SOURCE','com_virtuemart');
		}
		if (!self::isInstalledDest('com_virtuemart')) {
			$messages['error'][] = JText::sprintf('VMMIGRATE_EXTENSION_X_NOT_FOUND_DEST','com_virtuemart');
		}
		$instance = new  VMMigrateModelVirtuemart();
		$srcVersion = $instance->getSrcVersion();
		if (version_compare($srcVersion,'2','>=')) {
			$messages['error'][] = JText::_('VIRTUEMART_WRONG_VERSION_SRC');
		}
		return $messages;
	}
	
	public static function getSteps() {
		if (!self::isInstalledBoth('com_virtuemart')) {
			return array();
		}
		
		$instance = new  VMMigrateModelVirtuemart();
		$srcVersion = $instance->getSrcVersionPrivate();
		if (version_compare($srcVersion,'2','>=') || !$srcVersion) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'				,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_LOG'));
		$steps[] = array('name'=>'reset_log_error'			,'default'=>1);
		$steps[] = array('name'=>'reset_data'				,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_DATA'));
		$steps[] = array('name'=>'reset_vm'					,'default'=>0, 'warning'=>JText::_('RESET_VM_WARNING'));
		$steps[] = array('name'=>'set_config'				,'default'=>1);
		$steps[] = array('name'=>'create_languages'			,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'menu_items'				,'default'=>0);
		$steps[] = array('name'=>'fix_modules'				,'default'=>0);
		$steps[] = array('name'=>'vm_vendor'				,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_tax_rate'				,'default'=>1);
		$steps[] = array('name'=>'vm_payment_method'		,'default'=>0, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_shipping_rates'		,'default'=>0);
		$steps[] = array('name'=>'vm_shipment_method'		,'default'=>0);
		$steps[] = array('name'=>'vm_shopper_group'			,'default'=>1);
		$steps[] = array('name'=>'vm_category'				,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_manufacturer_category'	,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_manufacturer'			,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_order_status'			,'default'=>1);
		$steps[] = array('name'=>'vm_userfield'				,'default'=>1);
		$steps[] = array('name'=>'vm_shopper_vendor_xref'	,'default'=>1);
		$steps[] = array('name'=>'vm_product_type'			,'default'=>1);
		$steps[] = array('name'=>'vm_product'				,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'vm_product_stock'			,'default'=>0);
		$steps[] = array('name'=>'vm_product_medias'		,'default'=>1);
		$steps[] = array('name'=>'vm_waiting_list'			,'default'=>1);
		//$steps[] = array('name'=>'vm_product_votes'			,'default'=>1);
		$steps[] = array('name'=>'vm_product_reviews'		,'default'=>1);
		//$steps[] = array('name'=>'vm_product_discount'		,'default'=>1);
		$steps[] = array('name'=>'vm_orders'				,'default'=>1);
		$steps[] = array('name'=>'vm_coupons'				,'default'=>1);
		return $steps;
	}
	
	public function reset_data() {

		$resetall = (count($this->steps)==1 && $this->steps[0]=='reset_data');
		$sql = '';
		if (in_array('vm_vendor',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_vendors;";
			$sql .= "TRUNCATE TABLE #__virtuemart_vendors_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_vendors_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_vendor_medias;";
		}
		if (in_array('vm_tax_rate',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_calcs WHERE calc_kind = 'Tax';";
			$sql .= "DELETE FROM #__virtuemart_calc_countries WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_states WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_categories WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_shoppergroups WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
		}
		if (in_array('vm_payment_method',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_paymentmethods;";
			$sql .= "TRUNCATE TABLE #__virtuemart_paymentmethods_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_paymentmethods_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
		}
		if (in_array('vm_shipment_method',$this->steps) || in_array('vm_shipping_rates',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_shipmentmethods;";
			$sql .= "TRUNCATE TABLE #__virtuemart_shipmentmethods_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_shipmentmethods_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
		}
		if (in_array('vm_shopper_group',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_shoppergroups WHERE virtuemart_shoppergroup_id > 2;";
			$sql .= "DELETE FROM #__virtuemart_calcs WHERE calc_kind = 'DBTaxBill';";
			$sql .= "DELETE FROM #__virtuemart_calc_countries WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_states WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_categories WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
			$sql .= "DELETE FROM #__virtuemart_calc_shoppergroups WHERE virtuemart_calc_id NOT IN (SELECT virtuemart_calc_id FROM #__virtuemart_calcs);";
	}
		if (in_array('vm_category',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_categories_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_categories_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_category_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_category_medias;";
		}
		if (in_array('vm_manufacturer_category',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturercategories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturercategories_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_manufacturercategories_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
		}
		if (in_array('vm_manufacturer',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturers;";
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturers_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_manufacturers_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
		}
		if (in_array('vm_order_status',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_orderstates WHERE order_status_code NOT IN ('S','R','X','C','U','P');";
		}
		if (in_array('vm_userfield',$this->steps) || $resetall) {
			if ($this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_userfield')) {	
				$sql .= "DELETE FROM #__virtuemart_userfields WHERE sys = 0;";
				//$sql .= "DELETE FROM #__virtuemart_userfield_values WHERE virtuemart_userfield_id NOT IN (SELECT virtuemart_userfield_id FROM #__virtuemart_userfields);";
			}
		}
		if (in_array('vm_shopper_vendor_xref',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_userinfos;";
			$sql .= "TRUNCATE TABLE #__virtuemart_vmuser_shoppergroups;";
			$sql .= "TRUNCATE TABLE #__virtuemart_vmusers;";
		}
		if (in_array('vm_product_type',$this->steps) || $resetall) {
			//$sql .= "DELETE FROM #__virtuemart_customs WHERE field_type NOT IN ('R','Z');";
			$sql .= "TRUNCATE TABLE #__virtuemart_customs;";
		}
		if (in_array('vm_product',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_products;";
			$sql .= "TRUNCATE TABLE #__virtuemart_products_".$this->baseLanguageTable.";";
			foreach ($this->additionalLanguages as $language) {
				$sql .= "TRUNCATE TABLE #__virtuemart_products_".$this->getLanguageTableSuffix($language->lang_code).";";
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_product_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_manufacturers;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_prices;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_customfields;";
		}
		if ((in_array('vm_product_type',$this->steps) && in_array('vm_product',$this->steps)) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_product_customfields;";
		}
		if (in_array('vm_product_medias',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_medias WHERE file_type='product';";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_medias;";
		}
		if (in_array('vm_waiting_list',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_waitingusers;";
		}
		//if (in_array('vm_product_votes',$this->steps) || $resetall) {
		//	$sql .= "TRUNCATE TABLE #__virtuemart_rating_votes;";
		//}
		if (in_array('vm_product_reviews',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_rating_reviews;";
			$sql .= "TRUNCATE TABLE #__virtuemart_rating_votes;";
			$sql .= "TRUNCATE TABLE #__virtuemart_ratings;";
		}
		if (in_array('vm_product_discount',$this->steps) || $resetall) {
			//$sql .= "TRUNCATE TABLE #__virtuemart_calcs;";
		}
		if (in_array('vm_orders',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_orders;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_histories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_items;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_userinfos;";
		}
		if (in_array('vm_coupons',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_coupons;";
		}

		if (!$sql) {
			return;
		}

		if (VMMigrateHelperDatabase::queryBatch($this->destination_db,$sql)) {
			foreach ($this->steps as $step) {
				$this->logInfo(JText::sprintf('DATA_RESETED',JText::_($step)));
			}
			$this->resetAutoIncrements();
			$this->ensureRelatedCustomsFields();

		} else {
			$this->logError(JText::_('DATA_RESET_ERROR').'<br/>'.$this->destination_db->getErrorMsg());
		}
	}
	
	public function reset_vm() {

		try {
			$model = VmModel::getModel('updatesMigration');
			$model->restoreSystemTablesCompletly();
			$this->logInfo(JText::sprintf('DATA_RESETED',''));
		} catch (Exception $e) {
			$this->logError(JText::_('DATA_RESET_ERROR'));
		}
	}
	
	private function loadOldConfig() {
		$oldConfigFile = '/administrator/components/com_virtuemart/virtuemart.cfg.php';
		if ($this->source_filehelper->FileExists($oldConfigFile)) {
			return $this->source_filehelper->IncludeFile($oldConfigFile);
		}
		return false;
	}
	
	public function set_config() {

		if (!$this->loadOldConfig()) {
	        $this->logError('Could not read configuration file');
			return false;
		}

		$config = VmConfig::loadConfig(TRUE);

		//Shop
		$config->set('shop_is_offline',PSHOP_IS_OFFLINE);
		$config->set('offline_message',PSHOP_OFFLINE_MESSAGE);
		$config->set('use_as_catalog',USE_AS_CATALOGUE);
		$config->set('enable_content_plugin',VM_CONTENT_PLUGINS_ENABLE);
		$config->set('useSSL',(count($VM_MODULES_FORCE_HTTPS) && URL != SECUREURL) ? 1 : 0);
		if (DEBUG) {
			if (VM_DEBUG_IP_ENABLED) {
				$config->set('debug_enable','admin');
			} else {
				$config->set('debug_enable','all');
			}
		} else {
			$config->set('debug_enable','none');
		}
		
		//Shop front
		$config->set('show_emailfriend',VM_SHOW_EMAILFRIEND);
		$config->set('show_printicon',VM_SHOW_PRINTICON);
		$config->set('pdf_icon',PSHOP_PDF_BUTTON_ENABLE);
		$config->set('product_navigation',PSHOP_SHOW_TOP_PAGENAV);
		$config->set('coupons_enable',PSHOP_COUPONS_ENABLE);
		if (CHECK_STOCK) {
			if (PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS) {
				$config->set('stockhandle','disableadd');
			} else {
				$config->set('stockhandle','disableit_children');
			}
		} else {
			$config->set('stockhandle','none');
		}
		$config->set('reviews_autopublish',VM_REVIEWS_AUTOPUBLISH);
		$config->set('reviews_minimum_comment_length',VM_REVIEWS_MINIMUM_COMMENT_LENGTH);
		$config->set('reviews_maximum_comment_length',VM_REVIEWS_MAXIMUM_COMMENT_LENGTH);
		if (!PSHOP_ALLOW_REVIEWS) {
			$config->set('showReviewFor','none');
			$config->set('reviewMode','none');
			$config->set('showRatingFor','none');
			$config->set('ratingMode','none');
		}
		
		//Templates
		$theme_config_file = $this->source_path.VM_THEMEPATH.'theme.config.php';
		if (JFile::exists($theme_config_file)) {
			$theme_config_text = JFile::read($theme_config_file);
			$theme_config = $this->parseThemeConfig($theme_config_text);
			$config->set('show_manufacturers',$theme_config->showManufacturerLink);
			$config->set('show_featured',$theme_config->showFeatured);
			$config->set('show_latest',$theme_config->showlatest);
			$config->set('show_recent',($theme_config->showRecent>0 ? '1' : '0'));
			$config->set('recent_products_rows',$theme_config->show_recent);
			$config->set('addtocart_popup',$theme_config->useAjaxCartActions);
		}
		switch (CATEGORY_TEMPLATE) {
			case 'browse_1': $config->set('categories_per_row',1); break;
			case 'browse_2': $config->set('categories_per_row',2); break;
			case 'browse_3': $config->set('categories_per_row',3); break;
			case 'browse_4': $config->set('categories_per_row',4); break;
			case 'browse_5': $config->set('categories_per_row',5); break;
		}
		
		$config->set('products_per_row',PRODUCTS_PER_ROW);
		$config->set('feed_cat_published',VM_FEED_ENABLED);
		//$config->set('img_resize_enable',PSHOP_IMG_RESIZE_ENABLE);
		if (function_exists('imagecreatefromjpeg')) {
			$config->set('img_resize_enable',1);
		} else {
			$config->set('img_resize_enable',0);
		}
		$config->set('img_width',PSHOP_IMG_WIDTH);
		$config->set('img_height',PSHOP_IMG_HEIGHT);
		
		$no_image_path = VM_THEMEPATH.'images/'.NO_IMAGE;
		//JFile::copy($no_image_path,JPATH_ROOT.'/components/com_virtuemart/assets/images/vmgeneral/'.NO_IMAGE);
		$this->source_filehelper->CopyFile($src,JPATH_ROOT.'/components/com_virtuemart/assets/images/vmgeneral/'.NO_IMAGE);
		$config->set('no_image_set',NO_IMAGE);
		$config->set('no_image_found',NO_IMAGE);
		
		//Pricing
		$config->set('show_prices',_SHOW_PRICES);
		
		//Checkout
		$config->set('agree_to_tos_onorder',PSHOP_AGREE_TO_TOS_ONORDER);
		$config->set('oncheckout_show_legal_info',VM_ONCHECKOUT_SHOW_LEGALINFO);
		$config->set('oncheckout_show_steps',SHOW_CHECKOUT_BAR);
		$config->set('order_items_status','C');
		
		//Product Order
		switch (VM_BROWSE_ORDERBY_FIELD) {
			case 'product_list': $config->set('browse_orderby_field','pc.ordering'); break;
			case 'product_name': $config->set('browse_orderby_field','product_name'); break;
			case 'product_price': $config->set('browse_orderby_field','product_price'); break;
			case 'product_sku': $config->set('browse_orderby_field','`p`.product_sku'); break;
			case 'product_cdate': $config->set('browse_orderby_field','`p`.created_on'); break;
		}
//		$newOrderbyFields = array();
//		if (in_array('product_name',VM_BROWSE_ORDERBY_FIELDS)) {
//			$newOrderbyFields[] = 'product_name';
//		}
//		if (in_array('product_list',VM_BROWSE_ORDERBY_FIELDS)) {
//			$newOrderbyFields[] = 'pc.ordering';
//		}
//		if (in_array('product_price',VM_BROWSE_ORDERBY_FIELDS)) {
//			$newOrderbyFields[] = 'product_price';
//		}
//		if (in_array('product_cdate',VM_BROWSE_ORDERBY_FIELDS)) {
//			$newOrderbyFields[] = '`p`.created_on';
//		}
//		if (in_array('product_sku',VM_BROWSE_ORDERBY_FIELDS)) {
//			$newOrderbyFields[] = '`p`.product_sku';
//		}
//		$config->set('browse_orderby_fields',$newOrderbyFields);

		//Check how many vendors we have and activate multivendor if necessary	
		$query = $this->source_db->getQuery(true);
		$query->clear()->select('count(*)')
			->from('#__'.$this->vmTablePrefix.'_vendor');
		$count_vendors = $this->source_db->setQuery($query)->loadResult();
		if ($count_vendors > 1) {
			$config->set('multix','admin');
		}
		
		$active_languages=array();
		$active_languages[] = $this->baseLanguage;
		foreach ($this->additionalLanguages as $language) {
			$active_languages[] = $language->lang_code;
		}
		$config->set('active_languages',$active_languages);
		
		$query = $this->destination_db->getQuery(true);
		$query->update('#__virtuemart_configs')->set('config='.$this->destination_db->q($config->toString()))->where('virtuemart_config_id=1');
		$this->destination_db->setQuery($query);
		$this->destination_db->query();
		
		VmConfig::loadConfig();

		$this->logWarning(JText::_('VM_WARNING_CONFIG'));

	}
	
	public function create_languages() {
		//if ($this->joomfishInstalled) {
			if(!class_exists('GenericTableUpdater')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/tableupdater.php');
			$updater = new GenericTableUpdater();
			$active_languages=array();
			$active_languages[] = $this->baseLanguage;
			foreach ($this->additionalLanguages as $language) {
				$active_languages[] = $language->lang_code;
			}
			$result = $updater->createLanguageTables($active_languages);
			$this->logInfo(JText::_('VM_LANGUAGE_TABLE_CREATED'));
		//}
	}

	public function menu_items() {

		$query = $this->destination_db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where("element = 'com_virtuemart'");
		$this->destination_db->setQuery($query);
		$vm_extensionid = $this->destination_db->loadResult();
		if (!$vm_extensionid) {
	        $this->logError('Could not find Virtuemart extension');
		}
		
		$query = $this->destination_db->getQuery(true);
		$query->select('*')
			->from('#__menu')
			->where("client_id = 0")
			->where("link like '%option=com_virtuemart%'");
		$this->destination_db->setQuery($query);
		$menu_items = $this->destination_db->loadObjectList();
		
		//http://platinumwigs.com/index.php?page=shop.browse&category_id=77&option=com_virtuemart&Itemid=70
		//index.php?option=com_virtuemart&view=category&virtuemart_category_id=77
		foreach ($menu_items as $menu_item) {
			$break = false;
			$uri = JURI::getInstance($menu_item->link);
			$new_uri = null;
			$new_uri = JURI::getInstance('index.php?option=com_virtuemart');
			$new_uri->setQuery('option=com_virtuemart');
			$old_params = json_decode($menu_item->params);
			$page = $uri->getVar('page',$old_params->page);

			if (!$page) {
				if ($old_params->category_id && !$old_params->product_id) {
					$page = 'shop.browse';
				} else if ($old_params->product_id) {
					$page = 'shop.product_details';
				}

			}
			$new_params = new StdClass;
			
			if ($page == 'shop.index') {
				$new_uri->setVar('view','virtuemart');
			} else if ($page == 'shop.browse') {	
				$new_uri->setVar('view','category');
				$new_uri->setVar('virtuemart_category_id',$uri->getVar('category_id',$old_params->category_id));
			} else if ($page == 'shop.product_details') {
				$new_uri->setVar('view','productdetails');
				$new_uri->setVar('virtuemart_product_id',$uri->getVar('product_id',$old_params->product_id));
			} else if ($page == 'shop.manufacturer_page') {
				$new_uri->setVar('view','manufacturer');
				$new_uri->setVar('layout','details');
				if ($menu_item->type == 'url') {
					$new_params->virtuemart_manufacturer_id = $uri->getVar('manufacturer_id');
				} else {
					$new_params->virtuemart_manufacturer_id = $old_params->manufacturer_id;
				}
			} else if ($page == 'shop.infopage') {
				$new_uri->setVar('view','vendor');
				$new_uri->setVar('layout','details');
				if ($menu_item->type == 'url') {
					$new_params->virtuemart_vendor_id = $uri->getVar('vendor_id');
				} else {
					$new_params->virtuemart_vendor_id = $old_params->vendor_id;
				}
			} else if ($page == 'shop.tos') {
				$new_uri->setVar('view','vendor');
				$new_uri->setVar('layout','tos');
				//$uri->setVar('virtuemart_vendor_id','1');
			} else if ($page == 'shop.cart') {
				$new_uri->setVar('view','cart');
			} else if ($page == 'account.orders') {
				$new_uri->setVar('view','orders');
				$new_uri->setVar('layout','list');
			} else if ($page == 'account.index') {
				$new_uri->setVar('view','user');
				$new_uri->setVar('layout','edit');
			} else if ($page == 'account.billing') {
				$new_uri->setVar('view','user');
				$new_uri->setVar('layout','editaddress');
			} else {	
				continue;
			}
			
			$menu_item->link 			= $new_uri->toString();
			$menu_item->type 			= 'component';
			$menu_item->component_id	= $vm_extensionid;
			$menu_item->params			= json_encode($new_params);
			
			try {
				$this->destination_db->transactionStart();
				$this->destination_db->updateObject('#__menu', $menu_item, 'id');
				$this->destination_db->transactionCommit();
				$this->logRow($menu_item->id,$menu_item->title);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$menu_item->id);
			}
		}
		
	}

	public function fix_modules() {

		$query = $this->destination_db->getQuery(true);
		$query->select('*')
			->from('#__modules');
		$this->destination_db->setQuery($query);
		$modules = $this->destination_db->loadObjectList();
		foreach ($modules as $module) {

			$skip = false;
			$newParams = new JRegistry();
			$newParams->loadString($module->params, 'JSON');
			switch ($module->module) {
				case 'mod_virtuemart_login':
					$newModule = 'mod_login';
					break;
				case 'mod_product_categories':
					$newModule = 'mod_virtuemart_category';
					break;
				case 'mod_virtuemart_manufacturers':
					$newModule = 'mod_virtuemart_manufacturer';
					$newParams->set('headerText',$newParams->get('text_before'));
					break;
				case 'mod_virtuemart_topten':
					$newModule = 'mod_virtuemart_product';
					$newParams->set('product_group','topten');
					$newParams->set('max_items',$newParams->get('num_topsellers'));
					break;
				case 'mod_virtuemart_latestprod':
					$newModule = 'mod_virtuemart_product';
					$newParams->set('product_group','latest');
					$category_ids = explode(',',$newParams->get('category_id'));
					$newParams->set('virtuemart_category_id',$category_ids[0]);
					break;
				case 'mod_virtuemart_featureprod':
					$newModule = 'mod_virtuemart_product';
					$newParams->set('product_group','featured');
					$category_ids = explode(',',$newParams->get('category_id'));
					$newParams->set('virtuemart_category_id',$category_ids[0]);
					break;
				default:
					$skip = true;
			}

			if ($skip) {
				continue;
			}
			$module->module		= $newModule;
			$module->params		= $newParams->toString('JSON');
			$module->published	= 1;
			
			try {
				$this->destination_db->transactionStart();
				$this->destination_db->updateObject('#__modules', $module, 'id');
				$this->destination_db->transactionCommit();
				$this->logRow($module->id,$module->title);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$module->id);
			}
		}
		$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
		
	}

    public function vm_vendor() {
		
        $pk = 'vendor_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_vendor',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->virtuemart_vendor_id 	= $item['vendor_id'];
			$record->vendor_name 			= $item['vendor_name'];
			$record->vendor_currency		= $this->getCurrencyId($item['vendor_currency']);
			$record->vendor_accepted_currencies = implode(',',$this->getCurrencyIds(explode(',',$item['vendor_accepted_currencies'])));
			
			$vendor_params = 'vendor_min_pov="'.$item['vendor_min_pov'].'"|vendor_min_poq=1|vendor_freeshipment=0|vendor_address_format=""|vendor_date_format=""|vendor_letter_format="A4"|vendor_letter_orientation="P"|vendor_letter_margin_top="45"|vendor_letter_margin_left="25"|vendor_letter_margin_right="25"|vendor_letter_margin_bottom="25"|vendor_letter_margin_header="12"|vendor_letter_margin_footer="20"|vendor_letter_font="helvetica"|vendor_letter_font_size="8"|vendor_letter_header_font_size="7"|vendor_letter_footer_font_size="6"|vendor_letter_header="1"|vendor_letter_header_line="1"|vendor_letter_header_line_color="#000000"|vendor_letter_header_image="1"|vendor_letter_header_imagesize="60"|vendor_letter_header_cell_height_ratio="1"|vendor_letter_footer="1"|vendor_letter_footer_line="1"|vendor_letter_footer_line_color="#000000"|vendor_letter_footer_cell_height_ratio="1"|vendor_letter_add_tos="0"|vendor_letter_add_tos_newpage="1"|';
			$record->vendor_params 			= $vendor_params;

			$record->created_on	 			= $this->TimeToSqlDateTime($item['cdate']);
			$record->modified_on	 		= $this->TimeToSqlDateTime($item['mdate']);

			//Vendor image
			if(!empty($item['vendor_full_image']) || !empty($item['vendor_thumb_image'])){
				$mediaid = $this->importMediaByType($item['vendor_name'],$item['vendor_full_image'],$item['vendor_thumb_image'],'vendor',$item['vendor_id']);
				$record_media = new stdClass();
				$record_media->virtuemart_vendor_id = $record->virtuemart_vendor_id;
				$record_media->virtuemart_media_id = $mediaid;
				$record_media->ordering = 0;
			}

			$record_lang = new stdClass();
			$record_lang->virtuemart_vendor_id 		= $record->virtuemart_vendor_id;
			$record_lang->vendor_store_desc 		= $item['vendor_store_desc'];
			$record_lang->vendor_terms_of_service 	= $item['vendor_terms_of_service'];
			//$record_lang->vendor_legal_info 		= '';
			//$record_lang->vendor_letter_css 		= '';
			$record_lang->vendor_letter_header_html = '<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>';
			$record_lang->vendor_letter_footer_html = '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>';
			$record_lang->vendor_store_name 		= $item['vendor_store_name'];
			$record_lang->vendor_phone 				= $item['vendor_phone'];
			$record_lang->vendor_url 				= $item['vendor_url'];
			$record_lang->metadesc 					= '';
			$record_lang->metakey 					= '';
			$record_lang->customtitle 				= '';
			$slug = JApplication::stringURLSafe($item['vendor_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_vendors_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_vendor_id');
			

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_vendors', $record, 'virtuemart_vendor_id');
				$this->insertOrReplace('#__virtuemart_vendors_'.$this->baseLanguageTable, $record_lang, 'virtuemart_vendor_id');
				if (isset($record_media)) {
					$this->insertOrReplace('#__virtuemart_vendor_medias', $record_media, 'virtuemart_vendor_id');
				}
				$this->logRow($srcid,$item['vendor_name']);
				//Joomfish
				if ($this->joomfishInstalled) {
					//$this->logWarning(print_a(get_object_vars($record_lang),false));
					foreach ($this->additionalLanguages as $language) {
						if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_vendor',$srcid,$language->lang_id)) {
							$tempRecordLang = clone $record_lang;
							foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
								if (array_key_exists($propertyName,$translatedFields)) {
									$tempRecordLang->{$propertyName} = $translatedFields[$propertyName]->translation;
								}
							}
							if ($this->insertOrReplace('#__virtuemart_vendors_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_vendor_id')) {
								$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->vendor_store_name);
							}
						}
					}
				}
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}

		if ($this->moreResults) {
			return true;
		}

  	}
	
	public function vm_tax_rate() {
        $pk = 'tax_rate_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_tax_rate',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];
			
			$taxName = 'Tax '.(floatval($item['tax_rate']) * 100).'%';
			if ($item['tax_country']) {
				$taxName .= ' ('.$item['tax_country'];
				if ($item['tax_state']) {
					$taxName .= ' / '.$item['tax_state'];
				}
				$taxName .= ')';
			}

			$record_calc = new stdClass();
			
			$record_calc->virtuemart_calc_id 	= $item['tax_rate_id'];
			$record_calc->virtuemart_vendor_id 	= $item['vendor_id'];
			$record_calc->calc_name 			= $taxName;
			$record_calc->calc_descr 			= '';
			$record_calc->calc_kind 			= 'Tax';
			$record_calc->calc_value_mathop 	= '+%';
			$record_calc->calc_value 			= 100*floatval($item['tax_rate']);
			$record_calc->calc_currency 		= $this->getVendor($item['vendor_id'])->vendor_currency;
			$record_calc->calc_shopper_published = 1;
			$record_calc->calc_vendor_published = 1;
			$record_calc->published = 1;
			$record_calc->modified_on	 		= $this->TimeToSqlDateTime($item['mdate']);

			if ($item['tax_country']) {
				$record_calc_country = new stdClass();
				$record_calc_country->virtuemart_calc_id = 0;
				$record_calc_country->virtuemart_country_id = $this->getCountryIDByName($item['tax_country']);

				if ($item['tax_state']) {
					$record_calc_state = new stdClass();
					$record_calc_state->virtuemart_calc_id = 0;
					$record_calc_state->virtuemart_state_id = $this->getStateIDByName($item['tax_state'],$item['tax_country']);
				}
			}

			try {
				$this->destination_db->transactionStart();
				$result = $this->insertOrReplace('#__virtuemart_calcs', $record_calc, 'virtuemart_calc_id');
				if ($item['tax_country']) {
					$record_calc_country->virtuemart_calc_id = $record_calc->virtuemart_calc_id;
					$this->insertOrReplace('#__virtuemart_calc_countries', $record_calc_country, 'virtuemart_calc_id');
					if ($item['tax_state']) {
						$record_calc_state->virtuemart_calc_id = $record_calc->virtuemart_calc_id;
						$this->insertOrReplace('#__virtuemart_calc_states', $record_calc_state, 'virtuemart_calc_id');
					}
				}
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$taxName);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}

		if ($this->moreResults) {
			return true;
		}
	}
	
	private function getTaxRate($tax_rate_id) {
		$query = $this->source_db->getQuery(true);
		$query->select('tax_rate')
			->from('#__'.$this->vmTablePrefix.'_tax_rate')
			->where($this->source_db->qn('tax_rate_id').'='.$this->source_db->q($tax_rate_id));
		$tax_rate = $this->source_db->setQuery($query)->loadResult();
		return $tax_rate;
	}
	
	public function vm_payment_method() {

        $pk = 'payment_method_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$where = "";//"payment_enabled = 'Y'";
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_payment_method',$pk,$excludeids,$where);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		$query = $this->destination_db->getQuery(true);

		$known_payment_methods = array();
		$known_payment_methods['ps_payment'] 	= 'standard';
		$known_payment_methods['ps_paypal'] 	= 'paypal';
		$known_payment_methods['ps_paypal_api'] = 'paypal';
		$known_payment_methods['ps_authorize'] 	= 'authorizenet';
		$known_payment_methods['ps_authorize'] 	= 'authorizenet';

        foreach ($items as $i => $item) {

			$srcid = $item[$pk];	//Set the primary key
			$jplugin_id = 0;
			if (array_key_exists($item['payment_class'],$known_payment_methods)) {
				$jplugin_id = $this->getJPluginId('vmpayment',$known_payment_methods[$item['payment_class']]);
			}
	
			$record = new stdClass();
			$record->virtuemart_paymentmethod_id = $srcid;
			$record->virtuemart_vendor_id = $this->getVendorId($item['vendor_id']);
			$record->payment_jplugin_id = $jplugin_id;
			//$record->slug 			= JApplication::stringURLSafe($item['payment_method_name']);
			$record->payment_element= 'standard';
			$record->payment_params = 'payment_logos=""|countries=""|payment_currency=""|status_pending="P"|min_amount=""|max_amount=""|cost_per_transaction=""|cost_percent_total=""|tax_id=""|payment_info=""|';
			$record->shared 		= 0;
			$record->ordering 		= $item['list_order'];
			$record->published 		= ($item['payment_enabled']=='Y') ? 1 : 0;

			$record_lang = new stdClass();
			$record_lang->virtuemart_paymentmethod_id = $srcid;
			$record_lang->payment_name 			= $item['payment_method_name'];
			$record_lang->payment_desc 			= '';
			$slug = JApplication::stringURLSafe($item['payment_method_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_paymentmethods_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_paymentmethod_id');
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_paymentmethods', $record, 'virtuemart_paymentmethod_id');
				$this->insertOrReplace('#__virtuemart_paymentmethods_'.$this->baseLanguageTable, $record_lang, 'virtuemart_paymentmethod_id');

				$query->clear()
					->delete('#__virtuemart_paymentmethod_shoppergroups')
					->where('virtuemart_paymentmethod_id='.$this->destination_db->q($record->virtuemart_paymentmethod_id));
				$this->destination_db->setQuery($query)->query();

				if ($item['shopper_group_id'] && $item['shopper_group_id']!=$this->getDefaultShopperGroupId()) {
					$record_sg = new stdClass();
					$record_sg->virtuemart_paymentmethod_id 	= $record->virtuemart_paymentmethod_id;
					$record_sg->virtuemart_shoppergroup_id	= $item['shopper_group_id'];
					$this->insertOrReplace('#__virtuemart_paymentmethod_shoppergroups', $record_sg, 'id');
				}

				$this->logRow($srcid,$item['payment_method_name']);
				//Joomfish
				foreach ($this->additionalLanguages as $language) {
					if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_payment_method',$srcid,$language->lang_id)) {
						$tempRecordLang = clone $record_lang;
						foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
							$oldPropertyName = $propertyName;
							if ($propertyName == 'payment_name') {$oldPropertyName = 'payment_method_name';}
							if (array_key_exists($oldPropertyName,$translatedFields)) {
								$tempRecordLang->{$propertyName} = $translatedFields[$oldPropertyName]->translation;
							}
						}
						if ($this->insertOrReplace('#__virtuemart_paymentmethods_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_paymentmethod_id')) {
							$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->payment_name);
						}
					}
				}
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

			$this->logWarning(JText::sprintf('VM_WARNING_PAYMENT',$item['payment_method_name']));
		}
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_shipping_rates() {

        $pk = 'shipping_rate_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_shipping_rate',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		//Get the default Weight/Countries Joomla Plugin
		$query = $this->destination_db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('element='.$this->destination_db->q('weight_countries'))
			->where('folder='.$this->destination_db->q('vmshipment'));
		$jplugin_id = $this->destination_db->setQuery($query)->loadResult();
		
		$defaultVendor = $this->getVendor();

        foreach ($items as $i => $item) {
			
			//Get the carrier
			$query = $this->source_db->getQuery(true);
			$query->select('shipping_carrier_name')
				->from('#__'.$this->vmTablePrefix.'_shipping_carrier')
				->where('shipping_carrier_id='.$this->source_db->q($item['shipping_rate_carrier_id']));
			$carrierName = $this->source_db->setQuery($query)->loadResult();

			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->virtuemart_shipmentmethod_id = $srcid;
			$record->virtuemart_vendor_id = $this->getVendorId($defaultVendor->virtuemart_vendor_id);
			$record->shipment_jplugin_id = $jplugin_id;
			$record->shipment_element = 'weight_countries';
			
			$params = array();
			$params['shipment_logos'] 	= '';
			$countryCodes = explode(';',$item['shipping_rate_country']);
			$countryIds = array();
			foreach ($countryCodes as $countryCode) {
				$countryId = $this->getCountryIDByName($countryCode);
				$countryIds[] = '"'.$countryId.'"';
			}
			$params['countries'] 		= (count($countryIds)) ? '['.implode(',',$countryIds).']' : '""';
			$params['zip_start'] 		= '"'.$item['shipping_rate_zip_start'].'"';
			$params['zip_stop'] 		= '"'.$item['shipping_rate_zip_end'].'"';
			$params['weight_start'] 	= '"'.$item['shipping_rate_weight_start'].'"';
			$params['weight_stop'] 		= '"'.$item['shipping_rate_weight_end'].'"';
			$params['weight_unit'] 		= '"KG"';
			$params['nbproducts_start'] = 0;
			$params['nbproducts_stop'] 	= 0;
			$params['orderamount_start']= '""';
			$params['orderamount_stop'] = '""';
			$params['cost'] 			= '"'.$item['shipping_rate_value'].'"';
			$params['package_fee'] 		= '"'.$item['shipping_rate_package_fee'].'"';
			$params['tax_id'] 			= '"'.$item['shipping_rate_vat_id'].'"';
			$params['free_shipment'] 	= '';
			
			foreach ($params as $k=>$v) {
				$paramParts[] = $k.'='.$v;
			}
			$record->shipment_params = implode('|',$paramParts);
			$record->shared 		= 1;
			$record->ordering 		= $item['shipping_rate_list_order'];
			$record->published 		= 1;

			$record_lang = new stdClass();
			$record_lang->virtuemart_shipmentmethod_id = $record->virtuemart_shipmentmethod_id;
			$record_lang->shipment_name = $carrierName;
			$record_lang->shipment_desc = $item['shipping_rate_name'];
			$record_lang->slug 			= JApplication::stringURLSafe($record->virtuemart_shipmentmethod_id.'.'.$carrierName);
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_shipmentmethods', $record, 'virtuemart_shipmentmethod_id');
				$this->insertOrReplace('#__virtuemart_shipmentmethods_'.$this->baseLanguageTable, $record_lang, 'virtuemart_shipmentmethod_id');
				$this->logRow($srcid,$carrierName.' - '.$item['shipping_rate_name']);
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_shipment_method() {

		if (!$this->loadOldConfig()) {
	        $this->logError('Could not read configuration file');
			return false;
		}
		
		

		$items = $PSHOP_SHIPPING_MODULES;
		$this->logDebug($items);
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		$jplugin_id = $this->getJPluginId('vmshipment','weight_countries');
		
		$defaultVendor = $this->getVendor();
		
        foreach ($items as $i => $item) {

			$srcid = $item;	//Set the primary key

			$record = new stdClass();
			$record->virtuemart_shipmentmethod_id = null;
			$record->virtuemart_vendor_id = $this->getVendorId($defaultVendor->virtuemart_vendor_id);
			$record->shipment_jplugin_id = $jplugin_id;
			$record->shipment_element= $item;
			$record->shipment_params = '';
			$record->shared 		= 1;
			$record->ordering 		= 0;
			$record->published 		= 1;

			$record_lang = new stdClass();
			$record_lang->shipment_name 			= $item;
			$record_lang->shipment_desc 			= '';
			$record_lang->slug 					= JApplication::stringURLSafe($item);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_shipmentmethods', $record, 'shipment_element');
				$virtuemart_shipmentmethod_id = $this->getShipmentMethodId($srcid);
				$record_lang->virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id;
				$record_lang->slug = $this->checkSlug('#__virtuemart_shipmentmethods_'.$this->baseLanguageTable,$record_lang->slug,'slug',$virtuemart_shipmentmethod_id,'virtuemart_shipmentmethod_id');
				$this->insertOrReplace('#__virtuemart_shipmentmethods_'.$this->baseLanguageTable, $record_lang, 'virtuemart_shipmentmethod_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

			$this->logWarning(JText::sprintf('VM_WARNING_SHIPMENT',$item));
		}
	}
	
	private function getDefaultShopperGroupId() {
		$query = $this->source_db->getQuery(true);
		$query->select('shopper_group_id')
			->from('#__'.$this->vmTablePrefix.'_shopper_group')
			->where($this->source_db->qn('default').'='.$this->source_db->q('1'));
		$shopper_group_id = $this->source_db->setQuery($query)->loadResult();
		if (!$shopper_group_id) {
			$shopper_group_id = 5;
		}
		return $shopper_group_id;
	}
	
	private function ShowPriceIncludingTax($shopper_group_id) {
		$query = $this->source_db->getQuery(true);
		$query->select('show_price_including_tax')
			->from('#__'.$this->vmTablePrefix.'_shopper_group')
			->where($this->source_db->qn('shopper_group_id').'='.$this->source_db->q($shopper_group_id));
			
		$show_price_including_tax = $this->source_db->setQuery($query)->loadResult();
		return ($show_price_including_tax) ? true : false;
	}
	
    public function vm_shopper_group() {
        $pk = 'shopper_group_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_shopper_group',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			//if ($item['shopper_group_name'] == '-default-') {
			//	continue;
			//}
			
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->virtuemart_shoppergroup_id = $srcid;
			$record->virtuemart_vendor_id = $this->getVendorId($item['vendor_id']);
			$record->shopper_group_name = $item['shopper_group_name'];
			$record->shopper_group_desc = $item['shopper_group_desc'];
			$record->default = $item['default'];
			$record->published = 1;
			
			$vendor = $this->getVendor($item['vendor_id']);
			
			if ($item['shopper_group_discount'] && $item['shopper_group_discount'] > 0) {
				$record_calc = new stdClass();
				$record_calc->virtuemart_vendor_id 	= $this->getVendorId($item['vendor_id']);
				$record_calc->calc_name 			= $item['shopper_group_name'];
				$record_calc->calc_descr 			= $item['shopper_group_name'];
				$record_calc->calc_kind 			= 'DBTaxBill';
				$record_calc->calc_value_mathop 	= '-%';
				$record_calc->calc_value 			= floatval($item['shopper_group_discount']);
				$record_calc->calc_currency 		= $vendor->vendor_currency;
				$record_calc->calc_shopper_published = 1;
				$record_calc->calc_vendor_published = 1;
				$record_calc->published = 1;

				$record_calc_sg = new stdClass();
				$record_calc_sg->virtuemart_calc_id = 0;
				$record_calc_sg->virtuemart_shoppergroup_id = $record->virtuemart_shoppergroup_id;
			}

			try {
				$this->destination_db->transactionStart();
				$result = $this->insertOrReplace('#__virtuemart_shoppergroups', $record, 'virtuemart_shoppergroup_id');
				if (isset($record_calc) && $result=='insert') {
					$this->insertOrReplace('#__virtuemart_calcs', $record_calc, 'virtuemart_calc_id');
					$record_calc_sg->virtuemart_calc_id = $record_calc->virtuemart_calc_id;
					$this->insertOrReplace('#__virtuemart_calc_shoppergroups', $record_calc_sg, 'virtuemart_calc_id');
				}
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['shopper_group_name']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		//Ensure only one default shopper group is set
		//if ($newDefaultId) {
		//	$shoppergroupModel->makeDefault($newDefaultId);
		//}
		if ($this->moreResults) {
			return true;
		}
    }
	
	public function vm_category() {

		if ($this->source_filehelper->mode == 'ftp') {
			$this->limit = 5;
		}
		
        $pk = 'category_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_category',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->virtuemart_category_id = $srcid;
			$record->virtuemart_vendor_id = $this->getVendorId($item['vendor_id']);
			$record->category_template = 0;
			$record->category_layout = 0;
			$record->category_product_layout = 0;
			$record->products_per_row = ($item['products_per_row']==VmConfig::get('products_per_row',3)) ? 0 : $item['products_per_row'];
			$record->limit_list_step = 10;
			$record->limit_list_initial = 0;
			$record->ordering = $item['list_order'];
			$record->published = $item['category_publish'] == 'Y' ? 1 : 0;
			$record->created_on = $this->TimeToSqlDateTime($item['cdate']);
			$record->modified_on = $this->TimeToSqlDateTime($item['mdate']);

			if(!empty($item['category_full_image']) || !empty($item['category_thumb_image'])){
				$this->logDebug($item,'Category info');
				$mediaid = $this->importMediaByType($item['category_name'],$item['category_full_image'],$item['category_thumb_image'],'category',$srcid,$item['category_name']);
				$record_media = new stdClass();
				$record_media->virtuemart_category_id = $record->virtuemart_category_id;
				$record_media->virtuemart_media_id = $mediaid;
				$record_media->ordering = 0;
			}

			$record_lang = new stdClass();
			$record_lang->virtuemart_category_id = $record->virtuemart_category_id;
			$record_lang->category_name = $item['category_name'];
			$record_lang->category_description = $item['category_description'];
			$record_lang->metadesc = '';
			$record_lang->metakey = '';
			$record_lang->customtitle = '';
			$slug = JApplication::stringURLSafe($item['category_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_categories_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_category_id');
			
			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__'.$this->vmTablePrefix.'_category_xref')
				->where('category_child_id = '.$srcid); 
			$this->source_db->setQuery($query);
			$oldHhierarchy = $this->source_db->loadObject();
			$record_hierarchy = new stdClass();
			$record_hierarchy->category_parent_id = $oldHhierarchy->category_parent_id;
			$record_hierarchy->category_child_id = $srcid;
			$record_hierarchy->ordering = 0;
			$record_hierarchy = array($record_hierarchy);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_categories', $record, 'virtuemart_category_id');
				$this->insertOrReplace('#__virtuemart_categories_'.$this->baseLanguageTable, $record_lang, 'virtuemart_category_id');
				$this->insertOrReplace('#__virtuemart_category_categories', $record_hierarchy, 'category_child_id');
				if (isset($record_media)) {
					$this->insertOrReplace('#__virtuemart_category_medias', $record_media, 'virtuemart_category_id');
					$this->logRow($srcid,$item['category_name'].','.JText::sprintf('VM_INFO_MEDIAS_X',count($record_media)));
				} else {
					$this->logRow($srcid,$item['category_name'].','.JText::sprintf('VM_INFO_MEDIAS_X',0));
				}

				//Joomfish
				foreach ($this->additionalLanguages as $language) {
					if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_category',$srcid,$language->lang_id)) {
						$tempRecordLang = clone $record_lang;
						foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
							$oldPropertyName = $propertyName;
							if (array_key_exists($oldPropertyName,$translatedFields)) {
								$tempRecordLang->{$propertyName} = $translatedFields[$oldPropertyName]->translation;
							}
						}
						if ($this->insertOrReplace('#__virtuemart_categories_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_category_id')) {
							$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->category_name);
						}
					}
				}

				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		} else {
			$this->cleanCache('com_virtuemart_cats');
		}
	}
	
	public function vm_manufacturer_category() {

        $pk = 'mf_category_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_manufacturer_category',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->virtuemart_manufacturercategories_id = $srcid;
			$record->published = 1;

			$record_lang = new stdClass();
			$record_lang->virtuemart_manufacturercategories_id = $record->virtuemart_manufacturercategories_id;
			$record_lang->mf_category_name = $item['mf_category_name'];
			$record_lang->mf_category_desc = $item['mf_category_desc'];
			$slug = JApplication::stringURLSafe($item['mf_category_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_manufacturercategories_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_manufacturercategories_id');

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_manufacturercategories', $record, 'virtuemart_manufacturercategories_id');
				$record_lang->virtuemart_manufacturercategories_id = $record->virtuemart_manufacturercategories_id;
				$this->insertOrReplace('#__virtuemart_manufacturercategories_'.$this->baseLanguageTable, $record_lang, 'virtuemart_manufacturercategories_id');

				$this->logRow($srcid,$item['mf_category_name']);
				//Joomfish
				foreach ($this->additionalLanguages as $language) {
					if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_manufacturer_category',$srcid,$language->lang_id)) {
						$tempRecordLang = clone $record_lang;
						foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
							$oldPropertyName = $propertyName;
							if (array_key_exists($oldPropertyName,$translatedFields)) {
								$tempRecordLang->{$propertyName} = $translatedFields[$oldPropertyName]->translation;
							}
						}
						if ($this->insertOrReplace('#__virtuemart_manufacturercategories_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_manufacturercategories_id')) {
							$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->mf_category_name);
						}
					}
				}

				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_manufacturer() {

        $pk = 'manufacturer_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_manufacturer',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		VMMigrateHelperDatabase::AlterColumnIfExists('#__virtuemart_manufacturers','virtuemart_manufacturer_id','virtuemart_manufacturer_id','INT(4) UNSIGNED NOT NULL AUTO_INCREMENT');

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->virtuemart_manufacturer_id = $srcid;
			$record->published = 1;
			$record->virtuemart_manufacturercategories_id = $item['mf_category_id'];

			$record_lang = new stdClass();
			$record_lang->virtuemart_manufacturer_id = $manufacturer->virtuemart_manufacturer_id;
			$record_lang->mf_name = $item['mf_name'];
			$record_lang->mf_email = $item['mf_email'];
			$record_lang->mf_desc = $item['mf_desc'];
			$record_lang->mf_url = $item['mf_url'];
			$slug = JApplication::stringURLSafe($item['mf_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_manufacturers_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_manufacturer_id');
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_manufacturers', $record, 'virtuemart_manufacturer_id');
				$record_lang->virtuemart_manufacturer_id = $record->virtuemart_manufacturer_id;
				$this->insertOrReplace('#__virtuemart_manufacturers_'.$this->baseLanguageTable, $record_lang, 'virtuemart_manufacturer_id');

				$this->logRow($srcid,$item['mf_name']);
				//Joomfish
				foreach ($this->additionalLanguages as $language) {
					if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_manufacturer',$srcid,$language->lang_id)) {
						$tempRecordLang = clone $record_lang;
						foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
							$oldPropertyName = $propertyName;
							if (array_key_exists($oldPropertyName,$translatedFields)) {
								$tempRecordLang->{$propertyName} = $translatedFields[$oldPropertyName]->translation;
							}
						}
						if ($this->insertOrReplace('#__virtuemart_manufacturers_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_manufacturer_id')) {
							$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->mf_name);
						}
					}
				}

				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_order_status() {
        $pk = 'order_status_code';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$where = "order_status_code NOT IN ('S','R','X','C','P')";
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_order_status',$pk,$excludeids,$where);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$srccode = $item['order_status_code'];	
			
			$record = new stdClass();
			//$record->virtuemart_orderstate_id = null;
			$record->virtuemart_vendor_id = $this->getVendorId($item['vendor_id']);
			$record->order_status_code = $item['order_status_code'];
			$record->order_status_name = $item['order_status_name'];
			$record->order_status_description = $item['order_status_description'];
			$record->order_stock_handle = 'R';
			$record->ordering = $item['list_order'];
			$record->published = 1;

			try {
				$this->destination_db->transactionStart();
				
				$this->insertOrReplace('#__virtuemart_orderstates', $record, 'order_status_code');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['order_status_name']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_userfield() {

		if (!$this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_userfield')) {	
			$this->logWarning(JText::_('NOT_YET_IMPLEMENTED'));
			return;
		}
		
        $pk = 'fieldid';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		//$where = 'sys <> 1';
		$where = '(sys <> 1 OR name like "bank%")';
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_userfield',$pk,$excludeids,$where);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			$srccode = $item['name'];	
			
			$record = new stdClass();
			//$record->virtuemart_userfield_id = 0;
			$record->virtuemart_vendor_id = $this->getVendorId($item['vendor_id']);
			$record->userfield_jplugin_id = 0;
			$record->name = $item['name'];
			$title = $item['title'];
			//Transform some labels that were updated in VM2
			if (strpos($title,'PHPSHOP_')===0) {
				$title = str_replace('PHPSHOP_','COM_VIRTUEMART_',$title);
			}
			$record->title = $title;
			$record->description = $item['description'];
			$record->type = $item['type'];
			$record->maxlength = $item['maxlength'];
			$record->size = $item['size'];
			$record->required = $item['required'];
			$record->cols = $item['cols'];
			$record->rows = $item['rows'];
			$record->value = $item['value'];
			$record->default = $item['default'];
			$record->published = $item['published'];
			$record->registration = $item['registration'];
			$record->shipment = $item['shipping'];
			$record->account = $item['account'];
			$record->readonly = $item['readonly'];
			$record->calculated = $item['calculated'];
			$record->sys = $item['sys'];
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$record->params = $item['params'];
			} else {
				$record->userfield_params = $item['params'];
			}
			$record->ordering = $item['ordering']+100;
			$record->shared = 0;
			
			$record_values = new stdClass();
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_userfields', $record, 'name');
				if ($record->type != 'delimiter') {
					$type = $this->formatFieldType($record->type);
					VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_userinfos',$srccode,$type);
					VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_order_userinfos',$srccode,$type);
					VMMigrateHelperDatabase::AlterColumnIfExists('#__virtuemart_userinfos',$srccode,$srccode,$type);
					VMMigrateHelperDatabase::AlterColumnIfExists('#__virtuemart_order_userinfos',$srccode,$srccode,$type);
					$this->insert_field_values($item['fieldid'],$record);
				}
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['title']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_shopper_vendor_xref() {
        $pk = 'user_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_shopper_vendor_xref',$pk,$excludeids,'user_id > 0');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
			$this->fixUserIdZero();
            return false;
        }
		
		$query = $this->source_db->getQuery(true);
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			//Get the name from the Joomla users table
			$query->clear()->select('username, name, email')
				->from('#__users')
				->where('id='.$this->source_db->q($item['user_id']));
			$this->source_db->setQuery($query);
			$joomla_user = $this->source_db->loadObject();
			$username = $joomla_user->username;
			$user_name = $joomla_user->name;
			$user_email = $joomla_user->email;
			
			if (!$username) {
				//User was not found in Joomla!
				$query->clear()->select('count(*)')
					->from('#__'.$this->vmTablePrefix.'_orders')
					->where('user_id='.$this->source_db->q($item['user_id']));
				$found_orders = $this->source_db->setQuery($query)->loadResult();
				if (!$found_orders) {
					//The has no order so we can skip him
					$this->logWarning(JText::sprintf('VM_WARNING_NULL_USER',$srcid),$srcid);
					continue;
				}
				//The user was not found in Joomla but has orders
				$this->logWarning(JText::sprintf('VM_WARNING_MISSING_USER',$srcid));
			}
			
			
			//Check if user is a vendor based on his email address
			$query->clear()->select('count(*)')
				->from('#__'.$this->vmTablePrefix.'_vendor')
				->where('contact_email='.$this->source_db->q($user_email));
			$found_vendor = $this->source_db->setQuery($query)->loadResult();
			if ($found_vendor) {
				$user_is_vendor = 1;
			} else {
				$user_is_vendor = 0;
			}
			
			//Create the vm user record	
			$record_user = new stdClass(); //virtuemart_vmusers
			$record_user->virtuemart_user_id 			= $item['user_id'];
			$record_user->virtuemart_vendor_id 			= ($user_is_vendor) ? $this->getVendorId($item['vendor_id']) : 0;
			$record_user->user_is_vendor 				= $user_is_vendor;
			$record_user->customer_number 				= $item['customer_number'];
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$record_user->perms 					= ($user_is_vendor) ? 'admin' : $item['perms'];
			}
			$record_user->virtuemart_paymentmethod_id 	= 0;
			$record_user->virtuemart_shipmentmethod_id 	= 0;
			$record_user->agreed = 0;

			//Assign user to shopper group (only one possible in Virtuemart 1.1)
			$record_user_sg = new stdClass();
			$record_user_sg->virtuemart_user_id 		= $item['user_id'];
			$record_user_sg->virtuemart_shoppergroup_id = $item['shopper_group_id'];

			//Get the user addresses
			$query->clear()->select('*')
				->from('#__'.$this->vmTablePrefix.'_user_info')
				->where('user_id='.$this->source_db->q($item['user_id']))
				->order('address_type ASC');
			$this->source_db->setQuery($query);
			$addresses = $this->source_db->loadObjectList();
			
			$user_addresses = array();
			$i=0;
			foreach ($addresses as $addresse) {
				if ($i==0) {
					//Get value from the first record (BT: Bill to address)
					if (version_compare($this->getVmVersion(),'2.9','<')) {
						$record_user->perms = ($addresse->perms) ? $addresse->perms : 'shopper';
					}
					$i++;
				}
				$record_user_info = new stdClass();
				//$record->virtuemart_orderstate_id = null;
				//$record_user_info->user_info_id = $item['user_info_id'];
				$record_user_info->virtuemart_user_id 	= $item['user_id'];
				$record_user_info->address_type 		= $addresse->address_type;
				$record_user_info->address_type_name 	= $addresse->address_type_name;
				if (VMMigrateHelperDatabase::columnExists($this->destination_db,'#__virtuemart_userinfos','name')) {
					$record_user_info->name 			= $user_name;
				} elseif (VMMigrateHelperDatabase::columnExists($this->destination_db,'#__virtuemart_userinfos','username')) {
					$record_user_info->username 		= $user_name;
				}
				$record_user_info->title 				= $addresse->title;
				$record_user_info->last_name 			= $addresse->last_name;
				$record_user_info->first_name 			= $addresse->first_name;
				$record_user_info->middle_name 			= $addresse->middle_name;
				$record_user_info->phone_1 				= $addresse->phone_1;
				$record_user_info->phone_2 				= $addresse->phone_2;
				$record_user_info->fax 					= $addresse->fax;
				$record_user_info->address_1 			= $addresse->address_1;
				$record_user_info->address_2 			= $addresse->address_2;
				$record_user_info->city 				= $addresse->city;
				$record_user_info->virtuemart_state_id 	= $this->getStateIDByName($addresse->state,$addresse->country);
				$record_user_info->virtuemart_country_id = $this->getCountryIDByName($addresse->country);
				$record_user_info->zip 					= $addresse->zip;

				$record_user_info->company				= $addresse->company;
				
				if ($this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_userfield')) {	
					$query->clear()->select('*')
						->from('#__'.$this->vmTablePrefix.'_userfield')
						->where('(sys<>1 OR name like "bank%")');
					$this->source_db->setQuery($query);
					$custom_fields = $this->source_db->loadObjectList();
					foreach ($custom_fields as $custom_field) {
						if ($custom_field->name && isset($addresse->{$custom_field->name})) {
							$record_user_info->{$custom_field->name} = $addresse->{$custom_field->name}; //$item[$addresse->name];
						}
					}
				}
				$user_addresses[] = $record_user_info;
			}
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_vmusers', $record_user, 'virtuemart_user_id');
				$record_user_sg->virtuemart_user_id 		= $record_user->virtuemart_user_id;
				$this->insertOrReplace('#__virtuemart_vmuser_shoppergroups', $record_user_sg, 'virtuemart_user_id');
				$this->insertOrReplace('#__virtuemart_userinfos', $user_addresses, 'virtuemart_user_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$user_name);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		} else {
			$this->fixUserIdZero();
			$this->assignDefaultVendor();
		}
	}
	
	private function fixUserIdZero() {
		$query = $this->destination_db->getQuery(true);
		$query->delete('#__virtuemart_userinfos')
			->where('virtuemart_user_id=0');
		$this->destination_db->setQuery($query)->query();
	}
	
	private function assignDefaultVendor() {
		//Check if a vendor was defined. If not, set the current user id as vendor.
		$query = $this->destination_db->getQuery(true);
		$query->select('count(*)')
			->from('#__virtuemart_vmusers')
			->where('user_is_vendor=1');
		$found_vendor = $this->destination_db->setQuery($query)->loadResult();
		if (!$found_vendor) {
			$found_user = 0;
//			if (!$found_user) {
//				//Let's try to find a user with admin perms
//				$query->clear()
//					->select('virtuemart_user_id')
//					->from('#__virtuemart_vmusers')
//					->where('perms = '.$this->destination_db->q('admin'));
//				$found_user = $this->destination_db->setQuery($query)->loadResult();
//			}
//			if (!$found_user) {
//				//Let's try to find a user with storeadmin perms
//				$query->clear()
//					->select('virtuemart_user_id')
//					->from('#__virtuemart_vmusers')
//					->where('perms = '.$this->destination_db->q('storeadmin'));
//				$found_user = $this->destination_db->setQuery($query)->loadResult();
//			}
//			if (!$found_user) {
				//Still no found, let's try with the current admin
				$query->clear()
					->select('virtuemart_user_id')
					->from('#__virtuemart_vmusers')
					->where('virtuemart_user_id = '.$this->destination_db->q(JFactory::getUser()->id));
				$found_user = $this->destination_db->setQuery($query)->loadResult();
//			}
			if ($found_user) {
				$query->clear();
				$query->update('#__virtuemart_vmusers');
				$query->set('user_is_vendor=1');
				if (version_compare($this->getVmVersion(),'2.9','<')) {
					$query->set("perms='admin'");
				}
				$query->set('virtuemart_vendor_id=1');
				$query->where('virtuemart_user_id = '.$this->destination_db->q($found_user));
				if ($this->destination_db->setQuery($query)->query()) {
					$this->logInfo(JText::sprintf('VM_VENDOR_X_ASSIGNED',$found_user));
				} 
			}
		}
	}
	
	public function vm_product() {

        $pk = 'product_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product',$pk,$excludeids,'','product_parent_id ASC');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass(); //virtuemart_vmusers
			$record->virtuemart_product_id 	= $item['product_id'];
			$record->virtuemart_vendor_id 	= $this->getVendorId($item['vendor_id']);
			$record->product_parent_id 		= $item['product_parent_id'];
			$record->product_sku 			= $item['product_sku'];
			$record->product_weight 		= $item['product_weight'];
			$record->product_weight_uom 	= $this->parseUom($item['product_weight_uom']);
			$record->product_length 		= $item['product_length'];
			$record->product_width 			= $item['product_width'];
			$record->product_height 		= $item['product_height'];
			$record->product_lwh_uom 		= $this->parseUom($item['product_lwh_uom']);
			$record->product_url 			= $item['product_url'];
			$record->product_in_stock 		= $item['product_in_stock'];
			$record->product_ordered 		= $this->getNbOrderedItems($item['product_id']);
			$record->low_stock_notification	= 0; 
			$record->product_available_date	= $this->TimeToSqlDateTime($item['product_available_date']);
			$record->product_availability	= $item['product_availability']; //Todo: parse new avail image
			$record->product_special		= ($item['product_special']=='Y') ? 1 : 0;
			$record->product_sales 			= $item['product_sales'];
			$record->product_unit 			= ($item['product_packaging']) ? $this->parseUom($item['product_unit']) : '';
			$record->product_packaging 		= $item['product_packaging'];
			$record->product_params 		= $this->getProductParams($item);
			$record->hits 					= 0;
			$record->intnotes				= '';
			$record->metarobot				= '';
			$record->metaauthor				= '';
			$record->layout					= 0;
			$record->published				= ($item['product_publish']=='Y') ? 1 : 0;
			$record->pordering				= 0;
			$record->created_on				= $this->TimeToSqlDateTime($item['cdate']);
			$record->modified_on			= $this->TimeToSqlDateTime($item['mdate']);

			$record_lang = new stdClass(); 
			$record_lang->virtuemart_product_id 	= $item['product_id'];
			$record_lang->product_s_desc		 	= $item['product_s_desc'];
			$record_lang->product_desc			 	= $item['product_desc'];
			$record_lang->product_name			 	= htmlspecialchars(html_entity_decode($item['product_name'], ENT_QUOTES, "UTF-8"), ENT_QUOTES, "UTF-8");
			$record_lang->metadesc				 	= '';
			$record_lang->metakey				 	= '';
			$record_lang->customtitle			 	= '';
			$slug = JApplication::stringURLSafe($item['product_name']);
			$record_lang->slug = $this->checkSlug('#__virtuemart_products_'.$this->baseLanguageTable,$slug,'slug',$srcid,'virtuemart_product_id',$item['product_sku']);
			//$record_lang->slug					 	= $this->getProductSlug($item);
			$this->logDebug($record_lang,'Language record');

			//Categories
			$query = $this->source_db->getQuery(true);
			$query->select('distinct category_id as virtuemart_category_id,product_id as virtuemart_product_id, product_list as ordering')
				->from('#__'.$this->vmTablePrefix.'_product_category_xref')
				->where('product_id='.$this->destination_db->q($item['product_id']))
				->where('category_id <> 0');
			$this->source_db->setQuery($query);
			$record_categories = $this->source_db->loadObjectList();
			//$record_categories = array($record_categories); //Place it in an array so it gets deleted before inserting again
			
			//Manufacturer
			$query = $this->source_db->getQuery(true);
			$query->select('distinct manufacturer_id as virtuemart_manufacturer_id,product_id as virtuemart_product_id')
				->from('#__'.$this->vmTablePrefix.'_product_mf_xref')
				->where('product_id='.$this->destination_db->q($item['product_id']));
			$this->source_db->setQuery($query);
			$record_manufacturers = $this->source_db->loadObjectList();
			//$record_manufacturers = array($record_manufacturers); //Place it in an array so it gets deleted before inserting again

			//Related Products
			$query = $this->source_db->getQuery(true);
			$query->select('related_products')
				->from('#__'.$this->vmTablePrefix.'_product_relations')
				->where('product_id='.$this->destination_db->q($item['product_id']));
			$this->source_db->setQuery($query);
			$related_products = explode('|',$this->source_db->loadResult());
			$records_related = array();
			foreach ($related_products as $rel_product_id) {
				if (!$rel_product_id) {continue;};
				$record_related = new stdClass();
				$record_related->virtuemart_product_id = $item['product_id'];
				$record_related->virtuemart_custom_id = 1;
				if (version_compare($this->getVmVersion(),'2.9','<')) {
					$record_related->custom_value 		= $rel_product_id;
				} else {
					$record_related->customfield_value	= $rel_product_id;
				}
				$records_related[] = $record_related;
			}
			
			//Prices
			$query->clear()
				->select('*')
				->from('#__'.$this->vmTablePrefix.'_product_price')
				->where('product_id='.$this->source_db->q($item['product_id']));
			$prices = $this->source_db->setQuery($query)->loadObjectList();
			$product_prices = array();
			foreach ($prices as $price) {
				$record_price = new stdClass();
				$record_price->virtuemart_product_id 		= $item['product_id'];
				$record_price->virtuemart_shoppergroup_id 	= ($price->shopper_group_id && $price->shopper_group_id!=$this->getDefaultShopperGroupId()) ? $price->shopper_group_id : 0;
				$record_price->product_price			 	= $price->product_price;
				if ($item['product_discount_id']) {
					
					$query = $this->source_db->getQuery(true);
					$query->select('*')
						->from('#__'.$this->vmTablePrefix.'_product_discount')
						->where('discount_id='.$this->destination_db->q($item['product_discount_id']))
						->where('(start_date <= now() or start_date=0)')
						->where('(end_date >= now() or end_date=0)');
					$this->source_db->setQuery($query);
					$discount = $this->source_db->loadObject();

					if ($discount) {
						$showPriceIncludingTax = $this->ShowPriceIncludingTax($price->shopper_group_id);
						if ($showPriceIncludingTax) {
							if ($item['product_tax_id']) {
								$taxRate = $this->getTaxRate($item['product_tax_id']);
								$productFinalPrice = $record_price->product_price * (1+$taxRate);
							} else {
								$productFinalPrice = $record_price->product_price;
							}
							//We need to calculate the final price (with tax) and deduct the discount.
							//This will be the new final price.
							$record_price->override					= 1; //The discounted prices are calculated AFTER tax => Overwrite final
						} else {
							$productFinalPrice = $record_price->product_price;
							$record_price->override					= -1; //The discounted prices are calculated BEFORE tax => Overwrite price to be taxed
						}
						if ($discount->is_percent) {
							$record_price->product_override_price	= $productFinalPrice * (1-($discount->amount/100));
						} else {
							$record_price->product_override_price	= $productFinalPrice - $discount->amount;
						}
					} else {
						//Discount is expired
						$record_price->override					= 0;
						$record_price->product_override_price	= 0;
					}
				} else {
					$record_price->override					= 0;
					$record_price->product_override_price	= 0;
				}
				//In case the override price turns out to be negative, set it to zero
				if ($record_price->product_override_price <= 0 && $record_price->product_price == 0) {
					//If the main price is zero as well, do not override, this is not necessary
					$record_price->override = 0;
					$record_price->product_override_price = 0;
				} else if ($record_price->product_override_price <= 0) {
					$record_price->product_override_price = 0;
				}
				if ($item['product_tax_id']) {
					$record_price->product_tax_id			 	= $item['product_tax_id'];
				} else {
					$record_price->product_tax_id			 	= -1;
				}
				//$record_price->product_tax_id			 	= $item['product_price'];
				//$record_price->product_discount_id			= $item['product_price'];
				$record_price->product_currency			 	= $this->getCurrencyId($price->product_currency);
				$record_price->product_price_publish_up		= $this->TimeToSqlDateTime($price->product_price_vdate);
				$record_price->product_price_publish_down	= $this->TimeToSqlDateTime($price->product_price_edate);
				$record_price->price_quantity_start			= $price->price_quantity_start;
				$record_price->price_quantity_end			= $price->price_quantity_end;
				$record_price->created_on			 		= $this->TimeToSqlDateTime($price->cdate);
				$record_price->modified_on			 		= $this->TimeToSqlDateTime($price->mdate);
				$product_prices[] = $record_price;
			}
			
			try {
				$this->destination_db->transactionStart();
				//delete the product custom fields before inserting them again. Do not remove related categories if ever they were already defined
				$this->insertOrReplace('#__virtuemart_products', $record, 'virtuemart_product_id');
				$this->deleteRows('#__virtuemart_product_customfields',"virtuemart_product_id='".$item['product_id']."' AND virtuemart_custom_id<>1");
				$this->deleteRows('#__virtuemart_product_categories',"virtuemart_product_id='".$item['product_id']."'");
				$this->deleteRows('#__virtuemart_product_manufacturers',"virtuemart_product_id='".$item['product_id']."'");
				//Attributes are cart attributes to be selected by the customer when adding to cart
				$ordering = 0;
				$this->processProductTypes($item);
				$this->processProductAttributes($item,$product_prices,$ordering);
				//Custom attributes are free fields to be filled in by customer when adding to cart
				$this->processProductCustomAttributes($item,$ordering);
				//Product attributes are variant options of the child products. Customer selects a child to be added
				$this->processProductProductAttributes($item,$record_lang);
				$this->insertOrReplace('#__virtuemart_products_'.$this->baseLanguageTable, $record_lang, 'virtuemart_product_id');
				if (count($record_categories)) {
					$this->insertOrReplace('#__virtuemart_product_categories', $record_categories, 'virtuemart_product_id');
				}
				if (count($record_manufacturers)) {
					$this->insertOrReplace('#__virtuemart_product_manufacturers', $record_manufacturers, 'virtuemart_product_id');
				}
				if (count($records_related)) {
					//$this->deleteRows('#__virtuemart_product_customfields',"virtuemart_product_id='".$item['product_id']."' AND virtuemart_custom_id=1");
					foreach ($records_related as $related_product) {
						$this->destination_db->insertObject('#__virtuemart_product_customfields', $related_product, 'virtuemart_customfield_id');
					}
					//$this->insertOrReplace('#__virtuemart_product_relations', $records_related, 'virtuemart_product_id');
				}
				if (count($product_prices)) {
					$this->insertOrReplace('#__virtuemart_product_prices', $product_prices, 'virtuemart_product_id');
				}

				$this->logRow($srcid,$item['product_name'].' ('.$item['product_sku'].')');
				//Joomfish
				foreach ($this->additionalLanguages as $language) {
					if ($translatedFields = $this->getTranslations($this->vmTablePrefix.'_product',$srcid,$language->lang_id)) {
						$tempRecordLang = clone $record_lang;
						foreach (get_object_vars($record_lang) as $propertyName=>$propertyValue) {
							$oldPropertyName = $propertyName;
							if (array_key_exists($oldPropertyName,$translatedFields)) {
								$tempRecordLang->{$propertyName} = $translatedFields[$oldPropertyName]->translation;
							}
						}
						if ($this->insertOrReplace('#__virtuemart_products_'.$this->getLanguageTableSuffix($language->lang_code), $tempRecordLang, 'virtuemart_product_id')) {
							$this->logTranslation($srcid,$language->lang_code,$tempRecordLang->product_name);
						}
					}
				}

				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_product_stock() {
        $pk = 'product_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product',$pk,$excludeids,'','product_parent_id ASC');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record->virtuemart_product_id	= $srcid;
			$record->product_in_stock 		= $item['product_in_stock'];
			$record->product_ordered 		= $this->getNbOrderedItems($item['product_id']);
			$record->product_available_date	= $this->TimeToSqlDateTime($item['product_available_date']);
			$record->product_availability	= $item['product_availability']; //Todo: parse new avail image
			$record->product_sales 			= $item['product_sales'];

			try {
				$this->destination_db->transactionStart();
				$this->destination_db->updateObject('#__virtuemart_products', $record, 'virtuemart_product_id');
				$this->logRow($srcid,$item['product_name'].' ('.$item['product_sku'].')');
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_product_medias() {

		if ($this->source_filehelper->mode == 'ftp') {
			$this->limit = 1;
		}

        $pk = 'product_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product',$pk,$excludeids,'','product_parent_id ASC');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		$query = $this->source_db->getQuery(true);
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			//Images
			$medias = array();
			//Default image
			$i=0;
			if(!empty($item['product_full_image']) || !empty($item['product_thumb_image'])){
				$mediaid = $this->importMediaByType($item['product_name'],$item['product_full_image'],$item['product_thumb_image'],'product',$srcid);
				$record_media = new stdClass();
				$record_media->virtuemart_product_id = $item['product_id'];
				$record_media->virtuemart_media_id = $mediaid;
				$record_media->ordering = 0;
				$medias[] = $record_media;
				$i++;
			}
			
			//Other images and downloadable. Exclude the media for sale (unpublished and not an image) 
			$query->clear()->select('*')
				->from('#__'.$this->vmTablePrefix.'_product_files')
				->where('file_product_id='.$this->destination_db->q($item['product_id']))
				->where('file_published = 1')
				->order('file_id');
			$this->source_db->setQuery($query);
			$additional_files = $this->source_db->loadObjectList();
			foreach ($additional_files as $file) {
				if (!$file->file_is_image) {
					//Media for download
					$mediaid = $this->importMediaByType($file->file_title,$file->file_name,'','download',$item['product_id'],$file->file_description);
				} elseif (strpos($file->file_name,'http')===0) {
					//Additional remote image
					$mediaid = $this->importMediaByType($file->file_title,$file->file_name,'','product',$item['product_id'],$file->file_description);
					//$mediaid = 0;
				} else {
					//Additional image
					$filename = str_replace('/components/com_virtuemart/shop_image/product/','',$file->file_name);
					$mediaid = $this->importMediaByType($file->file_title,$filename,'','product',$item['product_id'],$file->file_description);
				}
				if (!$mediaid) {
					$this->logWarning(JText::sprintf('VM_WARNING_MEDIA',$file->file_name));
					continue;
				}
				$record_media = new stdClass();
				$record_media->virtuemart_product_id = $item['product_id'];
				$record_media->virtuemart_media_id = $mediaid;
				$record_media->ordering = $i;
				$medias[] = $record_media;
				$i++;
			}

			try {
				$this->destination_db->transactionStart();
				if (count($medias)) {
					$this->insertOrReplace('#__virtuemart_product_medias', $medias, 'virtuemart_product_id');
				}
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,JText::sprintf('VM_INFO_MEDIAS_X',count($medias)));
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_waiting_list() {
        $pk = 'waiting_list_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_waiting_list',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass(); //virtuemart_vmusers
			$record->virtuemart_waitinguser_id = $item['waiting_list_id'];
			$record->virtuemart_product_id = $item['product_id'];
			$record->virtuemart_user_id = $item['user_id'];
			$record->notify_email = $item['notify_email'];
			$record->notified = $item['notified'];
			$record->notify_date = $item['notify_date'];
			$record->ordering = $i;

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_waitingusers', $record, 'virtuemart_waitinguser_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_product_votes() {
        $pk = 'product_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product_votes',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass(); //virtuemart_vmusers
			$record->virtuemart_product_id = $item['product_id'];
			$record->vote = $item['votes'];
			//$record->vote = $item['allvotes'];
			$record->lastip = $item['lastip'];

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_rating_votes', $record, 'virtuemart_product_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function vm_product_reviews() {
		
		
        $pk = 'review_id';

		if (!VMMigrateHelperDatabase::columnExists($this->source_db,$this->vmTablePrefix.'_product_reviews',$pk)) {
			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__'.$this->vmTablePrefix.'_product_reviews');
			$items = $this->source_db->setQuery($query)->loadAssocList();
		} else {
			$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
			$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product_reviews',$pk,$excludeids);	//Get a list of source objects to be transfered
		}

		if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
			return false;
		}
		
        foreach ($items as $i => $item) {
			
			if (isset($item[$pk])) {
				$srcid = $item[$pk];	//Set the primary key
			} else {
				$srcid = $i;
			}
			
			$record = new stdClass(); 
			$record->virtuemart_rating_review_id = $item['review_id'];
			$record->virtuemart_product_id	 	= $item['product_id'];
			$record->comment				 	= $item['comment'];
			$record->review_ok				 	= $item['review_ok'];
			$record->review_rates			 	= $item['user_rating'];
			$record->review_ratingcount		 	= 1;
			$record->review_rating			 	= $item['user_rating'];
			//$record->review_editable		 	= $item['notify_email'];
			//$record->lastip					 	= $item['notify_email'];
			$record->published				 	= ($item['published']=='Y')?1:0;
			$record->created_on				 	= $this->TimeToSqlDateTime($item['time']);
			$record->created_by				 	= $item['userid'];

			$record_vote = new stdClass(); 
			$record_vote->virtuemart_product_id	= $item['product_id'];
			$record_vote->vote				 	= $item['user_rating'];
			$record_vote->created_on			= $this->TimeToSqlDateTime($item['time']);
			$record_vote->created_by			= $item['userid'];

			try {
				$this->destination_db->transactionStart();
				//$this->insertOrReplace('#__virtuemart_ratings', $record_rating, 'virtuemart_rating_id');
				$this->insertOrReplace('#__virtuemart_rating_reviews', $record, 'virtuemart_rating_review_id');
				$this->deleteRows('#__virtuemart_rating_votes',"virtuemart_product_id='".$item['product_id']."' AND created_by='".$item['userid']."'");
				$this->destination_db->insertObject('#__virtuemart_rating_votes', $record_vote, 'virtuemart_rating_vote_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		} else {
			//Re count the votes
			$this->recounts_votes();
		}
	}
	
	private function recounts_votes() {
		$query = $this->destination_db->getQuery(true);
		
		//Get the payment method
		$query->clear()
			->select('sum(vote) as sumvotes, count(*) as nbvotes, max(created_on) as newest, virtuemart_product_id')
			->from('#__virtuemart_rating_votes')
			->group('virtuemart_product_id');
		$votes = $this->destination_db->setQuery($query)->loadObjectList();
		foreach ($votes as $productvotes) {

			$record = new stdClass(); 
			$record->virtuemart_product_id	= $productvotes->virtuemart_product_id;
			$record->rates				 	= $productvotes->sumvotes;
			$record->ratingcount			= $productvotes->nbvotes;
			$record->rating			 		= intval($productvotes->sumvotes) / intval($productvotes->nbvotes);
			$record->published			 	= 1;
			$record->created_on			 	= $productvotes->newest;
			//$record->created_by			 	= $item['userid'];
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_ratings', $record, 'virtuemart_product_id');
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage());
			}
		}
		$this->logInfo(JText::_('VM_PRODUCT_VOTES_RECOUNT'));
	}
	
	public function vm_product_type() {
		
		$this->ensureRelatedCustomsFields();
		
		$this->limit = 1;
        $pk = 'product_type_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product_type',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			
			$custom_parent = new stdClass();
			$custom_parent->custom_parent_id 		= 0;
			$custom_parent->virtuemart_vendor_id 	= $this->getVendorId(1);
			$custom_parent->custom_jplugin_id 		= 0;
			$custom_parent->custom_element 			= '';
			$custom_parent->admin_only		 		= 0;
			$custom_parent->custom_title	 		= $item['product_type_name'];
			$custom_parent->custom_tip		 		= '';
			$custom_parent->custom_value	 		= '';
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$custom_parent->custom_field_desc	= $item['product_type_description'];
				$custom_parent->field_type	 		= 'P'; //Parent
				$custom_parent->layout_pos		 	= '';
			} else {
				$custom_parent->custom_desc 		= $item['product_type_description'];
				$custom_parent->field_type	 		= 'G'; //Group
				$custom_parent->is_input	 		= $isCart;
				$custom_parent->layout_pos		 	= ($isCart) ? 'addtocart' : '';
			}
			$custom_parent->is_list		 			= 0;
			$custom_parent->is_hidden		 		= 0;
			$custom_parent->is_cart_attribute 		= $isCart;
			$custom_parent->custom_params	 		= '';
			$custom_parent->shared			 		= 0;
			$custom_parent->published		 		= 1;
			
			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__'.$this->vmTablePrefix.'_product_type_parameter')
				->where('product_type_id = '.$srcid); 
			$this->source_db->setQuery($query);
			$custom_parameters = $this->source_db->loadObjectList();

//			try {
//				$query = $this->source_db->getQuery(true);
//				$query->select('*')
//					->from('#__'.$this->vmTablePrefix.'_product_type_'.$srcid);
//				$this->source_db->setQuery($query);
//				$custom_products = $this->source_db->loadColumn();
//			} catch (Exception $e) {
				$custom_products = array();
//				//$this->logWarning($e->getMessage(),$srcid);
//			}
			
			try {
				$this->destination_db->transactionStart();
				//$custom_parent->virtuemart_custom_id = $this->createCustomFieldInputIfNotExists($item['product_type_name'],$isCart);
				$this->deleteRows('#__virtuemart_customs',"custom_title=".$this->destination_db->q($custom_parent->custom_title)." AND field_type='P'");
				$this->insertOrReplace('#__virtuemart_customs', $custom_parent, 'virtuemart_custom_id');
				//Assign the parent custom field to all products
				
				foreach ($custom_products as $custom_product) {
					$custom_value = new stdClass();
					$custom_value->virtuemart_product_id	= $custom_product->product_id;
					$custom_value->virtuemart_custom_id		= $custom_parent->virtuemart_custom_id;
					if (version_compare($this->getVmVersion(),'2.9','<')) {
						$custom_value->custom_value				= '';
						$custom_value->custom_price				= null;
						$custom_value->custom_param				= '';
					} else {
						$custom_value->customfield_value		= '';
						$custom_value->customfield_price		= null;
						$custom_value->customfield_params		= '';
					}
					$custom_value->published				= 1;
					$custom_value->ordering					= 0;
					$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
				}
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$custom_parent->custom_title,$custom_parent->virtuemart_custom_id);
				//Create the child custom fields
				foreach ($custom_parameters as $child_parameter) {
					$custom_child = new stdClass();
					$custom_child->custom_parent_id 		= $custom_parent->virtuemart_custom_id;
					$custom_child->virtuemart_vendor_id 	= $this->getVendorId(1);
					$custom_child->custom_jplugin_id 		= 0;
					$custom_child->custom_element 			= $child_parameter->parameter_name;
					$custom_child->admin_only		 		= 0;
					$custom_child->custom_title	 			= $child_parameter->parameter_label;
					$custom_child->custom_tip		 		= '';
					$custom_child->custom_value	 			= $child_parameter->parameter_values;
					if (version_compare($this->getVmVersion(),'2.9','<')) {
						$custom_child->custom_field_desc 	= $child_parameter->parameter_description;
					} else {
						$custom_child->custom_desc 			= $child_parameter->parameter_description;
					}
					switch ($child_parameter->parameter_type) {
						case 'I': 
							if (version_compare($this->getVmVersion(),'2.9','<')) {
								$custom_child->field_type	= 'I'; break; //Interger
							} else {
								$custom_child->field_type	= 'S'; break; //Interger was removed from VM3
							}
						case 'T': $custom_child->field_type	= 'S'; break; //Text
						case 'S': $custom_child->field_type	= 'Y'; break; //Short text
						case 'F': $custom_child->field_type	= 'S'; break; //Float
						case 'C': $custom_child->field_type	= 'S'; break; //Char
						case 'D': $custom_child->field_type	= 'D'; break; //Date & Time
						case 'A': $custom_child->field_type	= 'D'; break; //Date
						case 'M': $custom_child->field_type	= 'T'; break; //Time
						case 'V': $custom_child->field_type	= 'S'; break; //Multiple Values
						case 'B': $custom_child->field_type	= 'S'; break; //Break line
					}
					$custom_child->is_list		 			= ($child_parameter->parameter_values)?1:0;
					$custom_child->is_hidden		 		= 0;
					$custom_child->is_cart_attribute 		= 0;
					$custom_child->layout_pos		 		= '';
					$custom_child->custom_params	 		= '';
					$custom_child->shared			 		= 0;
					$custom_child->published		 		= 1;
					$custom_child->ordering			 		= $child_parameter->parameter_list_order;
					$custom_child->virtuemart_custom_id = $this->createCustomFieldInputIfNotExists($child_parameter->parameter_name,0);
					//$this->deleteRows('#__virtuemart_customs',"custom_title=".$this->destination_db->q($custom_child->custom_title)." AND custom_parent_id>0");
					$this->insertOrReplace('#__virtuemart_customs', $custom_child, 'virtuemart_custom_id');
					//$this->logRow($srcid,'----> '.$custom_child->custom_title,$custom_child->virtuemart_custom_id);
					$this->logInfo('----> '.$custom_child->custom_title);

					//Set the child values for the products
					foreach ($custom_products as $custom_product) {
						$custom_value = new stdClass();
						$custom_value->virtuemart_product_id	= $custom_product->product_id;
						$custom_value->virtuemart_custom_id		= $custom_child->virtuemart_custom_id;

						if (version_compare($this->getVmVersion(),'2.9','<')) {
							if (isset($custom_child->custom_value) && isset($custom_product->{$custom_child->custom_value})) {
								$custom_value->custom_value				= $custom_product->{$custom_child->custom_value};
							}
							$custom_value->custom_price				= null;
							$custom_value->custom_param				= '';
						} else {
							if (isset($custom_child->custom_value) && isset($custom_product->{$custom_child->custom_value})) {
								$custom_value->customfield_value				= $custom_product->{$custom_child->custom_value};
							}
							$custom_value->customfield_price		= null;
							$custom_value->customfield_params		= '';
						}

						$custom_value->published				= 1;
						$custom_value->ordering					= 0;
						if ($child_parameter->parameter_multiselect=='Y') {
							//We may have a multiple value assigned. In this case, we need to assing the custom field as many time as there are values.
							if (isset($custom_child->custom_value) && isset($custom_product->{$custom_child->custom_element})) {
								$all_values = $custom_product->{$custom_child->custom_element};
								$all_values = explode(';',$all_values);
								foreach ($all_values as $value) {
									$custom_value->virtuemart_customfield_id = null;
									if (version_compare($this->getVmVersion(),'2.9','<')) {
										$custom_value->custom_value	= $value;
									} else {
										$custom_value->customfield_value	= $value;
									}
									//$this->logInfo('Assigning value '.$custom_value->custom_value);
									$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
								}
							}
						} else {
							if (isset($custom_child->custom_value) && isset($custom_product->{$custom_child->custom_element})) {
								if (version_compare($this->getVmVersion(),'2.9','<')) {
									$custom_value->custom_value				= $custom_product->{$custom_child->custom_element};
								} else {
									$custom_value->customfield_value		= $custom_product->{$custom_child->custom_element};
								}
								//$this->logInfo('Assigning value '.$custom_value->custom_value);
							}
							$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
						}
					}

				}
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		}
	}

	public function vm_product_discount() {

		$this->logWarning(JText::_('NOT_YET_IMPLEMENTED'));
		return;

        $pk = 'discount_id';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_product_discount',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		$defaultVendor = $this->getVendor();

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->virtuemart_calc_id 	= $item['discount_id'];
			$record->virtuemart_vendor_id 	= $this->getVendorId($defaultVendor->virtuemart_vendor_id);
			$record->calc_jplugin_id 		= 0;
			$record->calc_name				= 'Discount -'.$item['amount'].(($item['is_percent'])?'%':'');
			$record->calc_descr 			= '';
			$record->calc_kind 				= 'DBTax';
			$record->calc_value_mathop 		= ($item['is_percent'])?'-%':'-';
			$record->calc_value		 		= $item['amount'];
			$record->calc_currency	 		= $defaultVendor->vendor_currency;
			$record->calc_shopper_published	= 1;
			$record->calc_vendor_published	= 1;
			if ($item['start_date']) {
				$record->publish_up			= $this->TimeToSqlDateTime($item['start_date']);
			}
			if ($item['end_date']) {
				$record->publish_down		= $this->TimeToSqlDateTime($item['end_date']);
			}
			$record->for_override			= 0;
			//$record->calc_params			= 1;
			$record->ordering				= 0;
			$record->shared					= 0;
			$record->published				= 1;
			
			try {
				$this->destination_db->transactionStart();
				$result = $this->insertOrReplace('#__virtuemart_calcs', $record, 'virtuemart_calc_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$record->calc_name);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		//Ensure only one default shopper group is set
		//if ($newDefaultId) {
		//	$shoppergroupModel->makeDefault($newDefaultId);
		//}
		if ($this->moreResults) {
			return true;
		}
	}

	public function vm_orders() {
		
        $pk = 'order_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_orders',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			//Create the order record	
			$order = new stdClass(); //virtuemart_vmusers
			$order->virtuemart_order_id		= $item['order_id'];
			if ($item['user_id'] <= 0) {
				$order->virtuemart_user_id		= 0;
				$order->customer_number = 'nonreg_';				
			} else {
				$order->virtuemart_user_id		= $item['user_id'];
				$order->customer_number = $item['user_info_id'];;				
			}
			$order->virtuemart_vendor_id	= $this->getVendorId($item['vendor_id']);
			$order->order_number 			= $item['order_id'];
			$order->order_pass	 			= 'p_'.substr( md5((string)time().rand(1,1000).$item['order_id'] ), 0, 5);
			$order->order_total 			= $item['order_total'];
			$order->order_billTaxAmount		= 0;
			$order->order_billTax 			= 0;
			$order->order_billDiscountAmount= 0;
			$order->order_discountAmount	= 0;
			$order->order_subtotal 			= $item['order_subtotal'];
			$order->order_tax	 			= $item['order_tax'];
			$order->order_shipment 			= $item['order_shipping'];
			$order->order_shipment_tax		= $item['order_shipping_tax'];
			if ($item['order_discount']) {
				$order->order_payment		= -(floatval($item['order_discount']));
			} else {
				$order->order_payment		= 0;
			}
			$order->order_payment_tax		= 0;
			$order->coupon_discount			= $item['coupon_discount'];
			$order->coupon_code				= $item['coupon_code'];
			$order->order_discount			= 0; //$item['order_discount'];
			$order->order_currency			= $this->getCurrencyId($item['order_currency']);
			$order->order_status 			= $item['order_status'];
			$order->user_currency_id		= $order->order_currency;
			$order->user_currency_rate		= 1;
			if (version_compare($this->getVmVersion(),'2.9.9.4','<')) {
				$order->customer_note		= $item['customer_note'];
			}
			$order->ip_address				= $item['ip_address'];
			$order->created_on				= $this->TimeToSqlDateTime($item['cdate']);
			$order->created_by				= $item['user_id'];
			$order->modified_on				= $this->TimeToSqlDateTime($item['mdate']);
			$order->modified_by				= $item['user_id'];

			$query = $this->source_db->getQuery(true);
			
			//Get the payment method
			$query->clear()
				->select('payment_method_id')
				->from('#__'.$this->vmTablePrefix.'_order_payment')
				->where('order_id='.$this->source_db->q($item['order_id']));
			$order->virtuemart_paymentmethod_id = $this->source_db->setQuery($query)->loadResult();

			
			//Get the shipment method
			$ship_method_id = explode('|',$item['ship_method_id']);
			$ship_method_module = $ship_method_id[0];
			if ($ship_method_module=='standard_shipping' && isset($ship_method_id[4])) {
				$order->virtuemart_shipmentmethod_id = $ship_method_id[4];
			} else {
				$order->virtuemart_shipmentmethod_id = $this->getShipmentMethodId($ship_method_module);
			}
			

			//Get the order history
			$query->clear()
				->select('*')
				->from('#__'.$this->vmTablePrefix.'_order_history')
				->where('order_id='.$this->source_db->q($item['order_id']))
				->order('date_added ASC');
			$this->source_db->setQuery($query);
			$histories = $this->source_db->loadObjectList();
			
			$order_histories = array();
			foreach ($histories as $history) {
				$order_history = new stdClass();
				$order_history->virtuemart_order_id = $history->order_id;
				$order_history->order_status_code	= $history->order_status_code;
				$order_history->customer_notified	= $history->customer_notified;
				$order_history->comments			= $history->comments;
				$order_history->published			= 1;
				$order_history->created_on			= $history->date_added;
				$order_histories[] = $order_history;
			}
			
			//Get the order items
			$query = $this->source_db->getQuery(true);
			$query->clear()
				->select('*')
				->from('#__'.$this->vmTablePrefix.'_order_item')
				->where('order_id='.$this->source_db->q($item['order_id']));
			$this->source_db->setQuery($query);
			$items = $this->source_db->loadObjectList();
			
			$order_items = array();
			foreach ($items as $src_orderitem) {
				$order_item = new stdClass();
				$order_item->virtuemart_order_id 		= $this->getVendorId($src_orderitem->order_id);
				$order_item->virtuemart_vendor_id 		= $src_orderitem->vendor_id;
				$order_item->virtuemart_product_id 		= $src_orderitem->product_id;
				$order_item->order_item_sku		 		= $src_orderitem->order_item_sku;
				$order_item->order_item_name	 		= $src_orderitem->order_item_name;
				$order_item->product_quantity	 		= $src_orderitem->product_quantity;

				$order_item->product_item_price	 				= $src_orderitem->product_item_price;
				//$order_item->product_tax		 				= 0;
				$order_item->product_tax						= $src_orderitem->product_final_price - $src_orderitem->product_item_price;
				$order_item->product_priceWithoutTax			= $src_orderitem->product_item_price;
				$order_item->product_discountedPriceWithoutTax	= $src_orderitem->product_item_price;
				$order_item->product_basePriceWithTax 			= $src_orderitem->product_final_price;
				$order_item->product_final_price 				= $src_orderitem->product_final_price;
				$order_item->product_subtotal_discount 			= 0;
				$order_item->product_subtotal_with_tax 			= $src_orderitem->product_quantity * $src_orderitem->product_final_price;

				$order_item->order_item_currency 		= $this->getCurrencyId($src_orderitem->order_item_currency);
				$order_item->order_status 				= $src_orderitem->order_status;
				$order_item->product_attribute 			= $this->OrderItemAttributesToJson($src_orderitem);
				$order_item->created_on 				= $this->TimeToSqlDateTime($src_orderitem->cdate);
				$order_item->created_by 				= $item['user_id'];
				$order_item->modified_on 				= $this->TimeToSqlDateTime($src_orderitem->mdate);
				$order_items[] = $order_item;
			}

			//Get the order urser infos
			$query->clear()
				->select('*')
				->from('#__'.$this->vmTablePrefix.'_order_user_info')
				->where('order_id='.$this->source_db->q($item['order_id']));
			$this->source_db->setQuery($query);
			$user_infos = $this->source_db->loadObjectList();
			
			$order_user_infos = array();
			foreach ($user_infos as $src_infos) {
				$user_info = new stdClass();
				$user_info->virtuemart_order_id = $src_infos->order_id;
				$user_info->virtuemart_user_id 	= $src_infos->user_id;
				$user_info->address_type 		= $src_infos->address_type;
				$user_info->address_type_name 	= $src_infos->address_type_name;
				$user_info->company 			= $src_infos->company;
				$user_info->title 				= $src_infos->title;
				$user_info->last_name 			= $src_infos->last_name;
				$user_info->first_name 			= $src_infos->first_name;
				$user_info->middle_name 		= $src_infos->middle_name;
				$user_info->phone_1 			= $src_infos->phone_1;
				$user_info->phone_2 			= $src_infos->phone_2;
				$user_info->fax 				= $src_infos->fax;
				$user_info->address_1 			= $src_infos->address_1;
				$user_info->address_2 			= $src_infos->address_2;
				$user_info->city 				= $src_infos->city;
				$user_info->virtuemart_state_id = $this->getStateIDByName($src_infos->state,$src_infos->country);
				$user_info->virtuemart_country_id = $this->getCountryIDByName($src_infos->country);
				$user_info->zip 				= $src_infos->zip;
				$user_info->email	 			= $src_infos->user_email;
				$user_info->created_on			= $order->created_on;
				$user_info->created_by			= $order->created_by;
				$user_info->modified_on			= $order->modified_on;
				$user_info->modified_by			= $order->modified_by;

				if ($this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_userfield')) {	
					//Add the custom fields
					$query->clear()
						->select('*')
						->from('#__'.$this->vmTablePrefix.'_userfield')
						->where('sys=0');
					$this->source_db->setQuery($query);
					$custom_fields = $this->source_db->loadObjectList();
					foreach ($custom_fields as $custom_field) {
						try {
							if ($custom_field->name && isset($src_infos->{$custom_field->name})) {
								$user_info->{$custom_field->name} = $src_infos->{$custom_field->name};
							}
						} catch (Exception $e) {
						}
					}
				}
				
				if (version_compare($this->getVmVersion(),'2.9.9.4','>=')) {
					VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_order_userinfos','customer_note','varchar(2500)  NOT NULL DEFAULT \'\'');
					$user_info->customer_note = $item['customer_note'];
				}

				$order_user_infos[] = $user_info;
			}
			
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_orders', $order, 'virtuemart_order_id');
				$this->insertOrReplace('#__virtuemart_order_histories', $order_histories, 'virtuemart_order_id');
				$this->insertOrReplace('#__virtuemart_order_items', $order_items, 'virtuemart_order_id');
				$this->insertOrReplace('#__virtuemart_order_userinfos', $order_user_infos, 'virtuemart_order_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,'#'.$item['order_id']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}

	public function vm_coupons() {
		
        $pk = 'coupon_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($this->vmTablePrefix.'_coupons',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass(); 
			$record->virtuemart_coupon_id	= $item['coupon_id'];
			$record->coupon_code			= $item['coupon_code'];
			$record->percent_or_total		= $item['percent_or_total'];
			$record->coupon_type			= $item['coupon_type'];
			$record->coupon_value			= $item['coupon_value'];
			$record->virtuemart_vendor_id 	= $this->getVendorId();
			$record->published				= 1;

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__virtuemart_coupons', $record, 'virtuemart_coupon_id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['coupon_code']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	/*********************/
	/* PRIVATE FUNCTIONS */
	/*********************/
	private function getVendor($vendorid=1) {
		$vendorModel = VmModel::getModel('vendor');
		return $vendorModel->getVendor($vendorid);
	}

	private function getVendorId($vendorid=1) {
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($vendorid);
		return ($vendor) ? $vendor->virtuemart_vendor_id : $vendorid;
	}
		

	private function insert_field_values($srcid,&$record) {

		$query = $this->destination_db->getQuery(true);
		$query->select('virtuemart_userfield_id')->from('#__virtuemart_userfields')->where('name='.$this->destination_db->q($record->name));
		$this->destination_db->setQuery($query);
		$destid = $this->destination_db->loadResult();
		$record->virtuemart_userfield_id = $destid;
		
		//Remove existing values
		$query = $this->destination_db->getQuery(true);
		$query->delete('#__virtuemart_userfield_values')->where('virtuemart_userfield_id='.$this->destination_db->q($destid));
		$this->destination_db->setQuery($query);
		$this->destination_db->query();

		//Insert the userfield values
		$query = $this->source_db->getQuery(true);
		$query->select('*')->from('#__'.$this->vmTablePrefix.'_userfield_values')->where('fieldid='.$this->source_db->q($srcid));
		$this->source_db->setQuery($query);
		$record_values = $this->source_db->loadObjectList();

		foreach ($record_values as $record_value) {
			$this->logDebug($record_value,'Custom Field value');
			$record_value->virtuemart_userfield_id = $destid;
			$record_value->fieldid = null;
			$record_value->fieldvalueid = null;
			$this->destination_db->insertObject('#__virtuemart_userfield_values', $record_value, 'virtuemart_userfield_value_id');
		}
	}
	
	private function getMediaIdByMeta($filename,$type){
		
		$query = $this->destination_db->getQuery(true);
		$query->select('virtuemart_media_id')
			->from('#__virtuemart_medias')
			->where('file_url ='.$this->destination_db->q($filename))
			->where('file_type ='.$this->destination_db->q($type));
		$this->destination_db->setQuery($query);
		$virtuemart_media_id = $this->destination_db->loadResult();
		
		if ($virtuemart_media_id) {
			return $virtuemart_media_id;
		} else {
			return 0;
		}
	}
	
	private function importMediaByType($name,$filename,$alt_filename,$type,$ref,$description=''){

		//Check if the file is an image
//		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
//		if (!in_array($ext,array('jpg','jpe','jpeg','gif','png','bmp','tif','tiff','pdf','mp3','zip'))) {
//			$filename = $alt_filename;
//			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
//			if (!in_array($ext,array('jpg','jpe','jpeg','gif','png','bmp','tif','tiff','pdf','mp3','zip'))) {
//				return 0;
//			}
//		}

		if (!$filename && $alt_filename) {
			$filename = $alt_filename;
		}
		
		if (!$filename) {
			return 0;
		}
		
		if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');

		$src_root = '/components/com_virtuemart/shop_image';

		$mediaId = 0;
		switch ($type) {
			case 'vendor':
				$src = $src_root.'/vendor/'.$filename;
				$dest_folder = VmConfig::get('media_vendor_path');

				$dest_ext = strtolower(JFile::getExt($filename));
				$dest_filename = JFile::stripExt(basename($filename));
				if ($this->params->get('rename_files_safe', 0)) {
					$dest_file = JApplication::stringURLSafe($dest_filename).'.'.$dest_ext;
				} else {
					$dest_file = $filename;
				}
				$url = $dest_folder.$dest_file;
				$dest_path = JPATH_SITE.'/'.$url;
				break;
			case 'category':
				$src = $src_root.'/category/'.$filename;
				$dest_folder = VmConfig::get('media_category_path');

				$dest_ext = strtolower(JFile::getExt($filename));
				$dest_filename = JFile::stripExt(basename($filename));
				if ($this->params->get('rename_files_safe', 0)) {
					$dest_file = JApplication::stringURLSafe($dest_filename).'.'.$dest_ext;
				} else {
					$dest_file = $filename;
				}
				$url = $dest_folder.$dest_file;
				$dest_path = JPATH_SITE.'/'.$url;
				if ($alt_filename && $this->params->get('import_thumbs', 0)) {
					$src_thumb = $src_root.'/category/'.$alt_filename;
					$dest_filename_thumb = JFile::stripExt(basename($alt_filename));
					if ($this->params->get('rename_files_safe', 0)) {
						$dest_file_thumb = JApplication::stringURLSafe($dest_filename_thumb).'.'.strtolower(JFile::getExt($alt_filename));
					} else {
						$dest_file_thumb = $alt_filename;
					}
					$url_thumb = $dest_folder.$dest_file_thumb;
					$dest_path_thumb = JPATH_SITE.'/'.$url_thumb;
				}
				break;
			case 'product':
				$src = $src_root.'/product/'.$filename;
				$dest_folder = VmConfig::get('media_product_path');
				
				$dest_ext = strtolower(JFile::getExt($filename));
				$dest_filename = JFile::stripExt(basename($filename));
				if ($this->params->get('rename_files_safe', 0)) {
					$dest_file = JApplication::stringURLSafe($dest_filename).'.'.$dest_ext;
				} else {
					$dest_file = $filename;
				}
				$url = $dest_folder.$dest_file;
				$dest_path = JPATH_SITE.'/'.$url;
				if ($alt_filename && $this->params->get('import_thumbs', 0)) {
					$src_thumb = $src_root.'/product/'.$alt_filename;
					$dest_filename_thumb = JFile::stripExt(basename($alt_filename));
					if ($this->params->get('rename_files_safe', 0)) {
						$dest_file_thumb = JApplication::stringURLSafe($dest_filename_thumb).'.'.strtolower(JFile::getExt($alt_filename));
					} else {
						$dest_file_thumb = $alt_filename;
					}
					$url_thumb = $dest_folder.$dest_file_thumb;
					$dest_path_thumb = JPATH_SITE.'/'.$url_thumb;
				}
				break;
			case 'forSale':
				//$oldConfigFile = $this->source_path . '/administrator/components/com_virtuemart/virtuemart.cfg.php';
				//if (JFile::exists($oldConfigFile)) {
				//	include_once($oldConfigFile);
				//}
				$src = $filename;
				$dest_folder = VmConfig::get('forSale_path');

				$dest_ext = strtolower(JFile::getExt($filename));
				$dest_filename = JFile::stripExt(basename($filename));
				if ($this->params->get('rename_files_safe', 0)) {
					$dest_file = JApplication::stringURLSafe($dest_filename).'.'.$dest_ext;
				} else {
					$dest_file = $filename;
				}
				$url = $dest_folder.$dest_file;

				$dest_path = $url;
				$data['media_roles'] = 'file_is_forSale';
				break;
			case 'download':
				$src = $filename;
				$dest_folder = VmConfig::get('media_product_path');

				$dest_ext = strtolower(JFile::getExt($filename));
				$dest_filename = JFile::stripExt(basename($filename));
				if ($this->params->get('rename_files_safe', 0)) {
					$dest_file = JApplication::stringURLSafe($dest_filename).'.'.$dest_ext;
				} else {
					$dest_file = $filename;
				}
				$url = $dest_folder.$dest_file;
				$dest_path = JPATH_SITE.'/'.$url;
				$data['media_roles'] = 'file_is_downloadable';
				break;
		}
		
		//Create the destination folder if not exists
		if (!JFolder::exists(JPATH_SITE.'/'.$dest_folder)) {
			JFolder::create(JPATH_SITE.'/'.$dest_folder);
		}
	
		$defaultVendor = $this->getVendor();

		$mediaIsRemote = false;
		if (strpos($filename,'http')===0) {
			$mediaIsRemote  = true;
			$url = $filename;
		}
		
		if ($mediaIsRemote) {

			if (!JFile::exists($dest_path)) {
				try {
					$image = file_get_contents($url);
					file_put_contents($dest_path, $image);
				} catch (Exception $e) {
					if ($alt_filename) {
						//Try importing the thumbnail as main image
						$mediaId = $this->importMediaByType($name,$alt_filename,'',$type,$ref,$description);
						if ($mediaId) {
							return $mediaId;
						} else {
							$mediaId = $this->importMediaByType($name,'resized/'.$alt_filename,'',$type,$ref,$description);
							if ($mediaId) {
								return $mediaId;
							}
							return 0;
						}
					}
					$this->logWarning(JText::sprintf('VMMIGRATE_ERROR_READING_SRC_FILE',$type,$ref,$url));
					return 0;
				}
			}
			
			if ($mediaId = $this->getMediaIdByMeta(str_replace(JPATH_SITE.'/','',$dest_path),$type)) {
				return $mediaId;
			}
			$data['virtuemart_vendor_id'] = $defaultVendor->virtuemart_vendor_id;
			$data['file_title'] = $name;
			$data['file_description'] = $description;
			$data['file_meta'] = $name;
			$data['media_published'] = 1;
			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			switch ($ext) {
				case 'jpe': 
				case 'jpg': 
				case 'jpeg': 
					$data['file_mimetype'] = 'image/jpeg'; 
					break;
				case 'gif': 
					$data['file_mimetype'] = 'image/gif'; 
					break;
				case 'png': 
					$data['file_mimetype'] = 'image/png'; 
					break;
				case 'bmp': 
					$data['file_mimetype'] = 'image/bmp'; 
					break;
				case 'tif': 
				case 'tiff': 
					$data['file_mimetype'] = 'image/tiff'; 
					break;
				case 'pdf': 
					$data['file_mimetype'] = 'application/pdf'; 
					break;
			}
			$data['file_type'] = ($type == 'forSale' || $type == 'download') ? 'product' : $type;
			$data['file_url'] = str_replace(JPATH_SITE.'/','',$dest_path);
			//$data['file_url_thumb'] = ($remotethumb) ? $remotethumb : $url;

		} else {
			if (!$this->source_filehelper->FileExists($src)) {
				if ($alt_filename) {
					//Try importing the thumbnail as main image
					$mediaId = $this->importMediaByType($name,$alt_filename,'',$type,$ref,$description);
					if ($mediaId) {
						return $mediaId;
					} else {
						$mediaId = $this->importMediaByType($name,'resized/'.$alt_filename,'',$type,$ref,$description);
						if ($mediaId) {
							return $mediaId;
						}
						//return 0;
					}
				}
				$this->logWarning(JText::sprintf('VMMIGRATE_ERROR_READING_SRC_FILE',$type,$ref,$src));
				//return 0;
			}

			//Copy the image from source to dest site if not already copied
			if (!JFile::exists($dest_path) && $this->source_filehelper->FileExists($src)) {
				$this->source_filehelper->CopyFile($src,$dest_path);
			}
			//Copy the image from source to dest site if not already copied

			if ($dest_path_thumb && !JFile::exists($dest_path_thumb) && $this->source_filehelper->FileExists($src_thumb)) {
				$this->source_filehelper->CopyFile($src_thumb,$dest_path_thumb);
			}

			//Declare media in table
			$data['virtuemart_vendor_id'] = $defaultVendor->virtuemart_vendor_id;
			$data['file_title'] = $name;
			$data['file_description'] = $description;
			$data['file_meta'] = $name;
			$data['media_published'] = 1;
			$data['file_type'] = ($type == 'forSale' || $type == 'download') ? 'product' : $type;
			$data['file_url'] = $url;
			if ($url_thumb) {
				$data['file_url_thumb'] = $url_thumb;
			}
			if (JFile::exists($dest_path)) {
				if ($mediaId = $this->getMediaIdByMeta($url,$type)) {
					return $mediaId;
				}
				$ext = strtolower(JFile::getExt($dest_path));
				switch ($ext) {
					case 'jpe': 
					case 'jpg': 
					case 'jpeg': 
						$data['file_mimetype'] = 'image/jpeg'; 
						break;
					case 'gif': 
						$data['file_mimetype'] = 'image/gif'; 
						break;
					case 'png': 
						$data['file_mimetype'] = 'image/png'; 
						break;
					case 'bmp': 
						$data['file_mimetype'] = 'image/bmp'; 
						break;
					case 'tif': 
					case 'tiff': 
						$data['file_mimetype'] = 'image/tiff'; 
						break;
					case 'pdf': 
						$data['file_mimetype'] = 'application/pdf'; 
						break;
					case 'mp3': 
						$data['file_mimetype'] = 'audio/mpeg3'; 
						break;
					case 'zip': 
						$data['file_mimetype'] = 'application/zip'; 
						break;
				}
			}
		}
		
		if($type == 'product') $data['file_is_product_image'] = 1;
		if($type == 'forSale') $data['file_is_forSale'] = 1;
		if($type == 'download') {
			$data['file_is_downloadable'] = 1;
			$type = 'product';
		}
		
		if (!$data['file_title'] && $dest_filename) {
			$data['file_title'] = $dest_filename;
		}
		$this->mediaModel = VmModel::getModel('Media');
		$this->mediaModel->setId($mediaId);
		$mediaId = $this->mediaModel->store($data,$type);

		return $mediaId;
	}
	
	private $_countries = array();
	private function getCountryIdByName($name){
		if ($name=='FXX') $name='FRA'; //France métropolotaine was removed
		if ($name=='XJE') $name='JEY'; //Jersey code was changed
		//if ($name=='MNE') $name='MNE'; //Montenegro was removed
		
		if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');
		if(empty($this->_countries[$name])){
			$this->_countries[$name] = Shopfunctions::getCountryIDByName($name);
		}

		return $this->_countries[$name];
	}

	private $_states = array();
	private function getStateIdByName($name,$countryName){
		
		if(empty($this->_states[$countryName][$name])){
			$countryId = $this->getCountryIdByName($countryName);
			$query = $this->destination_db->getQuery(true);
			$query->select('virtuemart_state_id')
				->from('#__virtuemart_states');
			if ($countryId) {
				$query->where('virtuemart_country_id ='.$this->destination_db->q($countryId));
			}
	
			if (strlen ($name) === 2) {
				$query->where('state_2_code ='.$this->destination_db->q($name));
			} elseif (strlen ($name) === 3) {
				$query->where('state_3_code ='.$this->destination_db->q($name));
			} else {
				$query->where('state_name ='.$this->destination_db->q($name));
			}
			$this->destination_db->setQuery($query);
			$this->_states[$countryName][$name] = $this->destination_db->loadResult();
		}
		
		return $this->_states[$countryName][$name];
	}
	
	private $_currencies = array();
	private function getCurrencyId($currency_code) {

		if(!array_key_exists($currency_code,$this->_currencies)) {
			$query = $this->destination_db->getQuery(true);
			$query->select('virtuemart_currency_id');
			$query->from('#__virtuemart_currencies');
			$query->where('currency_code_3 ='.$this->destination_db->q($currency_code));
			$this->destination_db->setQuery($query);
			//return $this->destination_db->loadResult();
			$this->_currencies[$currency_code] = $this->destination_db->loadResult();
		} 
		return $this->_currencies[$currency_code];
	}
	private function getCurrencyIds($currency_codes) {
		$currencyIds = array();
		foreach ($currency_codes as $code) {
			$currencyId = $this->getCurrencyId($code);
			if ($currencyId) {
				$currencyIds[] = $currencyId;
			}
		}
		return $currencyIds;
	}

	private function parseUom($uom){
		$Units = array (
          '/mm/' => 'MM'
		, '/cm/' => 'CM'
		, '/m/' => 'M'
		, '/yd/' => 'YD'
		, '/yard/' => 'YD'
		, '/foot/' => 'FT'
		, '/feet/' => 'FT'
		, '/ft/' => 'FT'
		, '/inche?s?/' => 'IN'
        , '/kg/' => 'KG'
		, '/kilos/' => 'KG'
		, '/gr/' => 'G'
		, '/pounds?/' => 'LB'
		, '/livres?/' => 'LB'
		, '/once/' => 'OZ'
		, '/ounces?/' => 'OZ'
		, '/piece/' => ''
		);
		$uom = strtolower($uom);
		$unit = preg_replace(array_keys($Units),array_values($Units),$uom);
		return $unit;
	}

	private function getNbOrderedItems($product_id) {
		//Categories
		$query = $this->source_db->getQuery(true);
		$query->select('sum(product_quantity) as counter')
			->from('#__'.$this->vmTablePrefix.'_order_item')
			->where('product_id='.$this->destination_db->q($product_id))
			->where("order_status in ('C','P')");
		$this->source_db->setQuery($query);
		$counter = $this->source_db->loadResult();
		if ($counter) {
			return $counter;
		} else {
			return 0;
		}
	}
	
	private function getProductParams($item) {
		$order_levels = $item['product_order_levels'].'';
		$order_levels = explode(',',$order_levels);
		$params[] = 'min_order_level="'.(($order_levels[0]) ? $order_levels[0] : '').'"';
		$params[] = 'max_order_level="'.(($order_levels[1]) ? $order_levels[1] : '').'"';
		$params[] = 'step_order_level=""';
		$params[] = 'product_box="'.(($item['product_packaging']) ? $item['product_packaging'] : '').'"';
		
		return implode("|",$params);
	}
	
	private function getProductSlug($item) {
		//TODO : Generate a new slug with sku in case the slug already exists
		return JApplication::stringURLSafe($item['product_name']);
	}
	
	private function processProductTypes($item) {

		$product_id = $item['product_id'];
		
		$query = $this->source_db->getQuery(true);
		$query->select('*')
			->from('#__'.$this->vmTablePrefix.'_product_type');
		$product_types = $this->source_db->setQuery($query)->loadObjectList();

		//Loop on the product types
		foreach ($product_types as $product_type) {
			
			//$parent_custom_id = $this->createCustomFieldIfNotExists($product_type->product_type_name,true);
			$groupFieldType = version_compare($this->getVmVersion(),'2.9','<') ? 'P' : 'G';
			$query->clear()
				->select('virtuemart_custom_id')
				->from('#__virtuemart_customs')
				->where('field_type = '.$this->destination_db->q($groupFieldType))
				->where('custom_title = '.$this->destination_db->q($product_type->product_type_name));
			$parent_custom_id = $this->destination_db->setQuery($query)->loadResult();
			
			//Get the parameters from the product type table
			if ($this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_product_type_parameter')) {
				$query->clear()->select('*')
					->from('#__'.$this->vmTablePrefix.'_product_type_parameter')
					->where('product_type_id = '.$product_type->product_type_id); 
				$this->source_db->setQuery($query);
				$custom_parameters = $this->source_db->loadObjectList();
			} else {
				$custom_parameters = array();
			}

			//Get the values from the product type table
			if ($this->table_exists($this->source_db,'#__'.$this->vmTablePrefix.'_product_type_'.$product_type->product_type_id)) {
				$query->clear()
					->select('*')
					->from('#__'.$this->vmTablePrefix.'_product_type_'.$product_type->product_type_id)
					->where('product_id = '.$this->source_db->q($product_id));
				$custom_values = 	$this->source_db->setQuery($query)->loadObjectList();
			} else {
				$custom_values = 	array();
			}
				
			foreach($custom_values as $custom_value_src) {
				
				//Assign the parent custom field to the product
				$custom_value = new stdClass();
				$custom_value->virtuemart_product_id	= $product_id;
				$custom_value->virtuemart_custom_id		= $parent_custom_id;
				$custom_value->published				= 1;
				$custom_value->ordering					= 0;
				$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');

				//Now assign each child attribute
				foreach ($custom_parameters as $custom_parameter) {
					
					//Get the custom id for the selected parent
					$query->clear()
						->select('virtuemart_custom_id')
						->from('#__virtuemart_customs')
						->where('custom_parent_id = '.$this->destination_db->q($parent_custom_id))
						->where('custom_element = '.$this->destination_db->q($custom_parameter->parameter_name));
					$custom_id = $this->destination_db->setQuery($query)->loadResult();
				
					if ($custom_id) {
						$custom_value = new stdClass();
						$custom_value->virtuemart_product_id	= $product_id;
						$custom_value->virtuemart_custom_id		= $custom_id;
						if (version_compare($this->getVmVersion(),'2.9','<')) {
							$custom_value->custom_value				= $custom_value_src->{$custom_parameter->parameter_name};
							$custom_value->custom_price				= null;
							$custom_value->custom_param				= '';
						} else {
							$custom_value->customfield_value		= $custom_value_src->{$custom_parameter->parameter_name};
							$custom_value->customfield_price		= null;
							$custom_value->customfield_params		= '';
						}
						$custom_value->published				= 1;
						$custom_value->ordering					= 0;
						$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
					}
				}
				
			}
		}
	}

	private function processProductAttributes($item,$product_prices=array(),&$ordering=0) {
		$attributes_string = $item['attribute'];
		if (!$attributes_string) {
			return;
		}
		$attributes = explode(';',$attributes_string);
		foreach ($attributes as $attribute) {
			$attribute_data = explode(',',$attribute);
			$attribute_name = array_shift($attribute_data);
			$attribute_values = array();
			foreach ($attribute_data as $data) {
				$attribtxt = substr( $data, 0, strrpos( $data, '[' ) ) ;
				$attribute_value = new stdClass();
				if(strrpos($data,'[')) {
					$matches = null;
					//preg_match('/(.*)\[([\=,\+,\-])?(\d+)\]/', $data, $matches);
					preg_match('/(.*)\[([\=,\+,\-])?(.*)\]/', $data, $matches);
					$attribute_value->option = $matches[1];
					$attribute_value->operator = $matches[2];
					$attribute_value->price = $matches[3];
					if (strpos($attribute_value->price,'%')>0) {
						$attribute_value->percentage = true;
					} else {
						$attribute_value->percentage = false;
					}
					$attribute_value->price = floatval($attribute_value->price);
				} else {
					//No specific price set for this option
					$attribute_value->option = $data;
					$attribute_value->operator = '+';
					$attribute_value->price = 0;
					$attribute_value->percentage = false;
				}
				$attribute_values[] = $attribute_value;
			}
			$virtuemart_custom_id = $this->createCustomFieldIfNotExists($attribute_name,true);
			
			//Now assign the custom field to the product
			foreach ($attribute_values as $attribute_value) {
				$custom_value = new stdClass();
				$custom_value->virtuemart_product_id	= $item['product_id'];
				$custom_value->virtuemart_custom_id		= $virtuemart_custom_id;
				if (version_compare($this->getVmVersion(),'2.9','<')) {
					$custom_value->custom_value				= $attribute_value->option;
					$custom_value->custom_param				= '';
				} else {
					$custom_value->customfield_value		= $attribute_value->option;
					$custom_value->customfield_params		= '';
				}
	
				//We calculate the price to add or deduct to the base price
				if (count($product_prices)) {
					$baseprice = $product_prices[0]->product_price;
				} else {
					$baseprice = 0;
				}
				if ($attribute_value->percentage) {
					$attribute_value->price = floatval($attribute_value->price) * $baseprice / 100;
				} 

				switch ($attribute_value->operator) {
					case '-':
						if (version_compare($this->getVmVersion(),'2.9','<')) {
							$custom_value->custom_price				= floatval('-'.$attribute_value->price);
						} else {
							$custom_value->customfield_price		= floatval('-'.$attribute_value->price);
						}
						break;
					case '=':
						if (version_compare($this->getVmVersion(),'2.9','<')) {
							$custom_value->custom_price				= $attribute_value->price - $baseprice;
						} else {
							$custom_value->customfield_price		= $attribute_value->price - $baseprice;
						}
						break;
					case '+':
					default:
						if (version_compare($this->getVmVersion(),'2.9','<')) {
							$custom_value->custom_price				= floatval($attribute_value->price);
						} else {
							$custom_value->customfield_price		= floatval($attribute_value->price);
						}
						break;
				}
				
				$custom_value->published				= 1;
				$custom_value->ordering					= $ordering;
				$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
				$ordering++;
			}
		}
	}

	private function createCustomFieldIfNotExists($field_name,$isCart=true) {

		$db = $this->destination_db;
		$query = $db->getQuery(true);
		$query->select('virtuemart_custom_id')
			->from('#__virtuemart_customs')
			->where('custom_title='.$db->q($field_name))
			->where('is_cart_attribute='.$db->q(($isCart)?1:0));
		//if (version_compare($this->getVmVersion(),'2.9','<')) {
		//	$query->where('field_type='.$db->q(($isCart)?'V':'S'));
		//} else {
		//	$query->where('field_type='.$db->q(($isCart)?'C':'S'));
		//}
		$db->setQuery($query);
		$virtuemart_custom_id = $db->loadResult();
		
		if ($virtuemart_custom_id) {
			return $virtuemart_custom_id;
		} else {
			$custom = new stdClass();
			$custom->custom_parent_id 		= 0;
			$custom->virtuemart_vendor_id 	= $this->getVendorId();
			$custom->custom_jplugin_id 		= 0;
			$custom->custom_element 		= '';
			$custom->admin_only		 		= 0;
			$custom->custom_title	 		= $field_name;
			$custom->custom_tip		 		= '';
			$custom->custom_value	 		= $field_name;
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$custom->custom_field_desc 	= '';
				$custom->field_type		 	= ($isCart)?'V':'S';
				$custom->layout_pos		 	= '';
			} else {
				$custom->custom_desc	 	= '';
				//$custom->field_type		 	= ($isCart)?'C':'S';
				$custom->field_type		 	= 'S';
				$custom->is_input	 		= $isCart;
				$custom->layout_pos		 	= ($isCart) ? 'addtocart' : '';
			}
			$custom->is_list		 		= 0;
			$custom->is_hidden		 		= 0;
			$custom->is_cart_attribute 		= $isCart;
			$custom->custom_params	 		= '';
			$custom->shared			 		= 0;
			$custom->published		 		= 1;
			$db->insertObject('#__virtuemart_customs', $custom, 'virtuemart_custom_id');
			
			$virtuemart_custom_id = $custom->virtuemart_custom_id;
			return $virtuemart_custom_id;
		}
	}
		
	private function processProductCustomAttributes($item,$ordering) {
		$attributes_string = $item['custom_attribute'];
		if (!$attributes_string) {
			return;
		}
		$attributes = explode(';',$attributes_string);
		foreach ($attributes as $attribute_name) {
			$virtuemart_custom_id = $this->createCustomFieldInputIfNotExists($attribute_name,true);
			
			$custom_value = new stdClass();
			$custom_value->virtuemart_product_id	= $item['product_id'];
			$custom_value->virtuemart_custom_id		= $virtuemart_custom_id;
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$custom_value->custom_value			= 'textinput';
				$custom_value->custom_price			= null;
				$custom_value->custom_param			= '{"custom_size":"10"}';
			} else {
				$custom_value->customfield_value 	= 'textinput';
				$custom_value->customfield_price	= null;
				$custom_value->customfield_params	= '{"custom_size":"10"}';
			}
			$custom_value->published				= 1;
			$custom_value->ordering					= $ordering;
			$this->destination_db->insertObject('#__virtuemart_product_customfields', $custom_value, 'virtuemart_customfield_id');
			$ordering++;
		
		}
	}

	private function createCustomFieldInputIfNotExists($field_name,$isCart=true) {

		$db = $this->destination_db;
		$query = $db->getQuery(true);
		$query->select('virtuemart_custom_id')
			->from('#__virtuemart_customs')
			->where('custom_title='.$db->q($field_name))
			->where('custom_value='.$db->q('textinput'))
			->where('field_type='.$db->q('E'));
		$db->setQuery($query);
		$virtuemart_custom_id = $db->loadResult();
		
		if ($virtuemart_custom_id) {
			return $virtuemart_custom_id;
		} else {
			
			$jpluginid = $this->getJPluginId('vmcustom','textinput');
			
			$custom = new stdClass();
			$custom->custom_parent_id 		= 0;
			$custom->virtuemart_vendor_id 	= $this->getVendorId();
			$custom->custom_jplugin_id 		= $jpluginid;
			$custom->custom_element 		= 'textinput';
			$custom->admin_only		 		= 0;
			$custom->custom_title	 		= $field_name;
			$custom->custom_tip		 		= '';
			$custom->custom_value	 		= 'textinput';
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$custom->custom_field_desc 	= '';
				$custom->layout_pos		 	= '';
			} else {
				$custom->custom_desc	 	= '';
				$custom->is_input	 		= $isCart;
				$custom->layout_pos		 	= ($isCart) ? 'addtocart' : '';
			}
			$custom->field_type		 		= 'E';
			$custom->is_list		 		= 0;
			$custom->is_hidden		 		= 0;
			$custom->is_cart_attribute 		= $isCart;
			$custom->custom_params	 		= 'custom_size="10"|custom_price_by_letter="0"|';
			$custom->shared			 		= 0;
			$custom->published		 		= 1;
			$db->insertObject('#__virtuemart_customs', $custom, 'virtuemart_custom_id');
			
			$virtuemart_custom_id = $custom->virtuemart_custom_id;
			return $virtuemart_custom_id;
		}
	}
	
	private function processProductProductAttributes($item,&$record_lang) {
		
		//This applies to child products only
		if ($item['product_parent_id']==0) {
			return;
		}
		
		//Let's test to see if the product has product attributes
		$query = $this->source_db->getQuery(true);
		$query->select('*')
			->from('#__'.$this->vmTablePrefix.'_product_attribute')
			->where('product_id='.$this->source_db->q($item['product_id']));
		$this->source_db->setQuery($query);
		$product_attributes = $this->source_db->loadObjectList();
		if (!count($product_attributes)) {
			//Don't go further, this product does not have any product attribute
			return;
		}
		
		//Remove existing custom fields if present
		//$this->deleteRows('#__virtuemart_product_customfields',"virtuemart_product_id='".$item['product_id']."'");
	
		//Generate the generic Child variant it custom field if not already created
		$genericChildVariantId = $this->getGenericChildVariant();
		
		foreach ($product_attributes as $product_attribute) {
			
			//Create a non-cart string custom field for each attribute if not exists
			$custom_field_id = $this->createCustomFieldIfNotExists($product_attribute->attribute_name,false);
			
			//Assign the custom field to the child product and set it's value
			$record = new stdClass();
			$record->virtuemart_product_id = $item['product_id'];
			$record->virtuemart_custom_id = $custom_field_id;
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$record->custom_value = $product_attribute->attribute_value;
			} else {
				$record->customfield_value = $product_attribute->attribute_value;
			}
			$this->destination_db->insertObject('#__virtuemart_product_customfields', $record);
			
		}
		
		//Check if the generic child variant custom field was already assigned to the parent product
		$query = $this->destination_db->getQuery(true);
		$query->select('count(*)')
			->from('#__virtuemart_product_customfields')
			->where('virtuemart_custom_id='.$this->destination_db->q($genericChildVariantId))
			->where('virtuemart_product_id='.$this->destination_db->q($item['product_parent_id']));
		$this->destination_db->setQuery($query);
		$isAlreadyAssigned = $this->destination_db->loadResult();
		
		//Assign the generic child variant custom field to the parent product.
		if (!$isAlreadyAssigned) {
			$record = new stdClass();
			$record->virtuemart_product_id = $item['product_parent_id'];
			$record->virtuemart_custom_id = $genericChildVariantId;
			if (version_compare($this->getVmVersion(),'2.9','<')) {
				$record->custom_value = 'product_sku';
				$record->custom_param = 'withParent="0"|parentOrderable="0"|';
			} else {
				$record->customfield_value 	= 'product_sku';
				$record->customfield_params = 'withParent="0"|parentOrderable="0"|';
			}
			$this->destination_db->insertObject('#__virtuemart_product_customfields', $record);
		}
		
		//Now update the child product name to reflect the attributes so user can choose the appropriate
		$nameAdditionalInfos = array();
		foreach ($product_attributes as $product_attribute) {
			$nameAdditionalInfos[] = $product_attribute->attribute_name.': '.$product_attribute->attribute_value;
		}
		$nameAdditionalInfos = implode(', ',$nameAdditionalInfos);
		//$record_lang->product_name .= ' ('.$nameAdditionalInfos.')';
	}

	private $_multiVariantId = 0;
	private function getMultiVariantCustomFieldId() {

		if ($this->_multiVariantId) {
			return $this->_multiVariantId;
		}
		
		if (version_compare($this->getVmVersion(),'3.0.6','<')) {
			return false;
		}

		$db = $this->destination_db;
		$query = $db->getQuery(true);
		$query->select('virtuemart_custom_id')
			->from('#__virtuemart_customs')
			->where('custom_title='.$db->q('Multi Variant'))
			->where('field_type='.$db->q('C'))
			->where('is_cart_attribute=1');
		$db->setQuery($query);
		$virtuemart_custom_id = $db->loadResult();
		if ($virtuemart_custom_id) {
			$this->_multiVariantId = $virtuemart_custom_id;
			return $virtuemart_custom_id;
		}
		
		$custom = new stdClass();
		$custom->custom_parent_id 		= 0;
		$custom->virtuemart_vendor_id 	= $this->getVendorId();
		$custom->custom_jplugin_id 		= 0;
		$custom->custom_element 		= '';
		$custom->admin_only		 		= 0;
		$custom->custom_title	 		= $field_name;
		$custom->custom_tip		 		= '';
		$custom->custom_value	 		= $field_name;
		$custom->custom_desc	 		= '';
		$custom->field_type		 		= 'C';
		$custom->is_input	 			= 0;
		$custom->layout_pos		 		= 'addtocart';
		$custom->is_list		 		= 0;
		$custom->is_hidden		 		= 0;
		$custom->is_cart_attribute 		= $isCart;
		$custom->custom_params	 		= '';
		$custom->shared			 		= 0;
		$custom->published		 		= 1;
		$db->insertObject('#__virtuemart_customs', $custom, 'virtuemart_custom_id');
		
		$virtuemart_custom_id = $custom->virtuemart_custom_id;
		$this->_multiVariantId = $virtuemart_custom_id;
		return $virtuemart_custom_id;
	}

	private $_genericChildVariantId = 0;
	private function getGenericChildVariant() {

		if ($this->_genericChildVariantId) {
			return $this->_genericChildVariantId;
		}
		
		//Try to find an existing automatic child variant previously created
		$db = $this->destination_db;
		$query = $db->getQuery(true);
		$query->select('virtuemart_custom_id')
			->from('#__virtuemart_customs')
			->where('field_type='.$db->q('A'));
		$db->setQuery($query);
		$virtuemart_custom_id = $db->loadResult();
		if ($virtuemart_custom_id) {
			$this->_genericChildVariantId = $virtuemart_custom_id;
			return $virtuemart_custom_id;
		}
		
		//Finally, not know neither found -> create it
			
		$custom = new stdClass();
		$custom->custom_parent_id 		= 0;
		$custom->virtuemart_vendor_id 	= $this->getVendorId();
		$custom->custom_jplugin_id 		= 0;
		$custom->custom_element 		= 0;
		$custom->admin_only		 		= 0;
		$custom->custom_title	 		= JText::_('VM_CUSTOM_FIELD_VARIANT');
		$custom->custom_tip		 		= '';
		$custom->custom_value	 		= '';
		if (version_compare($this->getVmVersion(),'2.9','<')) {
			$custom->custom_field_desc 	= '';
			$custom->layout_pos		 	= '';
		} else {
			$custom->custom_desc	 	= '';
			$custom->is_input	 		= 1;
			$custom->layout_pos		 	= 'addtocart';
		}
		$custom->field_type		 		= 'A';
		$custom->is_list		 		= 0;
		$custom->is_hidden		 		= 0;
		$custom->is_cart_attribute 		= 1;
		$custom->custom_params	 		= 0;
		$custom->shared			 		= 0;
		$custom->published		 		= 1;
		$db->insertObject('#__virtuemart_customs', $custom, 'virtuemart_custom_id');
		
		$virtuemart_custom_id = $custom->virtuemart_custom_id;
		$this->_genericChildVariantId = $virtuemart_custom_id;
		return $virtuemart_custom_id;
	}
		
	private function parseThemeConfig( $txt, $process_sections = false, $asArray = false ) {
		if (is_string( $txt )) {
			$lines = explode( "\n", $txt );
		} else if (is_array( $txt )) {
			$lines = $txt;
		} else {
			$lines = array();
		}
		$obj = $asArray ? array() : new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}
		foreach ($lines as $line) {
			// ignore comments
			if ($line && $line[0] == ';') {
				continue;
			}
			$line = trim( $line );

			if ($line == '') {
				continue;
			}
			if ($line && $line[0] == '[' && $line[strlen($line) - 1] == ']') {
				$sec_name = substr( $line, 1, strlen($line) - 2 );
				if ($process_sections) {
					if ($asArray) {
						$obj[$sec_name] = array();
					} else {
						$obj->$sec_name = new stdClass();
					}
				}
			} else {
				if ($pos = strpos( $line, '=' )) {
					$property = trim( substr( $line, 0, $pos ) );

					if (substr($property, 0, 1) == '"' && substr($property, -1) == '"') {
						$property = stripcslashes(substr($property,1,count($property) - 2));
					}
					$value = trim( substr( $line, $pos + 1 ) );
					if ($value == 'false') {
						$value = false;
					}
					if ($value == 'true') {
						$value = true;
					}
					if (substr( $value, 0, 1 ) == '"' && substr( $value, -1 ) == '"') {
						$value = stripcslashes( substr( $value, 1, count( $value ) - 2 ) );
					}

					if ($process_sections) {
						$value = str_replace( '\n', "\n", $value );
						if ($sec_name != '') {
							if ($asArray) {
								$obj[$sec_name][$property] = $value;
							} else {
								$obj->$sec_name->$property = $value;
							}
						} else {
							if ($asArray) {
								$obj[$property] = $value;
							} else {
								$obj->$property = $value;
							}
						}
					} else {
						$value = str_replace( '\n', "\n", $value );
						if ($asArray) {
							$obj[$property] = $value;
						} else {
							$obj->$property = $value;
						}
					}
				} else {
					if ($line && trim($line[0]) == ';') {
						continue;
					}
					if ($process_sections) {
						$property = '__invalid' . $unparsed++ . '__';
						if ($process_sections) {
							if ($sec_name != '') {
								if ($asArray) {
									$obj[$sec_name][$property] = trim($line);
								} else {
									$obj->$sec_name->$property = trim($line);
								}
							} else {
								if ($asArray) {
									$obj[$property] = trim($line);
								} else {
									$obj->$property = trim($line);
								}
							}
						} else {
							if ($asArray) {
								$obj[$property] = trim($line);
							} else {
								$obj->$property = trim($line);
							}
						}
					}
				}
			}
		}
		return $obj;
	}

	private function OrderItemAttributesToJson($order_item){
		
		$attributesStr = trim($order_item->product_attribute);
		$attributesStr = str_replace('_',' ',$attributesStr);
		if (!$attributesStr) {
			return;
		}
		
		$db = $this->destination_db;
		
		//Color: #1B; Length: 20";
		$attributesStr = str_replace('<br/>',';',$attributesStr);
		$attributesArray = explode(';',$attributesStr);
		$newAttributes = array();
		foreach ($attributesArray as $valueKeyVal) {
			if (!$valueKeyVal) {
				continue;
			}
			$data = explode(":", $valueKeyVal);
			$attributeName = trim($data[0]);
			$attributeValue = trim($data[1]);

			$virtuemart_custom_id = $this->getNewCustomFieldId($attributeName);
			
			//Find the customfield_id based on the attribute name for the selected product.
			$query = $db->getQuery(true);
//			$query->select('pc.virtuemart_customfield_id,pc.virtuemart_custom_id')
//				->from('#__virtuemart_customs as c')
//				->join('INNER', '#__virtuemart_product_customfields AS pc ON (c.virtuemart_custom_id = pc.virtuemart_custom_id)')
//				->where('c.custom_title='.$db->q($attributeName))
//				->where('pc.virtuemart_product_id='.$db->q($order_item->product_id));

			$query->select('virtuemart_customfield_id,virtuemart_custom_id')
				->from('#__virtuemart_product_customfields')
				->where('customfield_value='.$db->q($attributeValue))
				->where('virtuemart_product_id='.$db->q($order_item->product_id));

			$custom = $db->setQuery($query)->loadObject();;

			if ($custom) {
				$virtuemart_customfield_id = $custom->virtuemart_customfield_id;
				$virtuemart_custom_id = $custom->virtuemart_custom_id;
			} else {
				$virtuemart_custom_id = $this->getNewCustomFieldId($attributeName);
				$virtuemart_customfield_id = $this->getNewProductCustomFieldId($virtuemart_custom_id,$order_item->product_id,$attributeValue);
			}
			
			if ($virtuemart_customfield_id) {
				if (version_compare($this->getVmVersion(),'2.9.9.4','<')) {
					$newAttributes[$virtuemart_customfield_id] = $attributeValue;
				} else {
					$newAttributes[$virtuemart_custom_id] = $virtuemart_customfield_id;
				}
	
			} else {
				$newAttributes['old_attributes'][$attributeName] = $attributeValue;
			}
			
			
		}
		return json_encode($newAttributes,JSON_FORCE_OBJECT);
		
//		if ( !trim($attributes) ) return '';
//		$attributesArray = explode(";", $attributes);
//		foreach ($attributesArray as $valueKey) {
//			// do the array
//			$tmp = explode(":", $valueKey);
//			if ( count($tmp) == 2 ) {
//				if ($pos = strpos($tmp[1], '[')) $tmp[1] = substr($tmp[1], 0, $pos) ; // remove price
//				$newAttributes['attributs'][$tmp[0]] = $tmp[1];
//			}
//		}
//		return json_encode($newAttributes,JSON_FORCE_OBJECT);
	}

	private function getNewCustomFieldId($customElement) {

		$query = $this->destination_db->getQuery(true);
		$query->select('virtuemart_custom_id')
			->from('#__virtuemart_customs')
			->where('custom_title='.$this->destination_db->q($customElement));

		$this->destination_db->setQuery($query);
		$virtuemart_custom_id = $this->destination_db->loadResult();
		return $virtuemart_custom_id;
		
	}

	private function getNewProductCustomFieldId($virtuemart_custom_id,$virtuemart_product_id,$value='') {

		$query = $this->destination_db->getQuery(true);
		$query->select('virtuemart_customfield_id')
			->from('#__virtuemart_product_customfields')
			->where('virtuemart_custom_id='.$this->destination_db->q($virtuemart_custom_id))
			->where('virtuemart_product_id='.$this->destination_db->q($virtuemart_product_id));
		if ($value) {
			$query->where('customfield_value='.$this->destination_db->q($value));
		}

		$this->destination_db->setQuery($query);
		$virtuemart_customfield_id = $this->destination_db->loadResult();
		
		if (!$virtuemart_customfield_id && $value) {
			$virtuemart_customfield_id = $this->getNewProductCustomFieldId($virtuemart_custom_id,$virtuemart_product_id);
		}
		return $virtuemart_customfield_id;
		
	}

	private function getShipmentMethodId($slug,$id=0) {
		
		if ($slug=='standard_shipping' && $id) {
			return $id;
		}
		$query = $this->destination_db->getQuery(true);
		$query->select('virtuemart_shipmentmethod_id')
			->from('#__virtuemart_shipmentmethods')
			->where('shipment_element='.$this->destination_db->q($slug));

		$virtuemart_shipmentmethod_id = $this->destination_db->setQuery($query)->loadResult();
		return $virtuemart_shipmentmethod_id;
	}

	private function createLanguageTables () {
		if ($this->joomfishInstalled) {
			if(!class_exists('GenericTableUpdater')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/tableupdater.php');
			$updater = new GenericTableUpdater();
			$result = $updater->createLanguageTables();
		}
	}
	
	private function ensureRelatedCustomsFields() {

		$custom = new stdClass();
		$custom->virtuemart_custom_id	= 1;
		$custom->custom_parent_id 		= 0;
		$custom->virtuemart_vendor_id 	= $this->getVendorId();
		$custom->custom_jplugin_id 		= 0;
		$custom->custom_element 		= '';
		$custom->admin_only		 		= 0;
		$custom->custom_title	 		= 'COM_VIRTUEMART_RELATED_PRODUCTS';
		$custom->custom_tip		 		= 'COM_VIRTUEMART_RELATED_PRODUCTS_TIP';
		$custom->custom_value	 		= 'related_products';
		if (version_compare($this->getVmVersion(),'2.9','<')) {
			$custom->custom_field_desc 	= 'COM_VIRTUEMART_RELATED_PRODUCTS_DESC';
		} else {
			$custom->custom_desc	 	= 'COM_VIRTUEMART_RELATED_PRODUCTS_DESC';
			$custom->is_input	 		= 0;
		}
		$custom->field_type		 		= 'R';
		$custom->layout_pos		 		= null;
		$custom->is_list		 		= 0;
		$custom->is_hidden		 		= 0;
		$custom->is_cart_attribute 		= 0;
		$custom->custom_params	 		= '';
		$custom->shared			 		= 0;
		$custom->published		 		= 1;
		$custom->ordering		 		= 0;
		$this->insertOrReplace('#__virtuemart_customs', $custom, 'virtuemart_custom_id');

		$custom->virtuemart_custom_id	= 2;
		$custom->custom_title	 		= 'COM_VIRTUEMART_RELATED_CATEGORIES';
		$custom->custom_tip		 		= 'COM_VIRTUEMART_RELATED_CATEGORIES_TIP';
		$custom->custom_value	 		= 'related_products';
		if (version_compare($this->getVmVersion(),'2.9','<')) {
			$custom->custom_field_desc 	= 'COM_VIRTUEMART_RELATED_CATEGORIES_DESC';
		} else {
			$custom->custom_desc	 	= 'COM_VIRTUEMART_RELATED_CATEGORIES_DESC';
		}
		$this->insertOrReplace('#__virtuemart_customs', $custom, 'virtuemart_custom_id');
	}
	
	private function formatFieldType($fieldType) {
		switch($fieldType) {
			case 'date':
				$fieldType = 'DATE';
				break;
			case 'editorta':
			case 'textarea':
			case 'multiselect':
			case 'multicheckbox':
				$fieldType = 'MEDIUMTEXT';
				break;
			case 'checkbox':
				$fieldType = 'TINYINT';
				break;
			case 'age_verification':
				//$this->params = 'minimum_age='.(int)$_data['minimum_age']."\n";
			default:
				$fieldType = 'VARCHAR(255)';
				break;
		}

		return $fieldType;
	}

}
