<?php

/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @copyright Copyright (C) 2012 iStraxx - All rights reserved.
 * @license license.txt Proprietary License. This code belongs to iStraxx UG
 * You are not allowed to distribute or sell this code. You bought only a license to use it for ONE virtuemart installation.
 * You are not allowed to modify this code. *
 */
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');

/**
 * @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
 * @license	GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a multiple item select element
 *
 */

class JElementShoppergroup extends JElement {

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */

    var $_name = 'shoppergroup';

    function fetchElement($name, $value, &$node, $control_name) {

		$shoppergroup_model = VmModel::getModel('shoppergroup');
		$shoppergroup_list = $shoppergroup_model->getShopperGroups(true,true);
		array_unshift($shoppergroup_list,vmText::_('VMUSERFIELD_ISTRAXX_EUVATCHECKER_GROUP_NO_CHANGE') );
		return JHTML::_('select.genericlist', $shoppergroup_list,'' . $control_name . '[' . $name . ']', '','virtuemart_shoppergroup_id', 'shopper_group_name', $value, $control_name . $name);//$model->_params->get('virtuemart_shoppergroup_id'));

    }

}
