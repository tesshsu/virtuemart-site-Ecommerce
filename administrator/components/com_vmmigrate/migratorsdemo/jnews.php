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

class VMMigrateModelJnews extends VMMigrateModelBase {

    function __construct($config = array()) {
        //parent::__construct($config);
    }
	
	public static function getSteps() {
		$steps = array();
		$steps[] = array('name'=>'reset_log'						,'default'=>0);
		$steps[] = array('name'=>'jnews_mailings'					,'default'=>1);
		$steps[] = array('name'=>'jnews_queue'						,'default'=>1);
		$steps[] = array('name'=>'jnews_stats_global'				,'default'=>1);
		$steps[] = array('name'=>'jnews_stats_stats_details'		,'default'=>1);
		return $steps;
	}

	protected function jnews_mailings() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function jnews_queue() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function jnews_stats_global() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	protected function jnews_stats_stats_details() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
}
