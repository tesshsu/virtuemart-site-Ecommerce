<?php

/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @copyright Copyright (C) 2014 iStraxx (haftungsbeschraenkt) - All rights reserved.
 * @license license.txt Proprietary License. This code belongs to iStraxx UG
 * You are not allowed to distribute or sell this code. You bought only a license to use it for ONE virtuemart installation.
 * You are not allowed to modify this code. *
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

if (!class_exists('ShopFunctions'))
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');

jimport('joomla.form.formfield');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class JFormFieldShoppergroup extends JFormField {

	var $type = 'shoppergroup';

	function getInput() {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadJLang('com_virtuemart');
		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		if (!class_exists('ShopFunctions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		return ShopFunctions::renderShopperGroupList($this->value,false,$this->name,'VMUSERFIELD_ISTRAXX_EUVATCHECKER_GROUP_NO_CHANGE');
	}
}
