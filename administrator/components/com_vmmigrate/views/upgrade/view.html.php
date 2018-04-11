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
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.error.exception');

/**
 * View class for a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class VMMigrateViewUpgrade extends JViewLegacy
{

	protected $canDo;
	protected $extensions;
	protected $steps;
	protected $messages;
	protected $demoextensions = array();
	protected $demosteps = array();
	protected $isPro;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		JHtml::_('behavior.keepalive');
		$this->canDo 		= VMMigrateHelperVMMigrate::getActions();
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_vmmigrate');
		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();

		VMMigrateHelperVMMigrate::loadCssJs();
		

		if ($params->get('show_spash_config', 1)) {
			$app->enqueueMessage(JText::_('VMMIGRATE_PLEASE_CONFIGURE'), 'warning');
		} else {

			$this->extensions = VMMigrateHelperVMMigrate::GetMigrators();
			$this->steps = VMMigrateHelperVMMigrate::GetMigratorsSteps($this->extensions);
			$this->messages = VMMigrateHelperVMMigrate::GetMigratorsMessages($this->extensions);

			//print_a($helper->isValidConnection());
			$valid_database_connection = VMMigrateHelperDatabase::isValidConnection();
			
			if (!$valid_database_connection) {
				$app->enqueueMessage(JText::_('VMMIGRATE_SOURCE_DATABASE_CONNECTION_WARNING'), 'error');
			} else {
				$validPrefix = VMMigrateHelperDatabase::isValidPrefix();
				if (!$validPrefix) {
					$app->enqueueMessage(JText::_('VMMIGRATE_SOURCE_DATABASE_CONNECTION_WARNING'), 'error');
				}
			}
	
			$valid_source_path = VMMigrateHelperFilesystem::isValidConnection();
			if (!$valid_source_path) {
				$app->enqueueMessage(JText::_('VMMIGRATE_SOURCE_PATH_STATUS_WARNING'), 'error');
			}
			
			if (JDEBUG) {
				$app->enqueueMessage(JText::_('VMMIGRATE_TURN_OFF_DEBUG'), 'warning');
			}
		}

		
		if ($params->get('show_not_pro', 0)) {
			$this->demoextensions = VMMigrateHelperVMMigrate::GetMigratorsDemo();
			$this->demosteps = VMMigrateHelperVMMigrate::GetMigratorsDemoSteps();
		}
		$this->demoextensions = array();
		
		
		$this->isPro = VMMigrateHelperVMMigrate::GetMigratorsPro($this->extensions);

		$this->addToolbar();
		VMMigrateHelperVMMigrate::setJoomlaVersionLayout($this);
		
		$this->extensionsFeed = array();
		if ($params->get('show_addon', 1)) {
			try {
				$this->extensionsFeed = VMMigrateHelperVMMigrate::getExtensionsRssFeed();
			} catch (Exception $e) {
				$this->extensionsFeed = array();
			}
		}

		$checkversion = $params->get('checkversion', 1);
		if ($checkversion) {
			jimport('joomla.cache.cache');
			$cache = JFactory::getCache('com_vmmigrate:versioncheck');
			$cache->setCaching(true);	
			$cache->setLifeTime(86400);	
			$versionCheck = $cache->call( array( 'VMMigrateHelperVMMigrate', 'getVersionInfo' ));
			if ($versionCheck['valid']==-1) {
				$app->enqueueMessage(JText::_('PLG_DAYCOUNTS_VERSION_UNKNOWN'), 'error');
			} else if ($versionCheck['valid']===0) {
				$app->enqueueMessage(JText::sprintf('PLG_DAYCOUNTS_VERSION_NEW_X',$versionCheck['latest']), 'notice');
			}
		}
		
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title(JText::_('COM_VMMIGRATE'), 'vmmigrate' );

		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_vmmigrate');
		}
		JToolBarHelper::divider();
		$bar->appendButton( 'Popup', 'help', JText::_('help'), 'https://www.daycounts.com/help/vm-migrator/?tmpl=component', 670, 500 );
	}

}
