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

class VMMigrateModelSt42download extends VMMigrateModelBase {

	public $isPro = false;

	var $vendorModel;
	var $vendor;
	var $mediaModel;
	var $maxDownload = 0;
	var $maxTime = 0;
	
    function __construct($config = array()) {
        //parent::__construct($config);
    }
	
	public static function getSteps() {
		$steps = array();
		$steps[] = array('name'=>'reset_log'				,'default'=>0);
		$steps[] = array('name'=>'reset_data'				,'default'=>0);
		$steps[] = array('name'=>'vm_product_downloads'		,'default'=>1);
		$steps[] = array('name'=>'vm_orders_downloads'		,'default'=>1);
		return $steps;
	}

	public function reset_data() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function vm_product_downloads() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function vm_orders_downloads() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

}
