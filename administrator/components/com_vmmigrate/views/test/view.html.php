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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class VMMigrateViewTest extends JViewLegacy {

    protected $canDo;

	function display($tpl = null)
	{

		$this->canDo = VMMigrateHelperVMMigrate::getActions();
		$this->config = new VMMigrateHelperConfig();

		VMMigrateHelperVMMigrate::loadCssJs();

		$app = JFactory::getApplication();
		
		$this->mode = $app->input->getString('mode', '');
		
		if ($this->mode == 'db') {
			$db = VMMigrateHelperDatabase::createSourceDbo();
			//$this->valid_database_connection = VMMigrateHelperDatabase::isValidConnection();
			$this->valid_database_connection = true;
			$this->validPrefix = VMMigrateHelperDatabase::isValidPrefix();
		}

		if ($this->mode == 'files') {
			$this->valid_source_path = VMMigrateHelperFilesystem::isValidConnection();
		}
		
		parent::display($tpl);

	}

}