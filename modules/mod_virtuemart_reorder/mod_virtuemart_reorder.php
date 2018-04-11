<?php
// no direct access
defined('_JEXEC') or die;
/**
 * @package	Joomla.Site
 * @subpackage	mod_virtuemart_reorder
 * @copyright	Copyright (C) EasyJoomla.org. All rights reserved.
 * @author      Jan Linhart
 * @license	GNU General Public License version 2 or later
 */


if (!class_exists( 'mod_virtuemart_reorder' )) require('helper.php');

$orderNumber = JRequest::getVar('order_number');

if($orderNumber)
{
    $helper = new mod_virtuemart_reorder();
    $order  = $helper->getOrderProducts($orderNumber);
    $products = $order['items'];

    
    // echo "<pre>";var_dump($products);echo "</pre>";

    foreach($products as $pkey => $product)
    {
		$attributes = (array)json_decode($product->product_attribute);

		if(is_array($attributes) && count($attributes) > 0)
		{
			$products[$pkey]->attributes = array();

			foreach($attributes as $key => $attr)
			{
				$attr = explode('</span>', $attr);
				$attribute = new stdClass();
				$attribute->id = $key;
				$attribute->name = trim(strip_tags($attr[0]));
				$attribute->value = trim(strip_tags($attr[1]));
				$attribute->custom_id = $helper->getCustomIdByTitle($attribute->name);
				$products[$pkey]->attributes[] = $attribute;
			}
		}
	}

    $pretext = $params->get('pretext', '');
    $buttontext = $params->get('buttontext', '');
    $moduleclass_sfx = $params->get('moduleclass_sfx', '');
    require(JModuleHelper::getLayoutPath('mod_virtuemart_reorder'));
}
