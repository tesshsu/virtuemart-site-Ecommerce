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

class VMMigrateModelAlphaup extends VMMigrateModelBase {

    function __construct($config = array()) {
        //parent::__construct($config);
    }

	public static function getSteps() {
		if (!self::isInstalledBoth('com_alphauserpoints')) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'					,'default'=>0);
		$steps[] = array('name'=>'reset_data'					,'default'=>0);
		$steps[] = array('name'=>'aup_medals'					,'default'=>1);
		$steps[] = array('name'=>'aup_levelrank'				,'default'=>1);
		$steps[] = array('name'=>'aup_coupons'					,'default'=>1);
		$steps[] = array('name'=>'aup_users'					,'default'=>1);
		$steps[] = array('name'=>'aup_details'					,'default'=>1);
		$steps[] = array('name'=>'aup_details_archive'			,'default'=>1);
		return $steps;
	}

	public function reset_data() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	
	protected function aup_medals() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	protected function aup_levelrank() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function aup_coupons() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function aup_users() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	

	protected function aup_details() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function aup_details_archive() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	
}
