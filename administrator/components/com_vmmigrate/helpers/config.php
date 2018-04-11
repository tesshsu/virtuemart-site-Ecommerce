<?php
/*------------------------------------------------------------------------
# vminventory - Virtuemart 2 Inventory management
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.helper');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.simplexml' );
jimport('joomla.updater.update');

class VMMigrateHelperConfig {

	var $versioncat	= '33';
	var $component_name = 'com_vmmigrate';
	
	function __construct() {
		$this->load();
	}

	function &getInstance() {
		static $config_instance;

		if (!isset( $config_instance )) {
			$config_instance	= new VMMigrateHelperConfig();
		}
		return $config_instance;
		
	}
	
	function render($group = '_default') {

		$paramsdefs = JPATH_ADMINISTRATOR."/components/".$this->component_name."/config.xml";
		$paramsdata = JComponentHelper::getParams($this->component_name);
		$params = new JParameter( $paramsdata->toString(), $paramsdefs );
		echo $params->render('params',$group);
	}
	
	function load() {
		$this->params = JComponentHelper::getParams($this->component_name);
		foreach ($this->params->toArray() as $key=>$val) {
			$this->{$key} = $val;
		}

		//Load the version number from the install XML file.
		$xml = JFactory::getXML(JPATH_ADMINISTRATOR.'/components/'.$this->component_name.'/'.$this->component_name.'.xml');
		$this->version = (string) $xml->version;
		$this->versioncat  = (string)$xml->custom->versioncheckercat;
	}
	
	function sanitizePost($post) {

		$sanitizedPost = array();
		foreach ($post as $key=>$val) {
			if (in_array($key,$this->exlcude_post_keys) || $key == JSession::getFormToken()) continue;
			if (is_array($val)) $val = implode(',',$val);
			if (in_array($key,$this->numeric_post_keys) && !is_numeric($val)) $val=0;
			$sanitizedPost[$key] = $val;
		}
		return $sanitizedPost;
	}
	
	function save($post) {
		
		$registry = new JRegistry();
		$post = $this->sanitizePost($post);
		$registry->loadArray($post);

		$component = JComponentHelper::getComponent($this->component_name);
		$table =& JTable::getInstance('extension');
		$table->load($component->id);
		if ($post['downloadcode'] != $this->downloadcode) {
			$this->manageUpdateServer($this->versioncat,$post['downloadcode'],$component->id);
		}
		
		$table->params = $registry->toString();
		$table->store();
		
	}
	
	function manageUpdateServer($versioncat=0,$downloadcode='',$ext_id) {
		
		if ($ext_id && $versioncat) {
			$update_url = 'http://www.daycounts.com/index.php?option=com_versions&catid='.$versioncat.'&download_code='.$downloadcode.'&task=updateserver.xml';

			$db =& JFactory::getDBO();
			//Check if the update site is already defined
			$query = $db->getQuery(true)
					->select($db->qn('update_site_id'))
					->from($db->qn('#__update_sites_extensions'))
					->where($db->qn('extension_id').'='.$db->q($ext_id));
			$db->setQuery($query);
			$updatesiteid = $db->loadResult();
			$updater = JUpdater::getInstance();
			
			if ($updatesiteid) {
				//If found update row ans valid download code
				$query = $db->getQuery(true)
						->update($db->qn('#__update_sites'))
						->set($db->qn('location').'='.$db->q($update_url))
						->where($db->qn('update_site_id').'='.$db->q($updatesiteid));
				$db->setQuery($query);
				$db->query();
				//$updater->findUpdates($ext_id);
			}
			return;
		}
		
	}
}