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

class VMMigrateModelJcomments extends VMMigrateModelBase {

    function __construct($config = array()) {
        //parent::__construct($config);
    }
	
	public static function getSteps() {
		if (!self::isInstalledBoth('com_jcomments')) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'				,'default'=>0);
		$steps[] = array('name'=>'reset_data'				,'default'=>0);
		$steps[] = array('name'=>'copy_config'				,'default'=>1);
		$steps[] = array('name'=>'jcomments_custom_bbcodes'	,'default'=>1);
		$steps[] = array('name'=>'jcomments_comments'		,'default'=>1);
		$steps[] = array('name'=>'jcomments_subscriptions'	,'default'=>1);
		$steps[] = array('name'=>'jcomments_votes'			,'default'=>1);
		return $steps;
	}

	public function reset_data() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function copy_config() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function jcomments_custom_bbcodes() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function jcomments_comments() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function jcomments_subscriptions() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function jcomments_votes() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

		
}
