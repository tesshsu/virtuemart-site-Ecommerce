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

class VMMigrateModelJoomla extends VMMigrateModelBase {

	public $isPro = true;
	
    function __construct($config = array()) {
        parent::__construct($config);
    }

	public static function getSteps() {
		$steps = array();
		$steps[] = array('name'=>'reset_log'						,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_LOG'));
		$steps[] = array('name'=>'reset_log_error'					,'default'=>1);
		$steps[] = array('name'=>'reset_data'						,'default'=>0, 'warning'=>JText::_('VMMIGRATE_WARNING_RESET_DATA'));
		$steps[] = array('name'=>'joom_settings'					,'default'=>0);
		$steps[] = array('name'=>'joom_languages_settings'			,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'joom_menu_types'					,'default'=>1);
		$steps[] = array('name'=>'joom_menus'						,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'joom_user_groups'					,'default'=>1);
		$steps[] = array('name'=>'joom_users'						,'default'=>1);
		
		$steps[] = array('name'=>'joom_sections'					,'default'=>1, 'joomfish'=>1);
		$steps[] = array('name'=>'joom_articles_settings'			,'default'=>1);
		$steps[] = array('name'=>'joom_articles'					,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_contact_settings'			,'default'=>1);
		$steps[] = array('name'=>'joom_contact_categories'			,'default'=>1);
		$steps[] = array('name'=>'joom_contact'						,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_weblinks_settings'			,'default'=>1);
		$steps[] = array('name'=>'joom_weblinks_categories'			,'default'=>1);
		$steps[] = array('name'=>'joom_weblinks'					,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_newsfeeds_settings'			,'default'=>1);
		$steps[] = array('name'=>'joom_newsfeeds_categories'		,'default'=>1);
		$steps[] = array('name'=>'joom_newsfeeds'					,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_banners_settings'			,'default'=>1);
		$steps[] = array('name'=>'joom_banners_categories'			,'default'=>1);
		$steps[] = array('name'=>'joom_banners_clients'				,'default'=>1);
		$steps[] = array('name'=>'joom_banners'						,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_search_settings'				,'default'=>1);
		$steps[] = array('name'=>'joom_search'						,'default'=>0);
		
		$steps[] = array('name'=>'joom_messages'					,'default'=>1);
		$steps[] = array('name'=>'joom_modules'						,'default'=>1, 'joomfish'=>1);
		
		$steps[] = array('name'=>'joom_media_settings'				,'default'=>1);
		$steps[] = array('name'=>'joom_images'						,'default'=>1);
		$steps[] = array('name'=>'joom_images_get'					,'default'=>1);
		
		return $steps;
	}

	public function reset_data() {
		
		$resetall = (count($this->steps)==1 && $this->steps[0]=='reset_data');
		$sql = '';
		if (in_array('joom_menu_types',$this->steps) || $resetall) {
			if (VMMigrateHelperDatabase::tableExists($this->source_db,'#__menu_types')) {
				$sql .= "TRUNCATE TABLE #__menu_types;";
			}
		}
		if (in_array('joom_menus',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__menu WHERE client_id = 0 and id<>1;";
		}
		if (in_array('joom_users',$this->steps) || $resetall) {
			$currentUser = JFactory::getUser();
			$sql .= "DELETE FROM #__users WHERE ".$this->destination_db->qn('id')." <> ".$this->destination_db->q($currentUser->id).";";
			$sql .= "TRUNCATE TABLE #__messages_cfg;";
		}
		if (in_array('joom_sections',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__categories WHERE ".$this->destination_db->qn('extension')." = 'com_content';";
			$sql .= "DELETE FROM #__assets WHERE ".$this->destination_db->qn('name')." like 'com_content.category%';";
		}
		if (in_array('joom_articles',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__content;";
			$sql .= "TRUNCATE TABLE #__content_frontpage;";
			$sql .= "DELETE FROM #__assets WHERE ".$this->destination_db->qn('name')." like 'com_content.article%';";
		}
		if (in_array('joom_contact_categories',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__categories WHERE ".$this->destination_db->qn('extension')." = 'com_contact';";
			$sql .= "DELETE FROM #__assets WHERE ".$this->destination_db->qn('name')." like 'com_contact.category%';";
		}
		if (in_array('joom_contact',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__contact_details;";
		}
		if (VMMigrateHelperDatabase::tableExists($this->destination_db,'#__weblinks')) {
			if (in_array('joom_weblinks_categories',$this->steps) || $resetall) {
				$sql .= "DELETE FROM #__categories WHERE ".$this->destination_db->qn('extension')." = 'com_weblinks';";
				$sql .= "DELETE FROM #__assets WHERE ".$this->destination_db->qn('name')." like 'com_weblinks.category%';";
			}
			if (in_array('joom_weblinks',$this->steps) || $resetall) {
				$sql .= "TRUNCATE TABLE #__weblinks;";
			}
		}
		if (in_array('joom_newsfeeds_categories',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__categories WHERE ".$this->destination_db->qn('extension')." = 'com_newsfeeds';";
		}
		if (in_array('joom_newsfeeds',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__newsfeeds;";
		}
		if (in_array('joom_banners_categories',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__categories WHERE ".$this->destination_db->qn('extension')." = 'com_banners';";
			$sql .= "DELETE FROM #__assets WHERE ".$this->destination_db->qn('name')." like 'com_banners.category%';";
		}
		if (in_array('joom_banners_clients',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__banner_clients;";
		}
		if (in_array('joom_banners',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__banners;";
			$sql .= "TRUNCATE TABLE #__banner_tracks;";
		}
		if (in_array('joom_banners_tracks',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__banner_tracks;";
		}
		if (in_array('joom_search',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__core_log_searches;";
		}
		if (in_array('joom_messages',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__messages;";
		}
		if (in_array('joom_modules',$this->steps) || $resetall) {
			$sql .= "DELETE FROM #__modules WHERE client_id = 0;";
			$sql .= "DELETE FROM #__modules_menu WHERE moduleid NOT IN (SELECT id from #__modules);";
		}
		if (in_array('joom_images',$this->steps) || $resetall) {
			$sql .= "TRUNCATE TABLE #__vmmigrate_temp";
		}
		if (!$sql) {
			return;
		}

		if (VMMigrateHelperDatabase::queryBatch($this->destination_db,$sql)) {
			foreach ($this->steps as $step) {
				$this->logInfo(JText::sprintf('DATA_RESETED',JText::_($step)));
			}
			$this->resetAutoIncrements();
		} else {
			$this->logError(JText::_('DATA_RESET_ERROR').'<br/>'.$this->destination_db->getErrorMsg());
		}
	}
	
	private function getJoomConfigSrc() {
		$oldConfigFile = '/configuration.php';
		if ($this->source_filehelper->FileExists($oldConfigFile)) {
			$buffer = $this->source_filehelper->ReadFile($oldConfigFile);

			try {
				$buffer = str_replace('JConfig','JConfigSrc',$buffer);
				$buffer = str_replace('<?php','',$buffer);
				$buffer = str_replace('?>','',$buffer);
				eval($buffer);
				if (class_exists('JConfigSrc')) {
					$configSrc = new JConfigSrc();
					return $configSrc;
				} else {
					$this->logDebug('Could not evaluate source configuration');
					return null;
				}
			} catch (Exception $e) {
				$this->logError($e->getMessage());
				return null;
			}
		} else {
			$this->logWarning(JText::_('JOOM_CONFIGURATION_READ_ERROR'));
			return null;
		}
	}
	
	public function getSrcVersion() {
		return $this->joomla_version_src;
	}

	public function getDstVersion() {
		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();
		return $joomla_version_dest;
	}

	public function joom_settings() {
		//Migrate Joomla settings
		//Read the old config file
		
		$configSrc = $this->getJoomConfigSrc();
		if ($configSrc) {
			
			JLoader::discover('ConfigModel', JPATH_SITE . '/components/com_config/model');
			JLoader::discover('ConfigModel', JPATH_ADMINISTRATOR . '/components/com_config/models');
			$configModel = new ConfigModelApplication();
			$currentData = $configModel->getData();
			$currentData['offline'] 		= $configSrc->offline;
			$currentData['offline_message'] = $configSrc->offline_message;
			$currentData['sitename'] 		= $configSrc->sitename;
			//$currentData['editor'] 			= $config15editor;
			$currentData['debug'] 			= $configSrc->debug;
			$currentData['debug_lang'] 		= $configSrc->debug_lang;
			$currentData['sef'] 			= $configSrc->sef;
			$currentData['sef_rewrite'] 	= $configSrc->sef_rewrite;
			$currentData['sef_suffix'] 		= $configSrc->sef_suffix;
			$currentData['feed_limit'] 		= $configSrc->feed_limit;
			$currentData['feed_email'] 		= $configSrc->feed_email;
			$currentData['gzip'] 			= $configSrc->gzip;
			switch ($configSrc->error_reporting) {
				case '-1':		$currentData['error_reporting'] = 'default';break;
				case '0':		$currentData['error_reporting'] = 'none';break;
				case '7':		$currentData['error_reporting'] = 'simple';break;
				case '6143':	$currentData['error_reporting'] = 'maximum';break;
				case '30719':	$currentData['error_reporting'] = 'development';break;
			}
			$currentData['xmlrpc_server'] 	= $configSrc->xmlrpc_server;
			$currentData['force_ssl'] 		= $configSrc->force_ssl;
			$currentData['offset'] 			= $this->getTimeZoneName($configSrc->offset);
			$currentData['caching'] 		= $configSrc->caching;
			$currentData['cachetime'] 		= $configSrc->cachetime;
			$currentData['cache_handler'] 	= $configSrc->cache_handler;
			$currentData['sitename'] 		= $configSrc->sitename;
			$currentData['MetaDesc'] 		= $configSrc->MetaDesc;
			$currentData['MetaKeys'] 		= $configSrc->MetaKeys;
			$currentData['MetaTitle'] 		= $configSrc->MetaTitle;
			$currentData['MetaAuthor'] 		= $configSrc->MetaAuthor;

			$currentData['mailfrom'] 		= $configSrc->mailfrom;
			$currentData['fromname'] 		= $configSrc->fromname;
			
			if ($configModel->save($currentData)) {
				$this->logInfo(JText::_('JOOM_CONFIGURATION_COPIED'));
			} else {
				$this->logError(JText::_('JOOM_CONFIGURATION_COPIED_ERROR'));
			}
			
			if ($configSrc->sef_rewrite && !JFile::exists(JPATH_SITE.'/.htaccess')) {
				if (JFile::copy(JPATH_SITE.'/htaccess.txt',JPATH_SITE.'/.htaccess')) {
					$this->logInfo(JText::_('JOOM_CONFIGURATION_HTACCESS'));
				} else {
					$this->logWarning(JText::_('JOOM_CONFIGURATION_HTACCESS_ERROR'));
				}
			}
			
		} 
		
	}
	
	public function joom_languages_settings() {
		
		if (version_compare($this->joomla_version_src, '1.5', '<')) {
			$this->logWarning('Not implmented');
			return false;
		}

		if (version_compare($this->joomla_version_src, '2.5', 'ge')) {
			$src_where = "`element`='com_languages'";
		} else if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_languages' AND parent=0";
		} else {
			$src_where = "`option`='com_languages' AND parent=0";
		}
		$dst_where = "`element`='com_languages'";
		$properties=array();

		$this->migrateComponentSettings($src_where,$dst_where,$properties);

		if (version_compare($this->joomla_version_src, '1.6', '<') && !$this->joomfishInstalled) {
			return;
		}
		
		$pk = 'lang_id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('languages',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->lang_id		= $item['lang_id'];
			$record->lang_code		= $item['lang_code'];
			$record->title			= $item['title'];
			$record->title_native	= $item['title_native'];
			$record->sef			= $item['sef'];
			$record->image			= ($item['image']) ? $item['image'] : $item['sef'];
			//$record->description	= '';
			$record->metakey		= $item['metakey'];
			$record->metadesc		= $item['metadesc'];
			$record->published		= $item['published'];
			$record->access			= 1;
			
			if (!$this->isInstalledLanguage($this->destination_db,$item['lang_code'])) {
				$this->logWarning(JText::sprintf('JOOM_LANGUAGE_NOT_INSTALLED',$item['title'],$item['lang_code']));
			}

			try {
				$this->destination_db->transactionStart();
				//Delete the row with the same language code to prevent unique contraint error.
				$this->deleteRows('#__languages','lang_code = '.$this->destination_db->q($record->lang_code));
				$this->insertOrReplace('#__languages', $record, $pk);
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['title'].'('.$item['lang_code'].')');
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function joom_menu_types() {

		if (version_compare($this->joomla_version_src, '1.5', '<')) {

			$query = $this->source_db->getQuery(true);
			$query->select('DISTINCT menutype')
					->from('#__menu');
			$menutypes = $this->source_db->setQuery($query)->loadColumn();

			foreach ($menutypes as $i => $menutype) {
				
				$record = new stdClass();
				$record->menutype = $menutype;
				$record->title = $menutype;
				$record->description = '';

				try {
					$this->destination_db->transactionStart();
					$this->insertOrReplace('#__menu_types', $record, 'menutype');
					$this->logRow($menutype,$record->menutype);
					$this->destination_db->transactionCommit();
				} catch (Exception $e) {
					$this->destination_db->transactionRollback();
					$this->logError($e->getMessage(),$srcid);
				}
			}
			return false;
		}

		try {
			$ret = $this->copy_one2one('menu_types');
			return $ret;
		} catch (Exception $e) {
			$this->logError($e);
		}
	}
	
	public function joom_menus() {

		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$table = 'menu';
			$pk = 'id';
	        $name_col = 'path';
			$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
			$items = $this->getItems2BTransfered($table,$pk,$excludeids,'id<>1 AND client_id=0');	//Get a list of source objects to be transfered
			if (!$items) {
				$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
				return false;
			}

			foreach ($items as $i => $item) {
				
				$srcid = $item[$pk];	//Set the primary key
				$record = JArrayHelper::toObject($item);
				$record->ordering = null;
				$record->component_id	= ($record->type=='component') ? $this->getExtensionId($record->component_id) : 0;

				//Look for an item with the same id.
				//If that item has a client id=1 (administration menu) then we need to give anoter id to the menu item.
				$query = $this->destination_db->getQuery(true);
				$query->select('id')->from('#__'.$table)->where('client_id = 1')->where('id = '.$this->destination_db->q($record->id));
				$lookup = $this->destination_db->setQuery($query)->loadResult();
				if ($lookup) {
					$record->id = null;
				}

				try {
					$this->destination_db->transactionStart();
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
	
		} else {
		
			if (version_compare($this->joomla_version_src, '1.5', 'ge')) {
				//Get the menu types to filter the menus
				$query = $this->source_db->getQuery(true);
				$query->select('menutype')->from('#__menu_types');
				$this->source_db->setQuery($query);
				$menutypes = $this->source_db->loadColumn();
				$menuTypesFilter = 'menutype IN (\''.implode("','",$menutypes).'\')';

			} else {
				$query = $this->source_db->getQuery(true);
				$query->select('DISTINCT menutype')
						->from('#__menu');
				$menutypes = $this->source_db->setQuery($query)->loadColumn();
				$menuTypesFilter = 'menutype IN (\''.implode("','",$menutypes).'\')';
			}
		
			//We need to delete and recreate to prevent conflicting with admin menu entries	
			$this->deleteRows('#__menu',"client_id=0 and id<>1;");
			
			foreach ($menutypes as $menutype) {
				$this->ImportMenuItems($menutype,0,'');
			}
			
			$this->FixMenuAlias();
			
			JLoader::register('JTableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');
			$table = JTable::getInstance('Menu', 'JTable');
			if ($table->rebuildPath('id')) {
				$this->logInfo(JText::_('JOOM_PATH_RECREATED'));
			}
			if ($table->rebuild(1)) {
				$this->logInfo(JText::_('JOOM_HIERARCHY_RECREATED'));
			}
		}
		
	}
	
	public function joom_user_groups() {
		
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			return $this->copy_one2one('usergroups','id');
		} 

		return false;
		
	}
	
	public function joom_users() {
		
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		//$items = $this->getItems2BTransfered('users',$pk,$excludeids,"usertype <> 'Super Administrator'");	//Get a list of source objects to be transfered
		$items = $this->getItems2BTransfered('users',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }

		//if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
		//	$this->copy_one2one('usergroups');
		//}

		$configSrc = $this->getJoomConfigSrc();
		
		$currentUser = JFactory::getUser();
		
        foreach ($items as $i => $item) {
			
			if ($currentUser->id == $item['id']) {
				$this->logWarning(JText::sprintf(JOOM_USER_SKIP_CURRENT,$item['id']),$srcid);
				continue;
			}
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass(); 
			$record->id				= $item['id'];
			$record->name			= $item['name'];
			$record->username		= $item['username'];
			$record->email			= $item['email'];
			$record->password		= $item['password'];
			//$record->usertype		= ''; //$item['usertype'];
			$record->block			= $item['block'];
			$record->sendEmail		= $item['sendEmail'];
			$record->registerDate	= $item['registerDate'];
			$record->lastvisitDate	= $item['lastvisitDate'];
			$record->activation		= $item['activation'];

			if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
				$record->params = $item['params'];
			} else {
				//Transform parameters to JSON
				$newparams = new JRegistry();
				$newparams->loadString($item['params'], 'INI');
				if ($configSrc && isset($configSrc->offset) && $configSrc->offset == $newparams->get('timezone')) {
					//If the user timezone is the same as the shop one, let's put it as default (empty)
					$newparams->set('timezone','');
				} else {
					$newparams->set('timezone',$this->getTimeZoneName($newparams->get('timezone')));
				}
				$record->params = $newparams->toString('JSON');
			}
			
			if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
				$query = $this->source_db->getQuery(true);
				$query->select('*')
					->from('#__user_usergroup_map')
					->where('user_id = '.$this->source_db->q($srcid)); 
				$this->source_db->setQuery($query);
				$record_group_map = $this->source_db->loadObjectList();
			} else {
				$group_mapping = array();
				$group_mapping[0]	= 0;	// ROOT
				$group_mapping[28]	= 1;	// USERS (=Public)
				$group_mapping[29]	= 1;	// Public Frontend
				$group_mapping[18]	= 2;	// Registered
				$group_mapping[19]	= 3;	// Author
				$group_mapping[20]	= 4;	// Editor
				$group_mapping[21]	= 5;	// Publisher
				$group_mapping[30]	= 6;	// Public Backend (=Manager)
				$group_mapping[23]	= 6;	// Manager
				$group_mapping[24]	= 7;	// Administrator
				$group_mapping[25]	= 8;	// Super Administrator
	
				$record_group_map = new stdClass();
				$record_group_map->user_id = $srcid;
				$record_group_map->group_id = $group_mapping[$item['gid']];
			}
			
//            if ($item['usertype'] == "Super Administrator") {
//                $record->usertype = "Super Users";
//			}
//
//	
//			$query = $this->source_db->getQuery(true);
//			$query->select('grp.id, grp.value')
//				->from('#__core_acl_aro aro')
//				->join('INNER','#__core_acl_aro_map map ON aro.id = map.aro_id')
//				->join('INNER','#__core_acl_aro_groups grp ON map.group_id = grp.id')
//				->where('aro.value = '.$this->source_db->q($srcid)); 
//			$this->source_db->setQuery($query);
//			$userGroups = $this->source_db->loadObjectList();
//			
//			
//			$query = $this->destination_db->getQuery(true);
//			$query->select('id')
//				->from('#__usergroups')
//				->where('title LIKE '.$this->destination_db->q($record->usertype)); 
//			$this->destination_db->setQuery($query);
//            $this->destination_db->query();
//            $group_id = $this->destination_db->loadResult();
//			//$usergroups = $this->destination_db->loadObjectList();
//
//			$record_group_map = new stdClass();
//			$record_group_map->user_id = $srcid;
//			$record_group_map->group_id = $group_id;
//			$record_group_mapping = array($record_group_map);

			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__messages_cfg')
				->where('user_id = '.$this->source_db->q($srcid)); 
			$this->source_db->setQuery($query);
			$userMsgCfg = $this->source_db->loadObjectList();

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__users', $record, $pk);

				$this->deleteRows('#__user_usergroup_map',"user_id='".$item['id']."'");
				$this->insertOrReplace('#__user_usergroup_map', $record_group_map, 'user_id');
				$this->insertOrReplace('#__messages_cfg', $userMsgCfg, 'user_id');
				//$this->copy_one2one('messages_cfg','cfg_name','messages_cfg','cfg_name',"user_id='".$srcid."'");
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$record->username);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
			
		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function joom_sections() {
		
		$this->limit = 25;

		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			return $this->joom_categories('com_content','com_content');
			//return $this->copy_one2one('categories',$pk,'','',"extension=".$this->destination_db->q($new_section));
		}

		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('sections',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass(); 
			$record->id				= 10000+(int)$item['id'];
			//$record->asset_id		= null;
			$record->parent_id		= 1;
			//$record->lft			= null;
			//$record->rgt			= null;
			$record->level			= 1;
			$record->path			= $item['alias'];
			$record->extension		= 'com_content';
			$record->title			= $item['title'];
			$record->alias			= ($item['alias']) ? $item['alias'] : JApplication::stringURLSafe($item['title']); //$this->checkSlug('#__virtuemart_vendors_'.VMLANG,$slug,'slug',$srcid,'virtuemart_vendor_id');
			$record->description	= $item['description'];
			$record->published		= $item['published'];
			$record->access			= $this->handleAccessLevel($item['access']);
			//$record->params			= $item[''];
			$record->hits			= $item['count'];
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';

			//Get the categories
			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__categories')
				->where('section='.$this->source_db->q($item['id']))
				->order('ordering');
			$this->source_db->setQuery($query);
			$categories = $this->source_db->loadObjectList();
			
			//$this->logWarning($query.'');

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'sections',$srcid);
			$translateCategories = array();

			$records_categories = array();
			$i=0;
			foreach ($categories as $category) {
				
				$category->alias = ($category->alias) ? $category->alias : JApplication::stringURLSafe($category->title);
				
				$records_category = new stdClass();
				$records_category->id				= ($category->id == 1) ? 10000 : $category->id;
				$records_category->asset_id		= null;
				$records_category->parent_id		= $record->id;
				$records_category->lft				= $i;
				$records_category->rgt				= $i+1;
				$records_category->level			= 2;
				$records_category->path				= $item['alias'].'/'.$category->alias;
				$records_category->extension		= 'com_content';
				$records_category->title			= $category->title;
				$records_category->alias			= $category->alias; //$this->checkSlug('#__virtuemart_vendors_'.VMLANG,$slug,'slug',$srcid,'virtuemart_vendor_id');
				$records_category->description		= $category->description;
				$records_category->published		= $category->published;
				$records_category->access			= $this->handleAccessLevel($category->access);
				$records_category->params 			= $this->IniToJson($category->params);
				$records_category->hits				= $category->count;
				$records_category->language			= (isset($item['language'])) ? $item['language'] : '*';;
				$records_categories[] = $records_category;

				/* Handle JoomFish translations */		
				$translateCategories[] = $this->getTranslationsRecords($records_category,'categories',$records_category->id);
				$i++;
				$i++;
			}
			

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__categories', $record, 'id');

				$this->logRow($srcid,$item['title'],$record->id);
				//Recreate the asset id
				$table = JTable::getInstance('Category', 'JTable');
				$table->load($record->id);
				$table->store();

				//$this->deleteRows('#__categories',"poll_id='".$item['id']);
				if ($count = count($records_categories)) {
					$this->logInfo(JText::sprintf('JOOM_FOUND_X_CAT_FOR_SECTION_Y',$count,$record->title));
					foreach ($records_categories as $category) {
						$this->insertOrReplace('#__categories', $category, 'id');
						
						//Recreate the asset id
						$table = JTable::getInstance('Category', 'JTable');
						$table->load($category->id);
						$table->store();
		
						$this->logInfo('&nbsp;&nbsp;&nbsp;-&nbsp;'.$category->title);
						//$this->logRow($category->id,$category->title);
					}
				}
				if (count($translateCategories)) {
					foreach ($translateCategories as $translateCategory) {
						$this->insertOrReplace('#__categories', $translateCategory,'id');
					}
				}
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__categories', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->title,$recordLang->id);
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
			$table = JTable::getInstance('Category', 'JTable');
			if ($table->rebuild()) {
				$this->logInfo(JText::_('JOOM_HIERARCHY_RECREATED'));
			}
		}
	}
	
	public function joom_articles_settings() {
		
		$properties=array();
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_content'";
		} else {
			$src_where = "`option`='com_content'";
			$properties['show_title'] = 'show_title';
			$properties['link_titles'] = 'link_titles';
			$properties['show_intro'] = 'show_intro';
			$properties['show_category'] = 'show_category';
			$properties['link_category'] = 'link_category';
			$properties['show_author'] = 'show_author';
			$properties['show_create_date'] = 'show_create_date';
			$properties['show_modify_date'] = 'show_modify_date';
			$properties['show_item_navigation'] = 'show_item_navigation';
			$properties['show_readmore'] = 'show_readmore';
			$properties['show_vote'] = 'show_vote';
			$properties['show_icons'] = 'show_icons';
			$properties['show_print_icon'] = 'show_print_icon';
			$properties['show_email_icon'] = 'show_email_icon';
			$properties['show_hits'] = 'show_hits';
			$properties['feed_summary'] = 'feed_summary';
			$properties['show_noauth'] = 'show_noauth';
		}
		$dst_where = "`element`='com_content'";

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}
	
	public function joom_articles() {
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('content',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			$record = new stdClass();
			$record->id					= $item['id'];
			$record->title				= $item['title'];
			$record->alias				= $item['alias'];
			if (version_compare($this->joomla_version_dest, 3, '<')) {
				$record->title_alias		= $item['title_alias'];
				$record->mask				= $item['mask'];
				$record->parentid			= $item['parentid'];
			}
			$record->introtext			= $item['introtext'];
			$record->fulltext			= $item['fulltext'];
			$record->state				= ($item['state']==-1) ? 2 : $item['state'];
			$record->catid				= ($item['catid'] == 1) ? 10000 : $item['catid'];
			$record->created			= $item['created'];
			$record->created_by			= $item['created_by'];
			$record->created_by_alias	= $item['created_by_alias'];
			$record->modified			= $item['modified'];
			$record->modified_by		= $item['modified_by'];
			$record->checked_out		= 0;
			$record->checked_out_time	= '0000-00-00 00:00:00';
			$record->publish_up			= $item['publish_up'];
			$record->publish_down		= $item['publish_down'];
			$record->images				= $item['images'];
			$record->urls				= $item['urls'];
			$record->attribs			= $this->IniToJson($record->attribs);
			$record->version			= $item['version'];
			$record->ordering			= $item['ordering'];
			$record->metakey			= $item['metakey'];
			$record->metadesc			= $item['metadesc'];
			$record->access				= $this->handleAccessLevel($item['access']);
			$record->metadata			= $this->IniToJson($record->metadata);
			$record->hits				= $item['hits'];
			$record->language			= (isset($item['language'])) ? $item['language'] : '*';
			
			//Check if the acticle is featured
			$query = $this->source_db->getQuery(true);
			$query->select('*')
				->from('#__content_frontpage')
				->where('content_id='.$this->source_db->q($item['id']));
			$this->source_db->setQuery($query);
			$featured = $this->source_db->loadObject();
			$record->featured			= ($featured) ? 1 : 0;

			//Import the articles ratings
			$query->clear()->select('*')
				->from('#__content_rating')
				->where('content_id='.$this->source_db->q($item['id']));
			$records_rating = $this->source_db->setQuery($query)->loadObjectList();

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'content',$srcid);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__content', $record, 'id');
				
				//Recreate the asset id
				$table = JTable::getInstance('Content', 'JTable');
				$table->load($record->id);
				$table->store();

				$this->deleteRows('#__content_frontpage',"content_id='".$item['id']."'");
				if ($record->featured) {
					$this->insertOrReplace('#__content_frontpage', $featured, 'content_id');
				}
				$this->insertOrReplace('#__content_rating', $records_rating, 'content_id');
				//$this->copy_one2one('content_rating','content_id','content_rating','content_id',"content_id='".$item['id']."'");
				$this->logRow($srcid,$item['title']);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__content', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->title,$recordLang->id);
							if ($record->featured) {
								$this->deleteRows('#__content_frontpage',"content_id='".$recordLang->id."'");
								$featured->content_id = $recordLang->id;
								$this->insertOrReplace('#__content_frontpage', $featured, 'content_id');
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
		} else {
			//If some articles are not assigned to any category, create an 'Uncategorized' category id necessary and assign the articles to it.
			$query = $this->destination_db->getQuery(true);
			$query->select('count(*)')->from('#__content')->where('catid=0');
			$orphanArticles  = $this->destination_db->setQuery($query)->loadResult();
			if ($orphanArticles) {
				$catid = $this->getDefaultCategory();
				$query->clear()->update('#__content')->set('catid='.$this->destination_db->q($catid))->where('catid=0');
				$this->destination_db->setQuery($query)->query();
			}
			
		}
	}
	
	private function getDefaultCategory() {

		$query = $this->destination_db->getQuery(true);
		$query->select('id')->from('#__categories')->where("extension='com_content'")->where("title='Uncategorised'");
		$catid  = $this->destination_db->setQuery($query)->loadResult();
		if ($catid) {
			return $catid;
		}

		$category = new stdClass();
		$category->parent_id		= 1;
		$category->level			= 2;
		$category->path				= 'uncategorised';
		$category->extension		= 'com_content';
		$category->title			= 'Uncategorised';
		$category->alias			= 'uncategorised';
		$category->description		= '';
		$category->published		= 1;
		$category->access			= 1;
		$category->params 			= '';
		$category->hits				= 0;
		$category->language			= '*';
		
		$this->insertOrReplace('#__categories', $category,'id');

		$table = JTable::getInstance('Category', 'JTable');
		$table->rebuild();
		return $category->id;
	}
	
	public function joom_categories($old_section,$new_section) {
		
		$pk = 'id';
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			return $this->copy_one2one('categories',$pk,'','',"extension=".$this->destination_db->q($new_section));
		}
		
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('categories',$pk,$excludeids,"section='".$old_section."'");	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->id				= ($item['id'] == 1) ? 10000 : $item['id'];
			$record->parent_id		= $item['parent_id'];
			$record->title			= ($item['title']) ? $item['title'] : $item['title'];
			$record->alias			= $item['alias'];
			$record->description	= $item['description'];
			$record->published		= $item['published'];
			$record->checked_out	= 0;
			$record->access			= $this->handleAccessLevel($item['access']);
			$record->hits			= $item['count'];
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';
			$record->extension		= $new_section;
			$record->level			= 1;
			
			$newparams = new JRegistry();
			$newparams->loadString($item['params'], 'INI');
			if ($item['image']) {
				$newparams->set('image',$item['image']);
			}
			$record->params			= $newparams->toString('JSON');

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__categories', $record, 'id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['title']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

		}
		
		if ($this->moreResults) {
			return true;
		} else {
			$table = JTable::getInstance('Category', 'JTable');
			if ($table->rebuild()) {
				$this->logInfo(JText::_('JOOM_HIERARCHY_RECREATED'));
			}
		}
	}
	
	/************/
	/* Contacts */
	/************/
	public function joom_contact_settings() {
		
		if (version_compare($this->joomla_version_src, '2.5', 'ge')) {
			$src_where = "`element`='com_contact'";
		} else if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_contact' AND parent=0";
		} else {
			$src_where = "`option`='com_contact' AND parent=0";
		}
		$dst_where = "`element`='com_contact'";
		$properties=array();

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}

	public function joom_contact_categories() {
		return $this->joom_categories('com_contact_details','com_contact');
	}
	
	public function joom_contact() {

		//if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
		//	return $this->copy_one2one('contact_details');
		//}

		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('contact_details',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->id				= $item['id'];
			$record->name			= $item['name'];
			$record->alias			= $item['alias'];
			$record->con_position	= $item['con_position'];
			$record->address		= $item['address'];
			$record->suburb			= $item['suburb'];
			$record->state			= $item['state'];
			$record->country		= $item['country'];
			$record->postcode		= $item['postcode'];
			$record->telephone		= $item['telephone'];
			$record->fax			= $item['fax'];
			$record->misc			= $item['misc'];
			$record->image			= $item['image'];
			if (version_compare($this->joomla_version_dest, 3, '<')) {
				$record->imagepos		= $item['imagepos'];
			}
			$record->email_to		= $item['email_to'];
			$record->default_con	= $item['default_con'];
			$record->published		= $item['published'];
			$record->telephone		= $item['telephone'];
			$record->checked_out	= 0;
			$record->ordering		= $item['ordering'];

			if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
				$record->params		= $item['params'];
			} else {
				$newparams = new JRegistry();
				$newparams->loadString($item['params'], 'INI');
				$params_array = $newparams->toArray();
				unset($params_array['contact_icons']);
				unset($params_array['icon_address']);
				unset($params_array['icon_email']);
				unset($params_array['icon_telephone']);
				unset($params_array['icon_mobile']);
				unset($params_array['icon_fax']);
				unset($params_array['icon_misc']);
				$newparams = new JRegistry();
				$newparams->loadArray($params_array);
				$record->params			= $newparams->toString('JSON');
			}

			$record->user_id		= $item['user_id'];
			$record->catid			= ($item['catid'] == 1) ? 10000 : $item['catid'];
			$record->access			= $this->handleAccessLevel($item['access']);
			$record->mobile			= $item['mobile'];
			$record->webpage		= $item['webpage'];

			$record->language		= (isset($item['language'])) ? $item['language'] : '*';

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'contact_details',$srcid);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__contact_details', $record, 'id');
				$this->logRow($srcid,$item['name']);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__contact_details', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->name,$recordLang->id);
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
	
	/************/
	/* Weblinks */
	/************/
	public function joom_weblinks_settings() {
		
		if (!VMMigrateHelperDatabase::tableExists($this->destination_db,'#__weblinks')) {
			$this->logWarning('Not implmented');
			return false;
		}
		
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_weblinks' AND parent=0";
		} else {
			$src_where = "`option`='com_weblinks' AND parent=0";
		}
		$dst_where = "`element`='com_weblinks'";
		$properties=array();

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}

	public function joom_weblinks_categories() {

		if (!VMMigrateHelperDatabase::tableExists($this->destination_db,'#__weblinks')) {
			$this->logWarning('Not implmented');
			return false;
		}
		
		return $this->joom_categories('com_weblinks','com_weblinks');
	}
	
	public function joom_weblinks() {

		if (!VMMigrateHelperDatabase::tableExists($this->destination_db,'#__weblinks')) {
			$this->logWarning('Not implmented');
			return false;
		}
		
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('weblinks',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->id				= $item['id'];
			$record->catid			= ($item['catid'] == 1) ? 10000 : $item['catid'];
			if (version_compare($this->joomla_version_dest, 3, '<')) {
				$record->sid			= $item['sid'];
				$record->date			= $item['date'];
				$record->archived		= $item['archived'];
				$record->approved		= $item['approved'];
			} else {
				$record->created		= $item['date'];
			}
			$record->title			= $item['title'];
			$record->alias			= $item['alias'];
			$record->url			= $item['url'];
			$record->description	= $item['description'];
			$record->hits			= $item['hits'];
			$record->state			= $record->published;
			$record->checked_out	= 0;
			$record->ordering		= $item['ordering'];
			$record->access			= 1;
			$record->params			= $this->IniToJson($item['params']);
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'weblinks',$srcid);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__weblinks', $record, 'id');
				$this->logRow($srcid,$item['title']);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__weblinks', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->title,$recordLang->id);
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
	
	/**************/
	/* News feeds */
	/**************/
	public function joom_newsfeeds_settings() {
		
		$properties=array();
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_newsfeeds' AND params<>''";
		} else {
			$src_where = "`option`='com_newsfeeds' AND params<>''";
			$properties['show_feed_image'] 			= 'show_feed_image';
			$properties['show_feed_description']	= 'show_feed_description';
			$properties['show_item_description']	= 'show_item_description';
			$properties['feed_word_count']			= 'feed_word_count';
			$properties['show_headings']			= 'show_headings';
			$properties['show_name']				= 'show_name';
			$properties['show_articles']			= 'show_articles';
			$properties['show_link']				= 'show_link';
		}
		$dst_where = "`element`='com_newsfeeds'";

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}

	public function joom_newsfeeds_categories() {
		return $this->joom_categories('com_newsfeeds','com_newsfeeds');
	}
	
	public function joom_newsfeeds() {
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('newsfeeds',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->catid			= ($item['catid'] == 1) ? 10000 : $item['catid'];
			$record->id				= $item['id'];
			$record->name			= $item['name'];
			$record->alias			= $item['alias'];
			$record->link			= $item['link'];
			if (version_compare($this->joomla_version_dest, 3, '<')) {
				$record->filename		= $item['filename'];
			}
			$record->published		= $item['published'];
			$record->numarticles	= $item['numarticles'];
			$record->cache_time		= $item['cache_time'];
			$record->checked_out	= 0;
			$record->ordering		= $item['ordering'];
			$record->rtl			= $item['rtl'];
			$record->access			= 1;
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'newsfeeds',$srcid);

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__newsfeeds', $record, 'id');
				$this->logRow($srcid,$item['name']);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__newsfeeds', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->name,$recordLang->id);
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
	
	/***********/
	/* Banners */
	/***********/
	public function joom_banners_settings() {
		
		$properties=array();
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_banners' AND parent=0";
		} else {
			$src_where = "`option`='com_banners' AND parent=0";
			$properties['track_impressions'] 	= 'track_impressions';
			$properties['track_clicks']			= 'link_titles';
			$properties['tag_prefix'] 			= 'metakey_prefix';
		}
		$dst_where = "`element`='com_banners'";

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}
	
	public function joom_banners_categories() {
		return $this->joom_categories('com_banner','com_banners');
	}
	
	public function joom_banners_clients() {

		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			return $this->copy_one2one('banner_clients');
		}

		$pk = 'cid';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('bannerclient',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass(); 
			$record->id				= $item['cid'];
			$record->name			= $item['name'];
			$record->contact		= $item['contact'];
			$record->email			= $item['email'];
			$record->extrainfo		= $item['extrainfo'];
			$record->state			= 1;

			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__banner_clients', $record, 'id');
				$this->destination_db->transactionCommit();
				$this->logRow($srcid,$item['name']);
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}

		}
		
		if ($this->moreResults) {
			return true;
		}
	}
	
	public function joom_banners() {

		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			return $this->copy_one2one('banners');
		}

		$pk = 'bid';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('banner',$pk,$excludeids);	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		//Create the banner images folder if not present
		if (!JFolder::exists(JPATH_SITE.'/images/banners')) {
			JFolder::create(JPATH_SITE.'/images/banners');
		}

       foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key

			$record = new stdClass();
			$record->id				= $item['bid'];
			$record->cid			= $item['cid'];
			$record->type			= ($item['type']=='banner') ? 1 : 0;
			$record->name			= $item['name'];
			$record->alias			= $item['alias'];
			$record->imptotal		= $item['imptotal'];
			$record->impmade		= $item['impmade'];
			$record->clicks			= $item['clicks'];
			$record->clickurl		= $item['clickurl'];
			$record->state			= $record->showBanner;
			$record->catid			= ($item['catid'] == 1) ? 10000 : $item['catid'];
			$record->description	= $item['description'];
			$record->custombannercode	= $item['custombannercode'];
			$record->sticky			= $item['sticky'];
			$record->ordering		= $item['ordering'];
			$record->metakey		= $item['tags'];
			$newparams = new JRegistry();
			$newparams->loadString($item['params'], 'INI');
			if ($item['imageurl']) {
				$newparams->set('imageurl','images/banners/'.$item['imageurl']);
			}
			$record->params			= $newparams->toString('JSON');
	
			//$record->own_prefix		= '';
			//$record->metakey_prefix	= '';
			//$record->purchase_type	= -1;
			//$record->track_clicks	= -1;
			//$record->track_impressions	= -1;
			$record->checked_out	= 0;
			$record->publish_up		= $item['publish_up'];
			$record->publish_down	= $item['publish_down'];
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';
	
			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'banner',$srcid);

			if ($imagename) {
				//Copy the image from source to dest site if not already copied
				$dest = JPATH_SITE.'/images/banners/'.$imagename;
				$src = '/images/banners/'.$imagename;
				if (!JFile::exists($dest) && $this->source_filehelper->FileExists($src)) {
					if ($this->source_filehelper->CopyFile($src,$dest)) {
						$this->logInfo(JText::sprintf('JOOM_BANNER_IMAGE_COPIED',$imagename));
					} else {
						$this->logError(JText::sprintf('JOOM_BANNER_IMAGE_NOT_COPIED',$imagename));
					}
				}
			}
			
			//Banner tracking
			$query = $this->source_db->getQuery(true);
			$query->select('track_date,track_type,banner_id, count(*) as count')
				->from('#__bannertrack')
				->where('banner_id = '.$this->source_db->q($record->id))
				->group('track_date,track_type,banner_id');
			$this->source_db->setQuery($query);
			$bannerTracks = $this->source_db->loadObjectList();
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__banners', $record, 'id');
				$this->deleteRows('#__banner_tracks',"banner_id='".$record->id."'");
				foreach ($bannerTracks as $track) {
					$this->destination_db->insertObject('#__banner_tracks', $track);
				}
				$this->logRow($srcid,$item['name']);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__banners', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->name,$recordLang->id);
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
	
	/**********/
	/* Search */
	/**********/
	public function joom_search_settings() {
		
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_search'";
		} else {
			$src_where = "`option`='com_search'";
		}
		$dst_where = "`element`='com_search'";
		$properties=array();

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}
	
	public function joom_search() {
		return $this->copy_one2one('core_log_searches','search_term');
	}

	/************/
	/* Messages */
	/************/
	public function joom_messages() {
		return $this->copy_one2one('messages','message_id');
	}

	/***********/
	/* Modules */
	/***********/
	public function joom_modules() {
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$items = $this->getItems2BTransfered('modules',$pk,$excludeids,'client_id=0');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logInfo(JText::_('NOTHING_TO_TRANSFER'));
            return false;
        }
		
		$cleanupQuery = "DELETE FROM #__modules_menu WHERE moduleid NOT IN (SELECT id from #__modules)";

		$query = $this->source_db->getQuery(true);
    	foreach ($items as $i => $item) {
			$raiseWarning = false;
			$srcid = $item[$pk];	//Set the primary key
			
			switch ($item['module']) {
				case 'mod_archive': 	$item['module'] = 'mod_articles_archive'; break;
				case 'mod_latestnews': 	$item['module'] = 'mod_articles_latest'; break;
				case 'mod_mainmenu': 	$item['module'] = 'mod_menu'; break;
				case 'mod_mostread': 	$item['module'] = 'mod_articles_popular'; break;
				case 'mod_newsflash': 	$item['module'] = 'mod_articles_news'; break;
				case 'mod_sections': 	$item['module'] = 'mod_articles_news'; break;
			}
			
			if (!JFolder::exists(JPATH_SITE.'/modules/'.$item['module'])) {
				$item['published'] = false;
				$raiseWarning = true;
				//continue;
			}

			$record = new stdClass(); 
			$record->id					= null; //Reset the id to be imported with a new id
			$record->title				= $item['title'];
			$record->content			= $item['content'];
			$record->ordering			= $item['ordering'];
			$record->position			= $item['position'];
			$record->checked_out		= 0;
			$record->published			= $item['published'];
			$record->module				= $item['module'];
			$record->access				= $this->handleAccessLevel($item['access']);
			$record->showtitle			= $item['showtitle'];
			$record->params				= $this->IniToJson($item['params']);
			$record->client_id			= $item['client_id'];
			$record->language			= (isset($item['language'])) ? $item['language'] : '*';

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'modules',$srcid);
			
			//Assign the module to menus
			$query->clear()
				->select('DISTINCT mm.*')
				->from('#__modules_menu mm')
				->join('INNER','#__menu m ON (m.id = mm.menuid OR mm.menuid=0)')
				->where('moduleid='.$this->source_db->q($srcid));
			$menuAssignements = $this->source_db->setQuery($query)->loadObjectList();
			
			$menu_records = array();
			foreach ($menuAssignements as $assignment) {
				if ($assignment->menuid < 0) {
					$newMenuid = $this->getNewMenuId(-$assignment->menuid);
					$assignment->menuid = ($newMenuid) ? (-$newMenuid) : 0;
				} else {
					$assignment->menuid = $this->getNewMenuId($assignment->menuid);
				}
				$menu_records[] = $assignment;
			}
			
			try {
				$this->destination_db->transactionStart();
				$this->insertOrReplace('#__modules', $record, 'id');
				//$this->deleteRows('#__modules_menu',"moduleid='".$srcid."'");
				//Cleanup modules assignments
				$this->destination_db->setQuery($cleanupQuery)->query();
				foreach ($menu_records as $assignment) {
					$assignment->moduleid = $record->id;
					$this->destination_db->insertObject('#__modules_menu', $assignment);
				}
				$this->logRow($srcid,$item['title'].' ('.$item['module'].')',$record->id);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						$recordLang->id = null;
						if ($this->insertOrReplace('#__modules', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->title,$recordLang->id);
							foreach ($menu_records as $assignment) {
								$assignment->moduleid = $recordLang->id;
								$this->destination_db->insertObject('#__modules_menu', $assignment);
							}
						}
					}
				}
				$this->destination_db->transactionCommit();
				if ($raiseWarning) {
					$this->logWarning(JText::sprintf('JOOM_MODULE_NOT_IMPORTED',$item['module'],$item['title']));
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
	
	public function joom_media_settings() {
		
		if (version_compare($this->joomla_version_src, '1.6', 'ge')) {
			$src_where = "`element`='com_media' AND params<>''";
		} else {
			$src_where = "`option`='com_media' AND params<>''";
		}
		$dst_where = "`element`='com_media'";
		$properties=array();

		$this->migrateComponentSettings($src_where,$dst_where,$properties);
	}

	public function joom_images() {
		
		$dest = JPATH_SITE.'/images';
		$src = '/images';
		if (version_compare($this->joomla_version_dest, 3, '<')) {
			$bkup = '/images_'.JFactory::getDate()->toFormat('_Ymd_Hi');
		} else {
			$bkup = '/images_'.JFactory::getDate()->format('_Ymd_Hi');
		}

        JFolder::move($src, $bkup, JPATH_SITE);
        JFolder::create(JPATH_SITE . '/images');
		$this->logInfo(JText::sprintf('JOOM_IMAGES_FOLDER_BACKUP',$src,$bkup));
		if ($this->source_filehelper->mode == 'path') {
			return;
		}
		
		
		$query = $this->destination_db->getQuery(true);
		$query->select('count(*)')
				->from('#__vmmigrate_temp');
		$imported = $result = $this->destination_db->setQuery($query)->loadResult();
		
		if (!$imported) {
			//Fill the temporary table with the images paths
			$this->readRecus($src);
		}
		
		return;
	
	}
	
	private function readRecus($src) {

		$files = $this->source_filehelper->Files($src);
		foreach ($files as $file) {
			$filepath = new stdClass();
			$filepath->path = $src.'/'.$file['name'];
			$this->destination_db->insertObject('#__vmmigrate_temp', $filepath);
			$this->logInfo($filepath->path);
		}
		$folders = $this->source_filehelper->Folders($src);
		if (count($folders)==0) {
			return false;
		} else {
			foreach ($folders as $folder) {
				$this->readRecus($src.'/'.$folder['name']);
			}
		}
	}
	
	public function joom_images_get() {

		if ($this->source_filehelper->mode == 'path') {
			$dest = JPATH_SITE.'/images';
			$src = '/images';
			if ($this->source_filehelper->CopyFolder($src,$dest)) {
				$this->logInfo(JText::sprintf('JOOM_IMAGES_FOLDER_COPIED',$src));
			} else {
				$this->logError(JText::sprintf('JOOM_IMAGES_FOLDER_NOT_COPIED',$src));
			}
			return;
		}
		
		$this->limit = 2;
		$pk = 'id';
        $excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items

		$query = $this->destination_db->getQuery(true);
		//Get the number of source rows to calculate progression percentage
		$query->select('count(*)')
				->from('#__vmmigrate_temp');
		$total_src_rows = $this->destination_db->setQuery($query)->loadResult();
		
		//Now get the actual records to transfer (excluding those already transfered)
		$query->clear()->select('*')
				->from('#__vmmigrate_temp')
				->where("id NOT IN ('".implode("','",$excludeids)."')");
		$result = $this->destination_db->setQuery($query)->query();
		$total_rows_to_transfer = $this->destination_db->getNumRows();
		if ($total_rows_to_transfer) {
			$this->status['percentage'] = round((( 100 * ($this->total_transfered + $this->limit) ) / $total_src_rows),2);
		} else {
			$this->status['percentage'] = 100;
		}
		
		if ($total_rows_to_transfer > $this->limit) {
	        $this->destination_db->setQuery($query,0,$this->limit);
			$this->moreResults = true;
		} else {
			$this->moreResults = false;
		}
        $items = $this->destination_db->loadAssocList();
		
		foreach ($items as $i => $item) {
			
			$srcid = $item['id'];

			if ($this->source_filehelper->CopyFile($item['path'],JPATH_SITE.$item['path'])) {
				$this->logRow($srcid,$item['path']);
			} else {
				$this->logError($item['path'],$srcid);
			}
		}

		if ($this->moreResults) {
			return true;
		}

	}

	/*******************/
	/* Private helpers */
	/*******************/
	private function getTimeZoneName($offset) {
		
		$timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
		return $timezone_name;
	}

	private function ImportMenuItems($menutype,$oldparentid=0,$newparentid=0,$path='') {
		$pk = 'id';
        //$excludeids = $this->getAlreadyTransferedIds();			// Load already transfered items
		$menuItemsFilter = "published<>-2 AND menutype='".$menutype."' AND parent='".$oldparentid."'";
		$items = $this->getItems2BTransfered('menu',$pk,array(),$menuItemsFilter,'ordering');	//Get a list of source objects to be transfered
        if (!$items) {
			$this->logDebug('No menu item for condition '.$menuItemsFilter);
            return false;
        }

		$i = 0;
        foreach ($items as $i => $item) {
			$srcid = $item[$pk];	//Set the primary key
			
			if ($item['params']) {
				$newparams = new JRegistry();
				$newparams->loadString($item['params'], 'INI');
				//Fix menu image
				if ($newparams->get('menu_image')=='-1') $newparams->set('menu_image','');
				//Transform parameters
				$newparams->set('show_page_heading',$newparams->get('show_page_title','0'));
				$newparams->set('page_heading',$newparams->get('page_title',''));
				//Fix alias menus
				//if ($newparams->get('menu_item')) {
				//	$newparams->set('aliasoptions',$newparams->get('menu_item'));
				//}
				$menuparams = $newparams->toString('JSON');
			} else {
				$menuparams = '';
			}
			
			$record = new stdClass(); 
			//$record->id				= $item['id'];
			$record->menutype		= $item['menutype'];
			$record->title			= $item['name'];
			if (version_compare($this->joomla_version_src, 1.5, '<')) {
				if (array_key_exists('title',$item)) {
					$record->alias			= JApplication::stringURLSafe($item['title']);
				} else if (array_key_exists('name',$item)) {
					$record->alias			= JApplication::stringURLSafe($item['name']);
				}
			} else {
				$record->alias			= $item['alias'];
				$record->home			= $item['home'];
			}
			$record->path			= ($path) ? $path.'/'.$record->alias : $record->alias;
			$record->link			= $this->fixJoomlaLink($item['link']);
			$record->type			= $this->fixType($item['type']);
			$record->published		= $item['published'];
			$record->parent_id		= ($newparentid) ? $newparentid : 1;
			$record->level			= intval($item['sublevel'])+1;
			if (version_compare($this->joomla_version_src, 1.6, '<')) {
				$record->component_id	= ($item['type']=='component' || $item['type']=='components') ? $this->getExtensionId($item['componentid']) : 0;
			} else {
				$record->component_id	= ($item['type']=='component' || $item['type']=='components') ? $this->getExtensionId($item['component_id']) : 0;
			}
			$record->checked_out	= 0;
			$record->browserNav		= $item['browserNav'];
			$record->access			= $this->handleAccessLevel($item['access']);
			$record->img			= ''; //This is for admin menus only
			//$record->template_style_id	= '';
			$record->params			= $menuparams;
			//$record->lft			= null;
			//$record->rgt			= null;
			if (version_compare($this->joomla_version_dest, 3, '<')) {
				$record->ordering		= $item['ordering'];
			}
			
			$record->language		= (isset($item['language'])) ? $item['language'] : '*';
			$record->client_id		= 0; //Frontend menu item
			
			$record->alias			= $this->checkSlug('#__menu',$record->alias,'alias',$srcid,'id','','parent_id='.$record->parent_id.' AND language='.$this->destination_db->q($record->language));

			/* Handle JoomFish translations */		
			$translatedRecords = $this->getTranslationsRecords($record,'menu',$srcid,'id',array('name'=>'title'));
			
			try {
				$this->destination_db->transactionStart();
				$this->destination_db->insertObject('#__menu', $record,'id');
				$newmenuitemid = $record->id;
				$this->logRow($srcid,'New id: '.$newmenuitemid.', '.$menutype.'/'.$record->path,$newmenuitemid);
				if ($translatedRecords) {
					foreach ($translatedRecords as $recordLang) {
						if ($this->insertOrReplace('#__menu', $recordLang,'id')) {
							$this->logTranslation($srcid,$recordLang->language,$recordLang->title,$recordLang->id);
						}
					}
				}
				$this->destination_db->transactionCommit();
				if (!$this->ImportMenuItems($menutype,$item['id'],$newmenuitemid,$record->path)) {
					continue;
				}
			} catch (Exception $e) {
				$this->destination_db->transactionRollback();
				$this->logError($e->getMessage(),$srcid);
			}
			$i++;
		}
	}
	
	private function fixJoomlaLink($link) {
		$uri = JURI::getInstance($link);
		$option = $uri->getVar('option');
		$view = $uri->getVar('view');
		$task = $uri->getVar('task');
		$layout = $uri->getVar('layout');
		if ($option=='com_content' && $view=='frontpage') {
			$uri->setVar('view','featured');
		}
		if ($option=='com_content' && $view=='section') {
			$uri->setVar('view','category');
			$uri->setVar('id',intval($uri->getVar('id'))+10000);
		}
		if ($option=='com_content' && $view=='article' && $layout=='form') {
			$uri->setVar('view','form');
			$uri->setVar('layout','edit');
		}
		if ($option=='com_user') {
			$uri->setVar('option','com_users');
		}
		if ($option=='com_user' && $view=='user') {
			$uri->setVar('view','profile');
		}
		if ($option=='com_user' && $view=='user' && $task=='edit') {
			$uri->setVar('layout','edit');
			$uri->delVar('task');
		}
		if ($option=='com_user' && $view=='register') {
			$uri->setVar('view','registration');
		}
		if ($option=='com_weblinks' && $view=='weblink' && $layout=='form') {
			$uri->setVar('view','form');
			$uri->setVar('layout','edit');
		}
		if ($option=='com_frontpage') {
			$uri->setVar('option','com_content');
			$uri->setVar('view','featured');
		}
		return $uri->toString();
	}
	
	private function fixType($type) {
		switch ($type) {
			case 'menulink': $type = 'alias'; break;
			case 'components': $type = 'component'; break;
		}
		return $type;
	}
	
	private function FixMenuAlias() {
		//Get the menu types to filter the menus
		$query = $this->destination_db->getQuery(true);
		$query->select('*')->from('#__menu')->where("type='alias'");
		$this->destination_db->setQuery($query);
		$aliasmenus = $this->destination_db->loadObjectList();
		
		foreach ($aliasmenus as $menu) {
			$uri = JURI::getInstance($menu->link);
			$uri->setVar('Itemid','');
			
			$newparams = new JRegistry();
			$newparams->loadString($menu->params, 'JSON');
			$oldItemId = $newparams->get('menu_item');
			
			$newItemId = $this->getNewMenuId($oldItemId);
			
			$newparams->set('aliasoptions', $newItemId);
			$newparams->set('menu_item', '');
			
			$query->clear()
				->update('#__menu')
				->set($this->destination_db->qn('params').'='.$this->destination_db->q($newparams->toString('JSON')))
				->set($this->destination_db->qn('link').'='.$this->destination_db->q($uri->toString()))
				->where("id=".$this->destination_db->q($menu->id));
				
			$this->destination_db->setQuery($query)->query();
		}
		
	}
	
	private function getNewMenuId($oldItemId) {
		$query = $this->destination_db->getQuery(true);
		$query->select('destination_id')
			->from('#__vmmigrate_log')
			->where("task='joom_menus'")
			->where("source_id='".$oldItemId."'");
		$newItemId = $this->destination_db->setQuery($query)->LoadResult();
		return ($newItemId) ? $newItemId : 0;
	}
	
	private function handleAccessLevel($access) {
		if (version_compare($this->joomla_version_src, '1.6', '<')) {
			return intval($access)+1;
		} else {
			return $access;
		}
	}

}
