<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
/**
 *
 * @author SM planet - smplanet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2014 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 **/

class CatproductAdmin {

	static function getVars () {
		$varsToPush = array(	'use_default'=>array(0.0,'bool'),	
								'use_default2'=>array(0.0,'bool'),	
								'sorting_field'=>array('default','string'),
								'layout_field'=>array('default.php','string'),
								'show_image'=>array(0,'bool'),
								'show_id'=>array(0.0,'bool'),
						    	'show_sku'=>array(0.0,'bool'),
						    	'show_name'=>array(0.0,'bool'),
								'show_s_desc'=>array(0.0,'bool'),
								'show_weight'=>array(0.0,'bool'),
								'show_sizes'=>array(0.0,'bool'),
								'show_stock'=>array(0.0,'bool'),
								'show_min_qty'=>array(0.0,'bool'),
								'show_max_qty'=>array(0.0,'bool'),
								'show_step_qty'=>array(0.0,'bool'),
								'show_basePrice'=>array(0.0,'bool'),
								'show_basePriceWithTax'=>array(0.0,'bool'),
								'show_salesPrice'=>array(0.0,'bool'),
								'show_taxAmount'=>array(0.0,'bool'),
								'show_priceWithoutTax'=>array(0.0,'bool'),
								'show_discountAmount'=>array(0.0,'bool'),
								'show_sum_weight'=>array(0.0,'bool'),
								'show_sum_basePrice'=>array(0.0,'bool'),
								'show_sum_basePriceWithTax'=>array(0.0,'bool'),
								'show_sum_salesPrice'=>array(0.0,'bool'),
								'show_sum_taxAmount'=>array(0.0,'bool'),
								'show_sum_priceWithoutTax'=>array(0.0,'bool'),
								'show_sum_discountAmount'=>array(0.0,'bool'),
								'show_total_weight'=>array(0.0,'bool'),
								'show_total_basePrice'=>array(0.0,'bool'),
								'show_total_basePriceWithTax'=>array(0.0,'bool'),
								'show_total_salesPrice'=>array(0.0,'bool'),
								'show_total_taxAmount'=>array(0.0,'bool'),
								'show_total_priceWithoutTax'=>array(0.0,'bool'),
								'show_total_discountAmount'=>array(0.0,'bool'),
								'enable_attached_products_array'=>array('','array'),
								'enable_title_for_attached_array'=>array('','array'),
								'title_for_attached_products_array'=>array('', 'array'),
								'id_sku_for_attached_products_array'=>array('','array'),
								'attached_products_array'=>array('','array'),
								'attached_products_layout_array'=>array('','array'),
								'attached_products_def_qty_array'=>array('','array'),
								'attached_products_show_qty_array'=>array('','array'),
								'use_max_min_quantity'=>array(0,'bool'),
								'use_box_quantity'=>array(0,'bool'),
								'update_prices'=>array(0,'bool'),
								'attached_array'=>array(0,'bool'),
								'add_parent_to_table'=>array(0,'bool'),
								'add_parent_from_original'=>array(0,'bool'),
								'do_not_show_child'=>array(0,'bool'),
								'add_plugin_support'=>array(0,'integer'),
								'enable_cs'=>array(0,'bool'),
								'c_string_1'=>array('','string'),
								'c_string_2'=>array('','string'),
								'c_string_3'=>array('','string'),
								'c_string_4'=>array('','string'),
								'c_string_5'=>array('','string'),
								'hide_original_addtocart'=>array('css','string'),
								'original_addtocart_css'=>array('.addtocart-area','string'),
								'orig_add_area'=>array('.productdetails-view .addtocart-area','string'),
								'override'=>array(0,'bool'),
								'layout_field_children'=>array('default.php','string'),
								'layout_field_parent'=>array('default.php','string'),
								'def_qty_children'=>array('0','string'),
								'def_qty_parent'=>array('0','string'),
								'show_qty_children'=>array('0','string'),
								'show_qty_parent'=>array('0','string'),
								'addtocart_button'=>array('forall','string')
		);
		return $varsToPush;
	}
}