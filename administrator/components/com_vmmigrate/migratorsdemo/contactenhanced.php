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

class VMMigrateModelContactenhanced extends VMMigrateModelBase {

	public $isPro = false;

    function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public static function getSteps() {
		if (!self::isInstalledDest('com_contactenhanced') && !self::isInstalledSource('com_contact_enhanced')) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'			,'default'=>0);
		$steps[] = array('name'=>'reset_log_error'		,'default'=>1);
		$steps[] = array('name'=>'reset_data'			,'default'=>0);
		$steps[] = array('name'=>'ce_settings'			,'default'=>1);
		$steps[] = array('name'=>'menu_items'			,'default'=>1);
		$steps[] = array('name'=>'ce_fields'			,'default'=>1);
		$steps[] = array('name'=>'ce_values'			,'default'=>1);
		$steps[] = array('name'=>'ce_details'			,'default'=>1);
		$steps[] = array('name'=>'ce_messages'			,'default'=>1);
		return $steps;
	}

	public function reset_data() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function ce_settings() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function menu_items() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function ce_fields() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function ce_details() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function ce_messages() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
}
