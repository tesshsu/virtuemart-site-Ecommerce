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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

abstract class VMMigrateHelperVMMigrate {
	
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(JText::_('MIGRATE'), 'index.php?option=com_vmmigrate&view=upgrade',($vName=='upgrade'));
		JSubMenuHelper::addEntry(JText::_('VMMIGRATE_EXTENSIONS'), 'index.php?option=com_vmmigrate&view=extensions',($vName=='extensions'));
		JSubMenuHelper::addEntry(JText::_('HISTORY'), 'index.php?option=com_vmmigrate&view=log',($vName=='log'));
		JSubMenuHelper::addEntry(JText::_('VMMIGRATE_ABOUT'), 'index.php?option=com_vmmigrate&view=about',($vName=='about'));
	}

	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = JAccess::getActions('com_vmmigrate');

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, 'com_vmmigrate'));
		}

		return $result;
	}

	public static function GetMigrators() {
		$migrators = JFolder::files(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators','.php');
		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migrators');
		$extensions[] = 'joomla';
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators/virtuemart.php')) {
			$instance = new  VMMigrateModelVirtuemart();
			$srcVersion = $instance->getSrcVersionPrivate();
			if ($srcVersion && version_compare($srcVersion,'2','<')) {
				$extensions[] = 'virtuemart';
			}
		}
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators/virtuemart2.php')) {
			$instance = new  VMMigrateModelVirtuemart2();
			$srcVersion = $instance->getSrcVersion();
			if (version_compare($srcVersion,'2','>=') && version_compare($srcVersion,'3.0','<')) {
				$extensions[] = 'virtuemart2';
			}
		}
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators/virtuemart3.php')) {
			$instance = new  VMMigrateModelVirtuemart3();
			$srcVersion = $instance->getSrcVersion();
			if (version_compare($srcVersion,'3.0','>=')) {
				$extensions[] = 'virtuemart3';
			}
		}
		//Get a list of installed migrators
		foreach ($migrators as $migrator) {
			$extension = basename($migrator,'.php');
			if ($extension != 'joomla' & $extension != 'virtuemart' & $extension != 'virtuemart2' & $extension != 'virtuemart3') {
				$extensions[] = $extension;
			}
		}
		$lang = JFactory::getLanguage();
		//Load the migrator language
		foreach ($extensions as $extension) {
			$lang->load('com_vmmigrate.'.$extension,JPATH_COMPONENT_ADMINISTRATOR,null,true);		
		}
		return $extensions;
	}

	public static function GetMigratorsSteps($extensions=array()) {
		
		if (!count($extensions)) {
			$extensions = VMMigrateHelperVMMigrate::GetMigrators();
		}

		$steps = array();
		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migrators');
		//Load the migrator steps
		foreach ($extensions as $extension) {
			$model = JModelLegacy::getInstance( $extension,'VMMigrateModel' );
			$steps[$extension] = $model->getSteps();
		}
		return $steps;
	}

	public static function GetMigratorsMessages($extensions=array()) {
		
		if (!count($extensions)) {
			$extensions = VMMigrateHelperVMMigrate::GetMigrators();
		}

		$messages = array();
		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migrators');
		//Load the migrator steps
		foreach ($extensions as $extension) {
			$model = JModelLegacy::getInstance( $extension,'VMMigrateModel' );
			$messages[$extension] = $model->getMessages();
		}
		return $messages;
	}

	public static function GetMigratorsDemo() {
		$migrators = JFolder::files(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migratorsdemo','.php');
		//Get a list of installed migrators
		foreach ($migrators as $migrator) {
			$extension = basename($migrator,'.php');
			if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators/'.$migrator)) {
				$extensions[] = $extension;
			}
		}
		$lang = JFactory::getLanguage();
		//Load the migrator language
		foreach ($extensions as $extension) {
			$lang->load('com_vmmigrate.'.$extension,JPATH_COMPONENT_ADMINISTRATOR,null,true);		
		}
		return $extensions;
	}

	public static function GetMigratorsDemoSteps($extensions=array()) {
		
		if (!count($extensions)) {
			$extensions = VMMigrateHelperVMMigrate::GetMigratorsDemo();
		}

		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migratorsdemo');
		//Load the migrator steps
		foreach ($extensions as $extension) {
			$model = JModelLegacy::getInstance( $extension,'VMMigrateModel' );
			$steps[$extension] = $model->getSteps();
		}
		return $steps;
	}

	public static function GetMigratorsPro($extensions=array()) {
		
		if (!count($extensions)) {
			$extensions = VMMigrateHelperVMMigrate::GetMigrators();
		}

		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migrators');
		//Load the migrator steps
		foreach ($extensions as $extension) {
			$model = JModelLegacy::getInstance( $extension,'VMMigrateModel' );
			$proversions[$extension] = $model->isPro;
		}
		return $proversions;
	}

	public static function GetMigratorsOptions() {
		
		$extensions = VMMigrateHelperVMMigrate::GetMigrators();
		
		$options[] = JHTML::_('select.option', '', JText::_('VMMIGRATE_SELECT_EXTENSION'));
		foreach ($extensions as $extension) {
			$name = JText::_($extension);
			$name = str_replace("<i class='fa fa-arrow-right'></i>","->",$name);
			$options[] = JHTML::_('select.option', $extension, $name);
		}
		return $options;
	}

	public static function GetMigratorsStepsOptions($extension='') {
		
		if (!$extension) {
			$options[] = JHTML::_('select.option', '', JText::_('VMMIGRATE_SELECT_EXTENSION_FIRST'));
			return $options;
		} else {
			$steps = VMMigrateHelperVMMigrate::GetMigratorsSteps(array($extension));
			$extensionSteps = $steps[$extension];
			$options[] = JHTML::_('select.option', '', JText::_('VMMIGRATE_SELECT_TASK'));
			foreach ($extensionSteps as $step) {
				$name = JText::_($step['name']);
				$options[] = JHTML::_('select.option', $step['name'], $name);
			}
			return $options;
		}
	}

	public static function GetLogStatesOptions($selected='') {
		
		$options[] = JHTML::_('select.option', '', JText::_('VMMIGRATE_SELECT_STATE'));
		$options[] = JHTML::_('select.option', '1', JText::_('VMMIGRATE_SUCCESS'));
		$options[] = JHTML::_('select.option', '2', JText::_('VMMIGRATE_WARNING'));
		$options[] = JHTML::_('select.option', '3', JText::_('VMMIGRATE_ERROR'));
		$options[] = JHTML::_('select.option', '4', JText::_('VMMIGRATE_TRANSLATIONS'));
		return $options;
	}
	
	public static function loadCssJs() {
		$doc =  JFactory::getDocument();
		$doc->addScriptDeclaration('
			var vmmigrate_waiting = "'.JText::_('VMMIGRATE_WAITING').'";
			var vmmigrate_skip = "'.JText::_('VMMIGRATE_SKIP').'";
			var vmmigrate_completed = "'.JText::_('VMMIGRATE_COMPLETED').'";
			var vmmigrate_completed_errors = "'.JText::_('VMMIGRATE_COMPLETED_WITH_ERRORS').'";
			var vmmigrate_completed_warnings = "'.JText::_('VMMIGRATE_COMPLETED_WITH_WARNINGS').'";
			var vmmigrate_all_completed = "'.JText::_('VMMIGRATE_ALL_COMPLETED').'";
			var vmmigrate_process_paused = "'.JText::_('VMMIGRATE_PAUSED').'";
		');

		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();
		if (version_compare($joomla_version_dest, 3, 'gt')) {
			// load jQuery, if not loaded before
			if (!JFactory::getApplication()->get('jquery')) {
				JFactory::getApplication()->set('jquery', true);
				JHtml::_('jquery.framework');
			}
			//Load bootstrap (Hator template does not load it
			//JHtml::_('bootstrap.framework');
			//JHtml::_('bootstrap.loadCss', true);
			//JHtml::_('bootstrap.tooltip');
			$doc->addScript(JURI::root().'administrator/components/com_vmmigrate/assets/js/jquery-ui.js');
			$doc->addStyleSheet(JURI::root().'administrator/components/com_vmmigrate/assets/css/adminj3.css');
		} else {
			// load jQuery, if not loaded before
			if (!JFactory::getApplication()->get('jquery')) {
				JFactory::getApplication()->set('jquery', true);
				$doc->addScript(JURI::root().'administrator/components/com_vmmigrate/assets/js/jquery-1.9.1.js');
			}
			$doc->addScript(JURI::root().'administrator/components/com_vmmigrate/assets/js/jquery-ui.js');
			$doc->addStyleSheet(JURI::root().'administrator/components/com_vmmigrate/assets/css/admin.css');
		}
		$doc->addStyleSheet(JURI::root().'administrator/components/com_vmmigrate/assets/css/font-awesome.min.css');
		$doc->addStyleSheet(JURI::root().'administrator/components/com_vmmigrate/assets/css/jquery-ui.css');
		$doc->addScript(JURI::root().'administrator/components/com_vmmigrate/assets/js/vmmigrate.js');
		
	}
	
	public static function setJoomlaVersionLayout(&$view) {
		//Set a custom layout for Joomla 3 if present
		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();
		try {
			if (version_compare($joomla_version_dest, 3, 'gt')) {
				$layout = $view->getLayout();
				$view->setLayout($layout.'_j3');
			}
		} catch (Exception $e) {
		}
	}
	
	static public function getCPsRssFeed($rssUrl,$max) {

		$cache_time=86400*3; // 3days
		$cache = JFactory::getCache ('com_vmmigrate_rss');
		$cached = $cache->getCaching();
		$cache->setLifeTime($cache_time);
		$cache->setCaching (1);

		if (JDEBUG) {
			$cache_time = 0;
			$cache->setCaching (0);
		}
		
		$feeds = $cache->call (array('VMMigrateHelperVMMigrate', 'getRssFeed'), $rssUrl, $max);
		$cache->setCaching ($cached);
		return $feeds;
	}


	static public function getRssFeedJ25 ($rssURL,$max) {
		jimport('simplepie.simplepie');
		$rssFeed = new SimplePie($rssURL);
		$feeds = array();
		$count = $rssFeed->get_item_quantity();
		$limit=min($max,$count);
		for ($i = 0; $i < $limit; $i++) {
			$feed = new StdClass();
			$item = $rssFeed->get_item($i);
			$feed->link = $item->get_link();
			$feed->title = $item->get_title();
			$feed->description = $item->get_description();
			$feeds[] = $feed;
		}

		return $feeds;
	}


	static public function getRssFeed ($rssURL,$max) {

		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();
		if (version_compare($joomla_version_dest, 3, '<')) {
			return self::getRssFeedJ25($rssURL,$max);
		}
		
		$feeds = array();
		$feed = new JFeedFactory;
		$rssDoc = $feed->getFeed($rssURL);
		if(empty($rssDoc)) return false;

		for ($i = 0; $i < $max; $i++) {
			if (!$rssDoc->offsetExists($i)) {
				break;
			}
			$item = $rssDoc[$i];
			$temp = new StdClass();
			$temp->link = $item->__get('uri');
			$temp->title = $item->__get('title');
			$temp->title = trim(str_ireplace('addon','',$temp->title));
			$temp->description = $item->__get('content');
			$feeds[] = $temp;
		}
		
		return $feeds;
	}

	public static $extFeeds = 0;
	static public function getExtensionsRssFeed() {
		if (empty(self::$extFeeds)) {
			self::$extFeeds =  VMMigrateHelperVMMigrate::getCPsRssFeed("https://www.daycounts.com/shop/migrator-addons/by,ordering/?format=feed&type=rss", 50);
		}
		return self::$extFeeds;
	}

	public static function getVersionInfo() {
		$xml = JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_vmmigrate/com_vmmigrate.xml');
		$version = (string) $xml->version;
		$versioncat  = (string)$xml->custom->versioncheckercat;
		
		$url = 'https://www.daycounts.com/index.php?option=com_versions&catid='.$versioncat.'&myVersion='.$version.'&task=checkjson';
		if(function_exists('curl_exec')) {
			// Use cURL
			$curl_options = array(
				CURLOPT_AUTOREFERER		=> true,
				CURLOPT_FAILONERROR		=> true,
				CURLOPT_HEADER			=> false,
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_CONNECTTIMEOUT	=> 5,
				CURLOPT_MAXREDIRS		=> 20,
				CURLOPT_USERAGENT		=> 'Daycounts Updater'
			);
			$ch = curl_init($url);
			foreach($curl_options as $option => $value)	{
				@curl_setopt($ch, $option, $value);
			}
			$data = curl_exec($ch);
		} elseif( ini_get('allow_url_fopen') ) {
			// Use fopen() wrappers
			$options = array( 'http' => array(
				'max_redirects' => 10,          // stop after 10 redirects
				'timeout'       => 20,         // timeout on response
				'user_agent'	=> 'Daycounts Updater'
			) );
			$context = stream_context_create( $options );
			$data = @file_get_contents( $url, false, $context );
		} else {
			$data = false;
		}
		$json = @json_decode($data, true);
		$json = JFilterInput::getInstance()->clean($json,'none');
		return $json;
	}
	
	
}