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
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class VMMigrateModelExtensions extends JModelList {

	protected $source_db;
	protected $destination_db;
	protected $source_filehelper;
	protected $joomla_version_src;

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JController
     * @since	1.6
     */
    public function __construct($config = array()) {

        $this->source_db = VMMigrateHelperDatabase::getSourceDb();
		$this->destination_db = JFactory::getDbo();
        $this->source_filehelper = VMMigrateHelperFilesystem::getInstance();
        $this->joomla_version_src = self::getJoomlaVersionSource();

        parent::__construct($config);
    }

	public static function getJoomlaVersionSource(){
		$params = JComponentHelper::getParams('com_vmmigrate');
		$joomla_version_src = $params->get('joomla_version','1.5');
		return $joomla_version_src;
	}

    public function getLanguages() {	
		return $this->getExtensions('language');
    }
	
    public function getPackages() {	
		return $this->getExtensions('package');
    }
	
    public function getComponents() {
		return $this->getExtensions('component');
    }

    public function getModules() {
		return $this->getExtensions('module');
    }
	
    public function getPlugins() {
		return $this->getExtensions('plugin');
    }
	
    public function getTemplates() {
		return $this->getExtensions('template');
    }
	
    protected function getExtensions($type='component') {
        // Get the application object
        $app = JFactory::getApplication();
		$query_src = $this->source_db->getQuery(true);
		$items = array();

		if (version_compare($this->joomla_version_src,'1.6','<')) {
			if ($type=='component-') {
//				$query_src->select('distinct name, '.$this->source_db->qn('option').' as element')
//					->from('#__components')
//					->where('iscore = 0')
//					->where('name <> ""')
//					->order('name');
//				$items = $this->source_db->setQuery($query_src)->loadObjectList();
//				foreach ($items as &$item) {
//					$item->client_id = 0;
//					$item->folder = '';
//					$xmlFile1 = '/administrator/components/'.$item->element.'/'.$item->element.'.xml';
//					$xmlFile2 = '/administrator/components/'.$item->element.'/manifest.xml';
//					$xmlFile3 = '/administrator/components/'.$item->element.'/'.str_replace('com_','',$item->element).'.xml';
//					if ($this->source_filehelper->FileExists($xmlFile1)) {
//						$xml = $this->source_filehelper->ReadXmlFile($xmlFile1);
//						$item->version_src = (string)$xml->version;
//					} else if ($this->source_filehelper->FileExists($xmlFile2)) {
//						$xml = $this->source_filehelper->ReadXmlFile($xmlFile2);
//						$item->version_src = (string)$xml->version;
//					} else if ($this->source_filehelper->FileExists($xmlFile3)) {
//						$xml = $this->source_filehelper->ReadXmlFile($xmlFile3);
//						$item->version_src = (string)$xml->version;
//					}
//					$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
//				}
			} else if ($type=='plugin') {
				$query_src->select('distinct name, element, folder, client_id')
					->from('#__plugins')
					->where('iscore = 0')
					->where('name <> ""')
					->order('folder, name');
				$items = $this->source_db->setQuery($query_src)->loadObjectList();
				foreach ($items as &$item) {
					$xmlFile1 = '/plugins/'.$item->folder.'/'.$item->element.'.xml';
					if ($this->source_filehelper->FileExists($xmlFile1)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile1);
						$item->version_src = (string)$xml->version;
					}
					$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
				}
			} else if ($type=='component') {
				$components = $this->source_filehelper->Folders('/administrator/components');
				$items = array();
				foreach ($components as $component) {
					$item = new stdClass();
					$item->name = $component;
					$item->element = $component;
					$item->folder = '';
					$xmlFile1 = '/administrator/components/'.$item->element.'/'.$item->element.'.xml';
					$xmlFile2 = '/administrator/components/'.$item->element.'/manifest.xml';
					$xmlFile3 = '/administrator/components/'.$item->element.'/'.str_replace('com_','',$item->element).'.xml';
					if ($this->source_filehelper->FileExists($xmlFile1)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile1);
						$item->version_src = (string)$xml->version;
					} else if ($this->source_filehelper->FileExists($xmlFile2)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile2);
						$item->version_src = (string)$xml->version;
					} else if ($this->source_filehelper->FileExists($xmlFile3)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile3);
						$item->version_src = (string)$xml->version;
					}
					$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
					$items[] = $item;
				}
			} else if ($type=='module') {
				$modules = $this->source_filehelper->Folders('/modules');
				$items = array();
				foreach ($modules as $module) {
					$item = new stdClass();
					$item->name = $module;
					$item->element = $module;
					$item->folder = '';
					$xmlFile = '/modules/'.$module.'/'.$module.'.xml';
					if ($this->source_filehelper->FileExists($xmlFile)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile);
						$item->version_src = (string)$xml->version;
					}
					$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
					$items[] = $item;
				}
			} else if ($type=='template') {
				$templates = $this->source_filehelper->Folders('/templates');
				$items = array();
				foreach ($templates as $template) {
					$item = new stdClass();
					$item->name = $template;
					$item->element = $template;
					$item->folder = '';
					$xmlFile = '/templates/'.$template.'/templateDetails.xml';
					if ($this->source_filehelper->FileExists($xmlFile)) {
						$xml = $this->source_filehelper->ReadXmlFile($xmlFile);
						$item->version_src = (string)$xml->version;
					}
					$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
					$items[] = $item;
				}
			}
		} else {
			$query_src->select('name, element, manifest_cache, client_id, folder')
				->from('#__extensions')
				->where('type = '.$this->source_db->q($type))
				->where('protected = 0')
				->order('folder, name');
			$items = $this->source_db->setQuery($query_src)->loadObjectList();
			foreach ($items as &$item) {
				$manifest_cache = new JRegistry();
				$manifest_cache->loadString($item->manifest_cache, 'JSON');
				$item->version_src = $manifest_cache->get('version');
				$item->version_dst = $this->getLocalVersion($item->element,$type,$item->folder);
			}
		}
		
        return $items;
    }
	
	protected function getLocalVersion($element,$type,$folder='') {

		$query_dst = $this->destination_db->getQuery(true);
		$query_dst->select('*')
			->from('#__extensions')
			->where('type = '.$this->destination_db->q($type))
			->where('element = '.$this->destination_db->q($element));
		if ($folder) {
			$query_dst->where('folder = '.$this->destination_db->q($folder));
		}

		$item = $this->destination_db->setQuery($query_dst)->loadObject();
		$version_dst = -1;
		
		if ($item) {
			$manifest_cache = new JRegistry();
			$manifest_cache->loadString($item->manifest_cache, 'JSON');
			$version_dst = $manifest_cache->get('version');
		}
		
		return $version_dst;
	}


}
