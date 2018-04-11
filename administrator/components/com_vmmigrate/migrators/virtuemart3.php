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

class VMMigrateModelVirtuemart3 extends VMMigrateModelBase {

	public $isPro = true;
	
	var $vendorModel;
	var $vendor;
	var $mediaModel;
	var $active_languages;
	var $default_language;
	var $active_languages_ext;
	var $default_language_ext;
	
    function __construct($config = array()) {

        parent::__construct($config);

		if (!self::isInstalledDest('com_virtuemart')) {
			return false;
		}

		//if (version_compare($this->joomla_version_src, '3', '<')) {
		//	return false;
		//}

		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php')) {
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
			$config = VmConfig::loadConfig(TRUE);
			if(!class_exists('Permissions') && JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php')) 
				require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php');
		}

    }
	
	private function getLanguages() {

		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		$config = VmConfig::loadConfig(TRUE);
		if(!class_exists('Permissions') && JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php');

		$this->limit = 25;
		$this->active_languages = $config->get('active_languages');
		if (!$this->active_languages) {
			$this->active_languages = array('en-GB');
		}

		$this->active_languages_ext = array();
		$i=0;

		//Get the source languages defined in Virtuemart.
		$sourceLanguages = $this->getSrcConfigValue('active_languages',array());
		if (!count($sourceLanguages)) {
			//We need to get the default Joomla Language
			//$sourceLanguages[] = $this->getSrcComponentSetting('com_languages','site', 'toto');
			$sourceLanguages = $this->getInstalledContentLanguages($this->source_db);
			//Do we get the english too ?
			$enableEnglish = $this->getSrcConfigValue('enableEnglish',true);
			if ($enableEnglish && !in_array('en-GB',$sourceLanguages)) {
				$sourceLanguages[] = 'en-GB';
			}
		}
		
		//Now get a list of languages locally
		//$destLanguages = $config->get('active_languages',array());
		$destLanguages = $this->getInstalledContentLanguages($this->destination_db);
//		if (!count($destLanguages)) {
//			//We need to get the default Joomla Language
//			//Do we get the english too ?
//			$enableEnglish = $config->get('enableEnglish',true);
//			if ($enableEnglish && !in_array('en-GB',$destLanguages)) {
//				$destLanguages[] = 'en-GB';
//			}
//		}
		
		//Finally we get a list of combined languages
		$filtered_languages = array_intersect($sourceLanguages,$destLanguages);
		$this->logDebug($sourceLanguages,'Source languages');
		$this->logDebug($destLanguages,'Destination languages');
		$this->logDebug($filtered_languages,'Filtered languages');
		
		if (!count($filtered_languages)) {
			$this->logError(JText::sprintf('VMMIGRATE_ERROR_LANGUAGES_CONFIG',implode(',',$sourceLanguages),implode(',',$destLanguages)));
		}
		
		//$sourceLanguages = $this->getCombinedLanguages();		
		
		//$filtered_languages = array();
		//foreach ($this->active_languages as $k=>$lang) {
		//	if (in_array($lang,$sourceLanguages)) {
		//		$filtered_languages[] = $lang;
		//	}
		//}
		$this->active_languages = $filtered_languages; 
		
		foreach ($this->active_languages as $language_code) {
			$language_suffix = $this->getLanguageTableSuffix($language_code);
			$this->active_languages_ext[$language_suffix] = $language_suffix;
			if ($i==0) {
				$this->default_language = $language_code;
				$this->default_language_ext = $language_suffix;
			}
			$i++;
		}
	}
	
	static $_languagesExt = array();
	private function getLanguagesExt() {
		if(!count(self::$_languagesExt)) {
			$this->getLanguages();
			self::$_languagesExt = $this->active_languages_ext;
		}
		return self::$_languagesExt;
	}

	private function getDstConfigValue($key,$default='') {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		$config = VmConfig::loadConfig(TRUE);
		$value = $config->get($key,$default);
		return $value;
	}
	
	private function getSrcConfigValue($key,$default='') {

		$value = '';
		if (!VMMigrateHelperDatabase::isValidConnection()) {
			return $default;
		}
		if (!VMMigrateHelperDatabase::isValidPrefix()) {
			return $default;
		}
		if (!VMMigrateHelperDatabase::tableExists($this->source_db,'#__virtuemart_configs')) {
			return $default;
		}

		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		$config = VmConfig::loadConfig(TRUE);
		if(!class_exists('Permissions') && JFile::exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/permissions.php');

		$srcVersion = $this->getSrcVersion();
		if (version_compare($srcVersion,'3.0','<')) {
			return $default;
		}
		if ($key) {

			$query = $this->source_db->getQuery(true);
			$query->select('config')->from('#__virtuemart_configs')->where('virtuemart_config_id=1');
			$rawConfig = $this->source_db->setQuery($query)->loadResult();
			$srcConfig = VmConfig::loadConfig(false,true); //Load a fresh config
			$srcConfig->_raw = $rawConfig;
			$srcConfig->setParams($srcConfig->_raw);
			
			if (!empty($srcConfig->_params)) {
				if(array_key_exists($key,$srcConfig->_params) && isset($srcConfig->_params[$key])){
					$value = $srcConfig->_params[$key];
				} else {
					$value = $default;
				}
			} else {
				$value = $default;
			}
		}

		return $value;
		
	}
	
	
	private function getLanguageTableSuffix($lang) {
		return strtolower(str_replace('-','_',$lang));
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
		$instance = new  VMMigrateModelVirtuemart3();
		$srcVersion = $instance->getSrcVersion();
		if (version_compare($srcVersion,'3.0','<')) {
			$messages['error'][] = JText::_('VIRTUEMART3_WRONG_VERSION_SRC');
		}
		return $messages;
	}
	

	public static function getSteps() {
		if (!self::isInstalledBoth('com_virtuemart')) {
			return array();
		}
		$instance = new  VMMigrateModelVirtuemart3();
		$srcVersion = $instance->getSrcVersion();
		if (version_compare($srcVersion,'3.0','<')) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'				,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_LOG'));
		$steps[] = array('name'=>'reset_log_error'			,'default'=>1);
		$steps[] = array('name'=>'reset_data'				,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_DATA'));
		$steps[] = array('name'=>'reset_vm'					,'default'=>0, 'warning'=>JText::_('RESET_VM_WARNING'));
		$steps[] = array('name'=>'menu_items'				,'default'=>1);
		$steps[] = array('name'=>'set_config'				,'default'=>1);
		$steps[] = array('name'=>'create_languages'			,'default'=>1);
		$steps[] = array('name'=>'vm_medias'				,'default'=>1);
		$steps[] = array('name'=>'vm_currencies'			,'default'=>0);
		$steps[] = array('name'=>'vm_countries'				,'default'=>0);
		$steps[] = array('name'=>'vm_vendor'				,'default'=>1);
		$steps[] = array('name'=>'vm_tax_calc'				,'default'=>1);
		$steps[] = array('name'=>'vm_payment_method'		,'default'=>1);
		$steps[] = array('name'=>'vm_shipment_method'		,'default'=>1);
		$steps[] = array('name'=>'vm_shopper_group'			,'default'=>1);
		$steps[] = array('name'=>'vm_category'				,'default'=>1);
		$steps[] = array('name'=>'vm_manufacturer_category'	,'default'=>1);
		$steps[] = array('name'=>'vm_manufacturer'			,'default'=>1);
		$steps[] = array('name'=>'vm_order_status'			,'default'=>1);
		$steps[] = array('name'=>'vm_userfield'				,'default'=>1);
		$steps[] = array('name'=>'vm_shopper_vendor_xref'	,'default'=>1);
		$steps[] = array('name'=>'vm_customfields'			,'default'=>1);
		$steps[] = array('name'=>'vm_product'				,'default'=>1);
		$steps[] = array('name'=>'vm_product_medias'		,'default'=>1);
		$steps[] = array('name'=>'vm_product_reviews'		,'default'=>1);
		$steps[] = array('name'=>'vm_waiting_list'			,'default'=>1);
		$steps[] = array('name'=>'vm_orders'				,'default'=>1);
		$steps[] = array('name'=>'vm_coupons'				,'default'=>1);
		$steps[] = array('name'=>'vm_plugin_data'			,'default'=>1);
		return $steps;
	}
	
	public function reset_data() {

		$resetall = (count($this->steps)==1 && $this->steps[0]=='reset_data');
		$sql = '';
		if (in_array('vm_vendor',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_vendors;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_vendors_". $language_ext.";";
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_vendor_medias;";
		}
		if (in_array('vm_tax_calc',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_calcs;";
			$sql .= "TRUNCATE TABLE #__virtuemart_calc_countries;";
			$sql .= "TRUNCATE TABLE #__virtuemart_calc_states;";
			$sql .= "TRUNCATE TABLE #__virtuemart_calc_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_calc_shoppergroups;";
		}
		if (in_array('vm_payment_method',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_paymentmethods;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_paymentmethods_".$language_ext.";";
			}
		}
		if (in_array('vm_currencies',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_currencies;";
		}
		if (in_array('vm_countries',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_countries;";
			$sql .= "TRUNCATE TABLE #__virtuemart_states;";
		}
		if (in_array('vm_shipment_method',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_shipmentmethods;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_shipmentmethods_".$language_ext.";";
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
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_categories_".$language_ext.";";
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_category_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_category_medias;";
		}
		if (in_array('vm_manufacturer_category',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturercategories;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_manufacturercategories_".$language_ext.";";
			}
		}
		if (in_array('vm_manufacturer',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_manufacturers;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				$sql .= "TRUNCATE TABLE #__virtuemart_manufacturers_".$language_ext.";";
			}
		}
		if (in_array('vm_order_status',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_orderstates WHERE order_status_code NOT IN ('S','R','X','C','U','P');";
		}
		if (in_array('vm_userfield',$this->steps) || $resetall) {
			//$sql .= "DELETE FROM #__virtuemart_userfields WHERE sys = 0;";
			$sql .= "DELETE FROM #__virtuemart_userfields;";
			$sql .= "DELETE FROM #__virtuemart_userfield_values WHERE virtuemart_userfield_id NOT IN (SELECT virtuemart_userfield_id FROM #__virtuemart_userfields);";
		}
		if (in_array('vm_shopper_vendor_xref',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_userinfos;";
			$sql .= "TRUNCATE TABLE #__virtuemart_vmuser_shoppergroups;";
			$sql .= "TRUNCATE TABLE #__virtuemart_vmusers;";
		}
		if (in_array('vm_product',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_products;";
			foreach ($this->getLanguagesExt() as $language_ext) {
				if ($language_ext) {
					$sql .= "TRUNCATE TABLE #__virtuemart_products_".$language_ext.";";
				}
			}
			$sql .= "TRUNCATE TABLE #__virtuemart_product_customfields;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_categories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_manufacturers;";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_prices;";
		}
		if (in_array('vm_product_medias',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__virtuemart_medias WHERE file_type='product';";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_medias;";
		}
		if (in_array('vm_waiting_list',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_waitingusers;";
		}
		if (in_array('vm_product_reviews',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_rating_reviews;";
			$sql .= "TRUNCATE TABLE #__virtuemart_rating_votes;";
			$sql .= "TRUNCATE TABLE #__virtuemart_ratings;";
		}
		if (in_array('vm_customfields',$this->steps) || $resetall) {
			//$sql .= "DELETE FROM #__virtuemart_customs WHERE field_type NOT IN ('R','Z');";
			$sql .= "TRUNCATE TABLE #__virtuemart_product_customfields;";
		}
		if (in_array('vm_orders',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_orders;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_histories;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_items;";
			$sql .= "TRUNCATE TABLE #__virtuemart_order_userinfos;";
			$sql .= "TRUNCATE TABLE #__virtuemart_invoices;";
		}
		if (in_array('vm_coupons',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__virtuemart_coupons;";
		}

		if (in_array('vm_plugin_data',$this->steps) || $resetall) {

			$query = "SHOW TABLES LIKE '%virtuemart_%_plg_%'";
			$tables = $this->source_db->setQuery($query)->loadColumn();
			foreach ($tables as $plugin_table) {
				$table_name = str_replace($this->source_db->getPrefix(),'#__',$plugin_table);
				$sql .= "TRUNCATE TABLE ".$this->destination_db->qn($table_name).";";
			}
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
	
	public function getSrcVersion() {
		
		if ($this->source_filehelper->FileExists('/administrator/components/com_virtuemart/version.php' )) { 
			$buffer = $this->source_filehelper->ReadFile('/administrator/components/com_virtuemart/version.php');
			try {
				if (!class_exists('vmVersionSrc3')) {
					$buffer = str_replace('vmVersion','vmVersionSrc3',$buffer);
					$buffer = str_replace('<?php','',$buffer);
					$buffer = str_replace('?>','',$buffer);
					if (version_compare(phpversion(), '7', '>=')) {
						//php7: New objects cannot be assigned by reference
						$buffer = str_replace('=&','=',$buffer);
						$buffer = str_replace('= &','=',$buffer);
					}
					eval($buffer);
				}
				$VMVERSION = new vmVersionSrc3();
				if (isset($VMVERSION->RELEASE)) {
					$version = $VMVERSION->RELEASE;
				} else {
					$version = vmVersionSrc3::$RELEASE;
				}
				if (version_compare($version,'3','<')) {
					$version='';
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

	public function reset_vm() {

		try {
			$model = VmModel::getModel('updatesMigration');
			$model->restoreSystemTablesCompletly();
			$this->logInfo(JText::sprintf('DATA_RESETED',''));
		} catch (Exception $e) {
			$this->logError(JText::_('DATA_RESET_ERROR'));
		}
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
		
		foreach ($menu_items as $menu_item) {
			$break = false;
			$uri = JURI::getInstance($menu_item->link);
			$view = $uri->getVar('view','');

			if ($view == 'categories') {
				$uri->setVar('view','category');
			}
			
			$menu_item->link 			= $uri->toString();
			$menu_item->component_id	= $vm_extensionid;
			
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

	public function set_config() {

		return $this->copy_one2one('virtuemart_configs','virtuemart_config_id');
	}
	
	public function create_languages() {

		if(!class_exists('GenericTableUpdater')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/tableupdater.php');
		$updater = new GenericTableUpdater();
		
		//Create all language tables form the Joomla installed languages (source)
		$J_languages_src = $this->getInstalledContentLanguages($this->source_db);
		$result = $updater->createLanguageTables($J_languages_src);

		//Create all language tables form the Joomla installed languages (destination)
		$J_languages_dest = $this->getInstalledContentLanguages($this->destination_db);
		$result = $updater->createLanguageTables($J_languages_dest);

		//Create all language tables form the Virtuemart languages (computed languages source and dest)
		//$config = VmConfig::loadConfig(TRUE);
		//$active_languages = $config->get('active_languages');
		//$this->getLanguages();
		//$result = $updater->createLanguageTables($this->active_languages);
		
		$logged = array();
		$all_languages = array_merge($J_languages_src,$J_languages_dest);
		foreach ($all_languages as $language_code) {
			if (!in_array($language_code,$logged)) {
				$img = '';
				$imgName = strtolower(str_replace('-','_',$language_code));
				$imgShortName = substr($imgName,0,2);
				if (JFile::exists(JPATH_SITE.'/media/mod_languages/images/'.$imgName.'.gif')) {
					$img = ' <img src="'.JURI::root().'/media/mod_languages/images/'.$imgName.'.gif"> ';
				} else if (JFile::exists(JPATH_SITE.'/media/mod_languages/images/'.$imgShortName.'.gif')) {
					$img = ' <img src="'.JURI::root().'/media/mod_languages/images/'.$imgShortName.'.gif"> ';
				}
				$this->logInfo(JText::_('VM_LANGUAGE_TABLE_CREATED').' '.$language_code.$img);
				$logged[] = $language_code;
			}
		}

	}

    public function vm_vendor() {

		$table = 'virtuemart_vendors';
        $pk = 'virtuemart_vendor_id';
        $name_col = 'vendor_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			$media_relations = $this->get_records('virtuemart_vendor_medias',$pk.'='.$srcid);
			$medias = array();
			foreach ($media_relations as $media) {
				$medias[] = $this->get_record('virtuemart_medias','virtuemart_media_id='.$media->virtuemart_media_id);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}
				//Media Relations
				foreach ($media_relations as $media) {
					$this->insertOrReplace('#__virtuemart_vendor_medias', $media, $pk);
				}
				//Medias
				foreach ($medias as $media) {
					$media->ordering = null;
					$this->insertOrReplace('#__virtuemart_medias', $media, 'virtuemart_media_id');
					$this->CopyMediaFiles($record_media);
				}
				
				$this->logRow($srcid,$record->{$name_col}.','.JText::sprintf('VM_INFO_MEDIAS_X',count($medias)));

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
	
	public function vm_tax_calc() {

		$table = 'virtuemart_calcs';
        $pk = 'virtuemart_calc_id';
        $name_col = 'calc_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		$srcVersion = $this->getSrcVersion();

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

			$calc_categories = $this->get_records('virtuemart_calc_categories',$pk.'='.$srcid);
			$calc_countries = $this->get_records('virtuemart_calc_countries',$pk.'='.$srcid);
			$calc_manufacturers = array();
			$calc_manufacturers = $this->get_records('virtuemart_calc_manufacturers',$pk.'='.$srcid);
			$calc_shoppergroups = $this->get_records('virtuemart_calc_shoppergroups',$pk.'='.$srcid);
			$calc_states = $this->get_records('virtuemart_calc_states',$pk.'='.$srcid);

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Relations
				foreach ($calc_categories as $child_record) {
					$this->insertOrReplace('#__virtuemart_calc_categories', $child_record, 'id');
				}
				foreach ($calc_countries as $child_record) {
					$this->insertOrReplace('#__virtuemart_calc_countries', $child_record, 'id');
				}
				foreach ($calc_manufacturers as $child_record) {
					$this->insertOrReplace('#__virtuemart_calc_manufacturers', $child_record, 'id');
				}
				foreach ($calc_shoppergroups as $child_record) {
					$this->insertOrReplace('#__virtuemart_calc_shoppergroups', $child_record, 'id');
				}
				foreach ($calc_states as $child_record) {
					$this->insertOrReplace('#__virtuemart_calc_states', $child_record, 'id');
				}

				$this->logRow($srcid,$record->{$name_col});
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
	
	public function vm_payment_method() {

		$table = 'virtuemart_paymentmethods';
        $pk = 'virtuemart_paymentmethod_id';
        $name_col = 'payment_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$record->slug = null;

			//Get the new Joomla Plugin Id
			$JPluginId = $this->getJPluginId('vmpayment',$record->payment_element);
			if (!$JPluginId) {
				$this->logWarning(JText::sprintf('VM_WARNING_MISSING_PLUGIN','vmpayment',$record->payment_element));			
				$this->logWarning(JText::sprintf('VM_WARNING_PAYMENT',$record->payment_element));			
			}
			$record->payment_jplugin_id = $JPluginId;

			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}
			
			//Extended table for known plugins
			$createScript = '';
			$records_extended = array();
			$extended_table_name = '#__virtuemart_payment_plg_'.$record->payment_element;
			if ($JPluginId && $this->table_exists($this->source_db,$extended_table_name)) {
				//Check if the destination table was already created
				if (!$this->table_exists($this->destination_db,$extended_table_name)) {
					//Not yet created, we need to create it
					$createScript = VMMigrateHelperDatabase::getCreateTableScript($this->source_db,$extended_table_name);
				}
				$records_extended = $this->get_records('virtuemart_payment_plg_'.$record->payment_element);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}
				//Create Extended Table
				if ($createScript) {
					$this->destination_db->setQuery($createScript)->query();
				}
				if (is_array($records_extended)) {
					//Dump Extended table records
					if ($this->table_exists($this->destination_db,$extended_table_name)) {
						$this->deleteRows($extended_table_name);
					}
					foreach ($records_extended as $record_extended) {
						$this->destination_db->insertObject($extended_table_name, $record_extended);
					}
				}
				
				
				$this->logRow($srcid,$records_lang[$this->default_language_ext]->{$name_col});
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

		$table = 'virtuemart_shipmentmethods';
        $pk = 'virtuemart_shipmentmethod_id';
        $name_col = 'shipment_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$record->slug = null;
			
			//Get the new Joomla pLugin Id
			$JPluginId = $this->getJPluginId('vmshipment',$record->shipment_element);
			if (!$JPluginId) {
				$this->logWarning(JText::sprintf('VM_WARNING_MISSING_PLUGIN','vmshipment',$record->shipment_element));			
				$this->logWarning(JText::sprintf('VM_WARNING_SHIPMENT',$record->shipment_element));			
			}
			$record->shipment_jplugin_id = $JPluginId;

			$record->shipment_params = str_replace('|cost=','|shipment_cost=',$record->shipment_params);

			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			//Extended table for known plugins
			$createScript = '';
			$records_extended = array();
			$extended_table_name = '#__virtuemart_shipment_plg_'.$record->shipment_element;
			if ($JPluginId && $this->table_exists($this->source_db,$extended_table_name)) {
				//Check if the destination table was already created
				if (!$this->table_exists($this->destination_db,$extended_table_name)) {
					//Not yet created, we need to create it
					$createScript = VMMigrateHelperDatabase::getCreateTableScript($this->source_db,$extended_table_name);
				}
				$records_extended = $this->get_records('virtuemart_shipment_plg_'.$record->shipment_element);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}
				
				//Create Extended Table
				if ($createScript) {
					$this->destination_db->setQuery($createScript)->query();
				}
				if (is_array($records_extended)) {
					//Dump Extended table records
					if ($this->table_exists($this->destination_db,$extended_table_name)) {
						$this->deleteRows($extended_table_name);
					}
					//Import Extended records
					foreach ($records_extended as $record_extended) {
						$this->destination_db->insertObject($extended_table_name, $record_extended);
					}
				}

				$this->logRow($srcid,$records_lang[$this->default_language_ext]->{$name_col});
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
	
    public function vm_shopper_group() {

		$table = 'virtuemart_shoppergroups';
        $pk = 'virtuemart_shoppergroup_id';
        $name_col = 'shopper_group_name';
		return $this->copy_one2one($table,$pk,null,null,null,$name_col);

    }
	
    public function vm_currencies() {

		$table = 'virtuemart_currencies';
        $pk = 'virtuemart_currency_id';
        $name_col = 'currency_name';
		return $this->copy_one2one($table,$pk,null,null,null,$name_col);

    }
	
    public function vm_countries() {

		$table = 'virtuemart_countries';
        $pk = 'virtuemart_country_id';
        $name_col = 'country_name';

		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			
			$record_states = $this->get_records('virtuemart_states','virtuemart_country_id='.$srcid);
		
			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				$this->logRow($srcid,$record->{$name_col});
				
				//States
				$this->deleteRows('#__virtuemart_states',$pk."='".$srcid."'");
				foreach ($record_states as $record_state) {
					$this->insertOrReplace('#__virtuemart_states', $record_state, 'virtuemart_state_id');
					$this->logInfo('-- '.$record_state->state_name);
				}
				//States
				//$this->insertOrReplace('#__virtuemart_states', $record_states, 'virtuemart_state_id');
				
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
	
	public function vm_category() {

		if ($this->source_filehelper->mode == 'ftp') {
			$this->limit = 5;
		}
		
		$table = 'virtuemart_categories';
        $pk = 'virtuemart_category_id';
        $name_col = 'category_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		//Ensure the table has all the necessary fields
		VMMigrateHelperDatabase::copyTableStructure($this->source_db,'#__'.$table);

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$record->limit_list_start = null;
			$record->limit_list_max = null;
			
			$record_hierarchy = $this->get_records('virtuemart_category_categories','category_child_id='.$srcid);
			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			$media_relations = $this->get_records('virtuemart_category_medias',$pk.'='.$srcid);
			$medias = array();
			foreach ($media_relations as $media) {
				$medias[] = $this->get_record('virtuemart_medias','virtuemart_media_id='.$media->virtuemart_media_id);
			}
		
			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				$this->deleteRows('#__'.$table.'_'.$language_ext,$pk."='".$srcid."'");
				foreach ($records_lang as $language_ext=>$record_lang) {
					//$slug = JApplication::stringURLSafe($record_lang->slug);
					//$record_lang->slug = $this->checkSlug('#__'.$table.'_'.$language_ext,$slug,'slug',$srcid,$pk);
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}
				//Hierarchy
				$this->insertOrReplace('#__virtuemart_category_categories', $record_hierarchy, 'category_child_id');
				//Media Relations
				foreach ($media_relations as $media) {
					$this->insertOrReplace('#__virtuemart_category_medias', $media, $pk);
				}
				//Medias
				foreach ($medias as $media) {
					$media->ordering = null;
					$this->insertOrReplace('#__virtuemart_medias', $media, 'virtuemart_media_id');
					$this->CopyMediaFiles($record_media);
				}
				
				$this->logRow($srcid,$records_lang[$this->default_language_ext]->{$name_col}.','.JText::sprintf('VM_INFO_MEDIAS_X',count($medias)));
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

		$table = 'virtuemart_manufacturercategories';
        $pk = 'virtuemart_manufacturercategories_id';
        $name_col = 'mf_category_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}

				$this->logRow($srcid,$records_lang[$this->default_language_ext]->{$name_col});
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

		$table = 'virtuemart_manufacturers';
        $pk = 'virtuemart_manufacturer_id';
        $name_col = 'mf_name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		//Ensure the table has all the necessary fields
		VMMigrateHelperDatabase::copyTableStructure($this->source_db,'#__'.$table);

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			$media_relations = $this->get_records('virtuemart_manufacturer_medias',$pk.'='.$srcid);
			$medias = array();
			foreach ($media_relations as $media) {
				$medias[] = $this->get_record('virtuemart_medias','virtuemart_media_id='.$media->virtuemart_media_id);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
				}
				//Media Relations
				foreach ($media_relations as $media) {
					$media->ordering = null;
					$this->insertOrReplace('#__virtuemart_manufacturer_medias', $media, $pk);
				}
				//Medias
				foreach ($medias as $media) {
					$media->ordering = null;
					$this->insertOrReplace('#__virtuemart_medias', $media, 'virtuemart_media_id');
					$this->CopyMediaFiles($media);
				}

				$this->logRow($srcid,$records_lang[$this->default_language_ext]->{$name_col}.','.JText::sprintf('VM_INFO_MEDIAS_X',count($medias)));
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

		$table = 'virtuemart_orderstates';
        $pk = 'virtuemart_orderstate_id';
        $name_col = 'order_status_code';
		return $this->copy_one2one($table,$pk,null,null,null,$name_col);

	}
	
	public function vm_userfield() {

		$table = 'virtuemart_userfields';
        $pk = 'virtuemart_userfield_id';
        $name_col = 'name';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$record->userfield_params = $record->params;
			$record->params = null;

			$userfield_values = $this->get_records('virtuemart_userfield_values',$pk.'='.$srcid.' AND (fieldtitle<>\'\' OR fieldvalue<>\'\')');
			
			try {
				$this->destination_db->transactionStart();

				//Main record 
				$this->deleteRows('#__'.$table,"name =".$this->destination_db->q($record->name));
				$this->insertOrReplace('#__'.$table, $record, $pk);
				
				$type = $this->formatFieldType($record->type);
				VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_userinfos',$record->name,$type);
				VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_order_userinfos',$record->name,$type);
				
				//Values
				$this->deleteRows('#__virtuemart_userfield_values',"virtuemart_userfield_id ='".$srcid."'");
				foreach ($userfield_values as $child_record) {
					$this->insertOrReplace('#__virtuemart_userfield_values', $child_record, 'virtuemart_userfield_value_id');
				}
				$this->logRow($srcid,$record->{$name_col});
				foreach ($userfield_values as $child_record) {
					$this->logInfo(' -  '.$child_record->fieldtitle. '/'.$child_record->fieldvalue);
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
	
	public function vm_shopper_vendor_xref() {

		$table = 'virtuemart_vmusers';
        $pk = 'virtuemart_user_id';
        $name_col = 'customer_number';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		//Ensure the table has all the necessary fields
		VMMigrateHelperDatabase::copyTableStructure($this->source_db,'#__virtuemart_userinfos');
		
        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			$record->perms = null;

			$vmuser_shoppergroups = $this->get_records('virtuemart_vmuser_shoppergroups',$pk.'='.$srcid);
			$userinfos = $this->get_records('virtuemart_userinfos',$pk.'='.$srcid);

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Relations
				foreach ($vmuser_shoppergroups as $child_record) {
					$this->insertOrReplace('#__virtuemart_vmuser_shoppergroups', $child_record, 'id');
				}
				$this->deleteRows('#__virtuemart_userinfos',"virtuemart_user_id ='".$srcid."'");
				foreach ($userinfos as $child_record) {
					$this->insertOrReplace('#__virtuemart_userinfos', $child_record, 'virtuemart_userinfo_id');
				}

				$this->logRow($srcid,$record->{$name_col});
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
	
	public function vm_product() {

		$table = 'virtuemart_products';
        $pk = 'virtuemart_product_id';
        $name_col = 'product_sku';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

			$records_lang = array();
			foreach ($this->getLanguagesExt() as $language_ext) {
				$translation = $this->get_record($table.'_'.$language_ext,$pk.'='.$srcid);
				if (!$translation) {
					$translation = $this->get_record($table.'_en_gb',$pk.'='.$srcid);
				}
				$records_lang[$language_ext] = $translation;
			}

			$product_categories = $this->get_records('virtuemart_product_categories',$pk.'='.$srcid);
			$product_customfields = $this->get_records('virtuemart_product_customfields',$pk.'='.$srcid);
			$product_manufacturers = $this->get_records('virtuemart_product_manufacturers',$pk.'='.$srcid);
			$product_prices = $this->get_records('virtuemart_product_prices',$pk.'='.$srcid);
			//$product_relations = $this->get_records('virtuemart_product_relations',$pk.'='.$srcid);
			$product_shoppergroups = $this->get_records('virtuemart_product_shoppergroups',$pk.'='.$srcid);

			$media_relations = $this->get_records('virtuemart_product_medias',$pk.'='.$srcid);
			$medias = array();
			foreach ($media_relations as $media) {
				$medias[] = $this->get_record('virtuemart_medias','virtuemart_media_id='.$media->virtuemart_media_id);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Languages
				foreach ($records_lang as $language_ext=>$record_lang) {
					if ($language_ext) {
						if ($record_lang->product_name) {
							$record_lang->product_name = htmlspecialchars(html_entity_decode($record_lang->product_name, ENT_QUOTES, "UTF-8"), ENT_QUOTES, "UTF-8");
						}
						$this->insertOrReplace('#__'.$table.'_'.$language_ext, $record_lang, $pk);
					}
				}

				$this->deleteRows('#__virtuemart_product_categories',$pk."='".$srcid."'");
				foreach ($product_categories as $child_record) {
					$this->insertOrReplace('#__virtuemart_product_categories', $child_record, 'id');
				}
				foreach ($product_customfields as $child_record) {
						if (isset($child_record->custom_value)) {
							$child_record->customfield_value 	= $child_record->custom_value;
							$child_record->custom_value			= null;
						}
						if (isset($child_record->custom_value)) {
							$child_record->customfield_price	= $child_record->custom_price;
							$child_record->custom_price			= null;
						}

						$params = json_decode($child_record->custom_param);
						$params_str = '';
						foreach ($params as $key => $v) {
							$params_str .= $key . '=' . json_encode($v) . '|';
						}
						$child_record->customfield_params	= $params_str;
						$child_record->custom_param			= null;
					$this->insertOrReplace('#__virtuemart_product_customfields', $child_record, 'virtuemart_customfield_id');
				}
				foreach ($product_manufacturers as $child_record) {
					$this->insertOrReplace('#__virtuemart_product_manufacturers', $child_record, 'id');
				}
				foreach ($product_prices as $child_record) {
					if (isset($child_record->product_price_vdate)) {
						$child_record->product_price_publish_up = $child_record->product_price_vdate;
						$child_record->product_price_vdate = null;
					}
					if (isset($child_record->product_price_edate)) {
						$child_record->product_price_publish_down = $child_record->product_price_edate;
						$child_record->product_price_edate = null;
					}
					$this->insertOrReplace('#__virtuemart_product_prices', $child_record, 'virtuemart_product_price_id');
				}
				foreach ($product_relations as $child_record) {
					$this->insertOrReplace('#__virtuemart_product_relations', $child_record, 'id');
				}
				foreach ($product_shoppergroups as $child_record) {
					$this->insertOrReplace('#__virtuemart_product_shoppergroups', $child_record, 'id');
				}

				$this->logRow($srcid,$record->{$name_col});
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
        }
		if ($this->moreResults) {
			return true;
		}
		return false;
	}
	
	public function vm_medias() {

		if ($this->source_filehelper->mode == 'ftp') {
			$this->limit = 1;
		}

		$table = 'virtuemart_medias';
        $pk = 'virtuemart_media_id';
        $name_col = 'file_title';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$media = JArrayHelper::toObject($item);
			$media->ordering = null;

			try {
				$this->destination_db->transactionStart();

				//Medias
				$result = $this->insertOrReplace('#__virtuemart_medias', $media, 'virtuemart_media_id');
				$this->CopyMediaFiles($media);

				//Media Relations

				$this->logRow($srcid,$media->{$name_col}.','.JText::sprintf('VM_INFO_MEDIAS_X',($result)?1:0));
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

		$table = 'virtuemart_products';
        $pk = 'virtuemart_product_id';
        $name_col = 'product_sku';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

			$media_relations = $this->get_records('virtuemart_product_medias',$pk.'='.$srcid);
			$medias = array();
			foreach ($media_relations as $media) {
				$medias[] = $this->get_record('virtuemart_medias','virtuemart_media_id='.$media->virtuemart_media_id);
			}

			try {
				$this->destination_db->transactionStart();

				//Medias
				foreach ($medias as $media) {
					$media->ordering = null;
					$this->insertOrReplace('#__virtuemart_medias', $media, 'virtuemart_media_id');
					$this->CopyMediaFiles($media);
				}
				//Media Relations
				$this->deleteRows('#__virtuemart_product_medias',"virtuemart_product_id=".$this->destination_db->q($srcid));
				foreach ($media_relations as $media_relation) {
					$this->destination_db->insertObject('#__virtuemart_product_medias', $media_relation, 'id');
					//$this->insertOrReplace('#__virtuemart_product_medias', $media_relation, $pk);
				}

				$this->logRow($srcid,$record->{$name_col}.','.JText::sprintf('VM_INFO_MEDIAS_X',count($medias)));
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
	
	public function vm_waiting_list() {

		$table = 'virtuemart_waitingusers';
        $pk = 'virtuemart_waitinguser_id';
        $name_col = 'notify_email';
		return $this->copy_one2one($table,$pk,null,null,null,$name_col);

	}
	
	public function vm_product_reviews() {

		$moreratings = $this->copy_one2one('virtuemart_ratings','virtuemart_rating_id');
		$morereviews = $this->copy_one2one('virtuemart_rating_reviews','virtuemart_rating_review_id');
		$morevotes = $this->copy_one2one('virtuemart_rating_votes','virtuemart_rating_vote_id');
		
		if ($moreratings  || $morereviews || $morevotes) {
			return true;
		}
		return false;

	}
	
	public function vm_customfields() {
		
		$table = 'virtuemart_customs';
        $pk = 'virtuemart_custom_id';
        $name_col = 'custom_title';
		$items = $this->getItems2BTransfered($table,$pk,$excludeids,"field_type NOT IN ('R','Z')");	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);

				$record->custom_desc	 	= $record->custom_field_desc;
				$record->custom_field_desc 	= null;
				$isCart = $record->is_cart_attribute;
				$record->is_input 			= $isCart;
				$record->layout_pos			= ($isCart) ? 'addtocart' : '';
				$record->field_type		 	= ($record->field_type=='V') ? 'S' : $record->field_type;

			//Get the new Joomla Plugin Id if necessary
			$JPluginId = 0;
			if ($record->custom_element) {
				$JPluginId = $this->getJPluginId('vmcustom',$record->custom_element);
				if (!$JPluginId) {
					$this->logWarning(JText::sprintf('VM_WARNING_MISSING_PLUGIN','vmcustom',$record->custom_element));			
					$this->logWarning(JText::sprintf('VM_WARNING_CUSTOMFIELD',$record->custom_element));			
				}
			}
			$record->custom_jplugin_id = $JPluginId;

			//Extended table for known plugins
			$createScript = '';
			$records_extended = array();
			$extended_table_name = 'virtuemart_product_custom_plg_'.$record->custom_element;
			if ($record->custom_element == 'customfieldsforall') {
				$extended_table_name = 'virtuemart_product_custom_plg_customsforall';
			}
			if ($JPluginId && $this->table_exists($this->source_db,'#__'.$extended_table_name)) {
				//Check if the destination table was already created
				if (!$this->table_exists($this->destination_db,'#__'.$extended_table_name)) {
					//Not yet created, we need to create it
					$createScript = VMMigrateHelperDatabase::getCreateTableScript($this->source_db,'#__'.$extended_table_name);
				}
				$records_extended = $this->get_records($extended_table_name);
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);

				//Create Extended Table
				if ($createScript) {
					$this->destination_db->setQuery($createScript)->query();
				}
				if (is_array($records_extended)) {
					//Dump Extended table records
					if ($this->table_exists($this->destination_db,'#__'.$extended_table_name)) {
						$this->deleteRows('#__'.$extended_table_name);
					}
					//Import Extended records
					foreach ($records_extended as $record_extended) {
						$this->destination_db->insertObject('#__'.$extended_table_name, $record_extended);
					}
				}

				$this->logRow($srcid,$record->{$name_col});
				$this->destination_db->transactionCommit();
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

		}

		if ($this->moreResults) {
			return true;
		}
		//return $this->copy_one2one($table,$pk,null,null,"field_type NOT IN ('R','Z')",$name_col);

	}

	public function vm_orders() {
		
		$table = 'virtuemart_orders';
        $pk = 'virtuemart_order_id';
        $name_col = 'order_number';
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		//Ensure the table has all the necessary fields
		VMMigrateHelperDatabase::copyTableStructure($this->source_db,'#__virtuemart_orders');
		VMMigrateHelperDatabase::copyTableStructure($this->source_db,'#__virtuemart_order_userinfos');

		$do_oc_note = VMMigrateHelperDatabase::columnExists($this->destination_db,'#__virtuemart_orders','oc_note');
		
        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			//$record->order_deposit = null;
			//$record->order_balance = null;
			//$record->reminded_on = null;
			$record->customer_note 	= null; //Dropped as of VM 2.9.9.4 and move to order_user_info
			//$record->oc_note = null

			$histories = $this->get_records('virtuemart_order_histories',$pk.'='.$srcid);
			$items = $this->get_records('virtuemart_order_items',$pk.'='.$srcid);
			$userinfos = $this->get_records('virtuemart_order_userinfos',$pk.'='.$srcid);
			$invoice = $this->get_record('virtuemart_invoices',$pk.'='.$srcid);

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				//Relations
				foreach ($histories as $child_record) {
					$child_record->amount_paid = null;
					$this->insertOrReplace('#__virtuemart_order_histories', $child_record, 'virtuemart_order_history_id');
				}
				foreach ($items as $child_record) {
					$child_record->product_deposit = null;
					$this->insertOrReplace('#__virtuemart_order_items', $child_record, 'virtuemart_order_item_id');
				}
				foreach ($userinfos as $child_record) {
					VMMigrateHelperDatabase::AddColumnIfNotExists('#__virtuemart_order_userinfos','customer_note','varchar(2500)  NOT NULL DEFAULT \'\'');
					$child_record->customer_note = $customer_note;
					$this->insertOrReplace('#__virtuemart_order_userinfos', $child_record, 'virtuemart_order_userinfo_id');
				}
				if ($invoice->invoice_number) {
					$this->insertOrReplace('#__virtuemart_invoices', $invoice, 'virtuemart_invoice_id');
					$this->logDebug($invoice,'Invoice data');
					$this->CopyInvoiceFile($invoice->invoice_number);
				}

				$this->logRow($srcid,$record->{$name_col});
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

	public function vm_coupons() {
		
		$table = 'virtuemart_coupons';
        $pk = 'virtuemart_coupon_id';
        $name_col = 'coupon_code';
		//return $this->copy_one2one($table,$pk,null,null,null,$name_col);
		$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered($table,$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

        foreach ($items as $i => $item) {
			
			$srcid = $item[$pk];	//Set the primary key
			$record = JArrayHelper::toObject($item);
			if (!isset($record->virtuemart_vendor_id)) {
				$record->virtuemart_vendor_id = $this->getVendorId();
			}

			try {
				$this->destination_db->transactionStart();

				//Main record
				$this->insertOrReplace('#__'.$table, $record, $pk);
				$this->logRow($srcid,$record->{$name_col});
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
	
	public function vm_plugin_data() {

		$query = "SHOW TABLES LIKE '".$this->source_db->getPrefix()."virtuemart_%_plg_%'";
		$tables = $this->source_db->setQuery($query)->loadColumn();
		foreach ($tables as $plugin_table) {
			$table_name = str_replace($this->source_db->getPrefix(),'',$plugin_table);
			$this->logInfo(JText::sprintf('VM_PLUGIN_TABLE_X',$table_name));
			$this->copy_one2one($table_name);
		}
		
	}
	
	/*********************/
	/* PRIVATE FUNCTIONS */
	/*********************/
	private function CopyMediaFiles($record_media) {

		if ($record_media->file_url && !stripos($record_media->file_url_thumb,'http')) {
			$this->source_filehelper->CopyFile('/'.$record_media->file_url,JPATH_SITE.'/'.$record_media->file_url);
		}
		if ($record_media->file_url_thumb && !stripos($record_media->file_url_thumb,'http')) {
			$this->source_filehelper->CopyFile('/'.$record_media->file_url_thumb,JPATH_SITE.'/'.$record_media->file_url_thumb);
		}
	}

	private function CopyInvoiceFile($invoiceNumber) {
	
		$srcSecurePath = $this->getSrcConfigValue('forSale_path','');
		$srcInvoicePath = $srcSecurePath.'invoices';
		$srcInvoiceFilePath = $srcInvoicePath.'/vminvoice_'.$invoiceNumber.'.pdf';

		$dstSecurePath = $this->getDstConfigValue('forSale_path',$srcSecurePath);
		$dstInvoicePath = $dstSecurePath.'invoices';
		$dstInvoiceFilePath = $dstInvoicePath.'/vminvoice_'.$invoiceNumber.'.pdf';

		if (!JFolder::exists($dstInvoicePath)) {
			JFolder::create($dstInvoicePath);
		}
		if ($this->source_filehelper->FilePathExists($srcInvoiceFilePath)) {
			$this->source_filehelper->CopyPathFile($srcInvoiceFilePath,$dstInvoiceFilePath);
		}
	}

	private function ensureRelatedCustomsFields() {

		$custom = new stdClass();
		$custom->virtuemart_custom_id	= 1;
		$custom->custom_parent_id 		= 0;
		$custom->virtuemart_vendor_id 	= 1;
		$custom->custom_jplugin_id 		= 0;
		$custom->custom_element 		= '';
		$custom->admin_only		 		= 0;
		$custom->custom_title	 		= 'COM_VIRTUEMART_RELATED_PRODUCTS';
		$custom->custom_tip		 		= 'COM_VIRTUEMART_RELATED_PRODUCTS_TIP';
		$custom->custom_value	 		= 'related_products';
		$custom->custom_desc	 		= 'COM_VIRTUEMART_RELATED_PRODUCTS_DESC';
		$custom->is_input	 			= 0;
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
		$custom->custom_desc	 	= 'COM_VIRTUEMART_RELATED_CATEGORIES_DESC';
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
		
	private function getVendorId($vendorid=1) {
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($vendorid);
		return ($vendor) ? $vendor->virtuemart_vendor_id : $vendorid;
	}

}
