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

class VMMigrateModelAwocoupon extends VMMigrateModelBase {

    function __construct($config = array()) {
        //parent::__construct($config);
    }
	
	public static function getSteps() {
		if (!self::isInstalledBoth('com_awocoupon')) {
			return array();
		}
		$steps = array();
		$steps[] = array('name'=>'reset_log'		,'default'=>0);
		$steps[] = array('name'=>'reset_data'		,'default'=>0);
		$steps[] = array('name'=>'license'			,'default'=>0);
		$steps[] = array('name'=>'copy_config'		,'default'=>0);
		$steps[] = array('name'=>'profiles'			,'default'=>1);
		$steps[] = array('name'=>'coupons'			,'default'=>1);
		$steps[] = array('name'=>'coupon_usage'		,'default'=>1);
		$steps[] = array('name'=>'giftcert_codes'	,'default'=>1);
		$steps[] = array('name'=>'giftcert'			,'default'=>1);
		$steps[] = array('name'=>'giftcert_usage'	,'default'=>1);
		return $steps;
	}

	public function reset_data() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}
	
	public function license() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function copy_config() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	public function coupons($coupontype='coupon') {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function profiles() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function coupon_usage() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function giftcert_codes() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function giftcert() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

	protected function giftcert_usage() {
		$this->logError(JText::_('VMMIGRATE_GET_PRO_MIGRATOR'));
	}

		
}
