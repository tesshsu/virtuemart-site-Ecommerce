<?php
/**
 *
 * @author SM planet - smplanet.net
 * @package Catproduct
 * @copyright Copyright (C) 2012-2016 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 **/
defined('_JEXEC') or die();

$colspan = 0;
?>
<table style="width:100%;" class="catproducttable">
<thead>
<tr>
	<?php 

	if ($parametri["show_image"] == 1) { echo $currency->showTableField('th','cell_image',JText::_('CATPRODUCT_TABLE_IMAGE_FIELD'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_id"] == 1) { echo $currency->showTableField('th','cell_id',JText::_('CATPRODUCT_TABLE_ID_FIELD'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_sku"] == 1) { echo $currency->showTableField('th','cell_sku',JText::_('CATPRODUCT_TABLE_SKU_FIELD'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_name"] == 1) { echo $currency->showTableField('th','cell_name',JText::_('CATPRODUCT_TABLE_NAME_FIELD'),'scope="col"'); $colspan += 1; }

	// here for non-cart variant customfield (string etc.) $parent_product['child']['customfieldsSorted']['normal']
	if (isset($group['params']['nvcustomfield']) && count($group['params']['nvcustomfield']) > 0) {
		foreach ($group['params']['nvcustomfield'] as $nv_customfield) {
			echo '<th class="cell_customfields non-varinat" scope="col">';
			echo $nv_customfield['custom_title'];
			echo '</th>';
			$colspan += 1;
		}
	}
	
	if ($use_customfields == 1) { echo $currency->showTableField('th','cell_customfields',JText::_('CATPRODUCT_TABLE_CUSTOMFIELDS'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_s_desc"] == 1) { echo $currency->showTableField('th','cell_s_desc',JText::_('CATPRODUCT_TABLE_DESC_FIELD'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_weight"] == 1) { echo $currency->showTableField('th','cell_product_weight',JText::_('CATPRODUCT_TABLE_WEIGHT'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_sizes"] == 1) { echo $currency->showTableField('th','cell_size',JText::_('CATPRODUCT_TABLE_SIZES'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_stock"] == 1) { echo $currency->showTableField('th','cell_stock',JText::_('CATPRODUCT_TABLE_STOCK'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_min_qty"] == 1) { echo $currency->showTableField('th','cell_stock',JText::_('CATPRODUCT_TABLE_MIN_QTY'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_max_qty"] == 1) { echo $currency->showTableField('th','cell_stock',JText::_('CATPRODUCT_TABLE_MAX_QTY'),'scope="col"'); $colspan += 1; }
	if ($parametri["show_step_qty"] == 1) { echo $currency->showTableField('th','cell_stock',JText::_('CATPRODUCT_TABLE_STEP_QTY'),'scope="col"'); $colspan += 1; }
	
	if ($parametri["show_basePrice"] == 1) { echo $currency->showTableField('th','cell_basePrice',JText::_('CATPRODUCT_TABLE_BASEPRICE'),'scope="col" data-type="currency"'); $colspan += 1; }
	if ($parametri["show_basePriceWithTax"] == 1) { echo $currency->showTableField('th','cell_basePriceWithTax',JText::_('CATPRODUCT_TABLE_BASEPRICEWITHTAX'),'scope="col" data-type="currency"'); $colspan += 1; }
	if ($parametri["show_priceWithoutTax"] == 1) { echo $currency->showTableField('th','cell_priceWithoutTax',JText::_('CATPRODUCT_TABLE_PRICEWITHOUTTAX'),'scope="col" data-type="currency"'); $colspan += 1; }
	if ($parametri["show_salesPrice"] == 1) { echo $currency->showTableField('th','cell_salesPrice',JText::_('CATPRODUCT_TABLE_SALESPRICE'),'scope="col" data-type="currency"'); $colspan += 1; }
	if ($parametri["show_taxAmount"] == 1) { echo $currency->showTableField('th','cell_taxAmount',JText::_('CATPRODUCT_TABLE_TAXAMOUNT'),'scope="col" data-type="currency"'); $colspan += 1; }
	if ($parametri["show_discountAmount"] == 1) { echo $currency->showTableField('th','cell_discountAmount',JText::_('CATPRODUCT_TABLE_DISCOUNTAMOUNT'),'scope="col" data-type="currency"'); $colspan += 1; }
	
	// now Quantity
	if (!VmConfig::get('use_as_catalog', 0)  ) { 
		if ($see_price) {
			echo $currency->showTableField('th','cell_quantity',JText::_('CATPRODUCT_TABLE_QUANTITY'),'scope="col"'); $colspan += 1; 
		} else {
			echo $currency->showTableField('th','cell_quantity',JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE'),'scope="col"'); $colspan += 1; 
		}
	}
	
	if ($parametri["show_sum_weight"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_product_weight',JText::_('CATPRODUCT_TABLE_SUM_WEIGHT'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_basePrice"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_basePrice',JText::_('CATPRODUCT_TABLE_SUM_BASEPRICE'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_basePriceWithTax"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_basePriceWithTax',JText::_('CATPRODUCT_TABLE_SUM_BASEPRICEWITHTAX'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_taxAmount"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_taxAmount',JText::_('CATPRODUCT_TABLE_SUM_TAXAMOUNT'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_discountAmount"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_discountAmount',JText::_('CATPRODUCT_TABLE_SUM_DISCOUNTAMOUNT'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_priceWithoutTax"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_priceWithoutTax',JText::_('CATPRODUCT_TABLE_SUM_PRICEWITHOUTTAX'),'scope="col" data-type="currency"'); $colspan += 1; 
	}
	if ($parametri["show_sum_salesPrice"] == 1 && $see_price) { 
		echo $currency->showTableField('th','cell_sum_salesPrice',JText::_('CATPRODUCT_TABLE_SUM_SALESPRICE'),'scope="col" data-type="currency"'); $colspan += 1; 
	}	
	if (!VmConfig::get('use_as_catalog', 0) && $see_price) {
		if (isset($parametri["addtocart_button"]) && $parametri["addtocart_button"] == "foreach"  && $see_price) {
			echo $currency->showTableField('th','cell_addtocart','','scope="col"'); $colspan += 1; 
		}
	}
 ?>
 </tr>
</thead>
<tbody>
<?php

foreach($group AS $product){

	if (isset($product['group_title']) && $product['group_title']) {
		$group_id++;
		if (!isset($hide_caption) || !$hide_caption) {
			echo '<caption>'.$product['group_title'].'</caption>';
		}
	} else {
/*	$price_per_unit = 'NA';
	if (isset($product['child']['product_packaging']) && $product['child']['product_packaging'] <> 0 ){
		$product_unit = $product['child']['product_unit'];
		switch ($product_unit) {
			case 'KG':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2).' / '.$product_unit ;
				break;
			case 'L':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
				break;
			case 'M':
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
				break;
			default:	
				$price_per_unit = round(((float) $product['prices']['salesPrice'] / (float) $product['child']['product_packaging']),2) ;
		}
	}
	print_r($price_per_unit);
	  //$product['child']['product_packaging'];
	  print_r($currency->_priceConfig);
	  */
	  	// min max box quantity
		$min_order_level = '0';
		$max_order_level = '0';
		$product_box = '0';
		if (isset($product['qparam'])) {
			$min_order_level = $product['qparam']['min'];
			$max_order_level = $product['qparam']['max'];
			$product_box = $product['qparam']['box'];
		}
	  //if attached product title
	  if (empty($product['child']['catproduct_inline_row'])) {		
		echo '<tr class="row_article">';
		// start with showing product
		if ($check_stock == 1 && $product['child']['product_in_stock'] > 0 || $check_stock == 0){
		// hidden input with whole product data
		$catproduct_product_datas = $currency->getCatproductProductData($product, $see_price, $def_group_qty ,$group_id);

		}
		
		// show table with product
		//Product_image
		if ($parametri["show_image"] == 1) {
			if (isset($product['images'])) {
				$firsttime = true;
				echo '<td class="cell_image">';
				foreach ($product['images'] as $image) {
					if ($firsttime) {
						echo $image->displayMediaThumb('class="product-image"', true, 'rel="catproduct_images_'.$i.'"', true, false);
						$firsttime = false;
					} else {
						echo '<a title="'.$image->file_name.'" rel="catproduct_images_'.$i.'" href="'.$image->file_url.'"></a>';
					}
				}
				echo '</td>';
			}
			else {
				echo '<td class="cell_image"></td>';
			}
		}
		if(VmConfig::get('usefancy',0)){
			$image_script .= 'jQuery(document).ready(function() {
			jQuery("a[rel=catproduct_images_'.$i.']").fancybox({
			"titlePosition" 	: "inside",
			"transitionIn"	:	"elastic",
			"transitionOut"	:	"elastic"
			});});';
		} else {
			$image_script .= 'jQuery(document).ready(function() {
			jQuery("a[rel=catproduct_images_'.$i.']").facebox();});';
		}

		//Product_ID
		if ($parametri["show_id"] == 1) { echo $currency->showTableField('td','cell_id',$product['child']['virtuemart_product_id'], 'data-title="'.JText::_('CATPRODUCT_TABLE_ID_FIELD').'"');}
		//Product_SKU
		if ($parametri["show_sku"] == 1) { echo $currency->showTableField('td','cell_sku',$product['child']['product_sku'], 'data-title="'.JText::_('CATPRODUCT_TABLE_SKU_FIELD').'"');}
		// Product title
		if ($parametri["show_name"] == 1) { echo $currency->showTableField('td','cell_name',$product['child']['product_name'], 'data-title="'.JText::_('CATPRODUCT_TABLE_NAME_FIELD').'"');}
		
		// here for non-cart variant customfield (string etc.) $parent_product['child']['customfieldsSorted']['normal']
		if (isset($group['params']['nvcustomfield']) && count($group['params']['nvcustomfield'])) {
			foreach ($group['params']['nvcustomfield'] as $nv_customfield) {
				$customfield = '';
				echo '<td class="cell_customfields non-variant" data-title="'.$nv_customfield['custom_title'].'">';
				if (isset($product['child']['customfieldsSorted']['normal'])) {
					foreach ($product['child']['customfieldsSorted']['normal'] as $product_customfield) {
						if ($nv_customfield['custom_title'] == $product_customfield->custom_title && $nv_customfield['field_type'] == $product_customfield->field_type) {
							$customfield .= $product_customfield->customfield_value;
						}
					}
				}
				if ($customfield != '') echo $customfield; else echo '-';
				echo '</td>';
			}
		}

		// check for customfield
		if ($use_customfields == 1) {
			$customfield = '';
			if (isset( $product['child']['customfieldsCart'][0])) {
				foreach($product['child']['customfieldsCart'] as $customfields) {
					$test = (array) $customfields;
					$customfield .= ('<span class="product-field-display"><strong>'.$test['custom_title'].'</strong><br/>'.$test['display'].'</span>');
				}
			}
			if (isset( $product['child']['customfieldsSorted']['addtocart'][0])) {
				foreach($product['child']['customfieldsSorted']['addtocart'] as $customfields) {
					$test = (array) $customfields;
					$customfield .= ('<span class="product-field-display"><strong>'.$test['custom_title'].'</strong><br/>'.$test['display'].'</span>');
				}
			}
			
			echo '<td class="cell_customfields" ';
			echo $customfield != '' ? 'data-title="'.JText::_('CATPRODUCT_TABLE_CUSTOMFIELDS').'"' : '';
			echo '>'.$customfield;
			echo '</td>';
		}
		
		// Product description
		if ($parametri["show_s_desc"] == 1) { echo $currency->showTableField('td','cell_s_desc',$product['child']['product_s_desc'], 'data-title="'.JText::_('CATPRODUCT_TABLE_DESC_FIELD').'"');}

		// Product weight
		if ($parametri["show_weight"] == 1) { 
			echo $currency->showTableField('td','cell_product_weight',$product['child']['product_weight'] != 0?round($product['child']['product_weight'],2).' '.$product['child']['product_weight_uom']:'', 'data-title="'.JText::_('CATPRODUCT_TABLE_WEIGHT').'"');
		}

		// Product sizes
		if ($parametri["show_sizes"] == 1) {
			$sizes = '';
			if ($product['child']['product_length'] <> 0) $sizes .= round($product['child']['product_length'],2);
			if ($product['child']['product_width'] <> 0) {
				if ($sizes <> '') {
					$sizes .= ' x ';
				}
				$sizes .= round($product['child']['product_width'],2);
			}
			if ($product['child']['product_height'] <> 0) {
				if ($sizes <> '') {
					$sizes .= ' x ';
				}
				$sizes .= round($product['child']['product_height'],2);
			}
			if ($sizes <> '') {
				$sizes .= ' '.$product['child']['product_lwh_uom'];
			}
			echo $currency->showTableField('td','cell_size',$sizes, 'data-title="'.JText::_('CATPRODUCT_TABLE_SIZES').'"');
		}
		// Product stock
		if ($parametri["show_stock"] == 1) { echo $currency->showTableField('td','cell_stock',$product['child']['product_in_stock'], 'data-title="'.JText::_('CATPRODUCT_TABLE_STOCK').'"');}

		if ($parametri["show_min_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["min"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo $currency->showTableField('td','cell_stock',$minqty, 'data-title="'.JText::_('CATPRODUCT_TABLE_MIN_QTY').'"');
		}
		if ($parametri["show_max_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["max"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo $currency->showTableField('td','cell_stock',$minqty, 'data-title="'.JText::_('CATPRODUCT_TABLE_MAX_QTY').'"');
		}
		if ($parametri["show_step_qty"] == 1) { 
			$minqty = str_replace('"', "", $product["qparam"]["box"]);
			if ($minqty == '' || $minqty == 'null') $minqty = JText::_('CATPRODUCT_QTY_NA');
			echo $currency->showTableField('td','cell_stock',$minqty, 'data-title="'.JText::_('CATPRODUCT_TABLE_STEP_QTY').'"');
		}
		
		// Product base price without tax
		if ($parametri["show_basePrice"] == 1 && $see_price) { echo $currency->showTableField('td','cell_basePrice','<div class="basePrice_text">'.$currency->createPriceDiv ('basePrice', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_BASEPRICE').'" data-type="currency"');}
		// Product base price with tax
		if ($parametri["show_basePriceWithTax"] == 1 && $see_price) { echo $currency->showTableField('td','cell_basePriceWithTax','<div class="basePriceWithTax_text">'.$currency->createPriceDiv ('basePriceWithTax', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_BASEPRICEWITHTAX').'" data-type="currency"');}
		// Product final price without tax
		if ($parametri["show_priceWithoutTax"] == 1 && $see_price) { echo $currency->showTableField('td','cell_priceWithoutTax','<div class="priceWithoutTax_text">'.$currency->createPriceDiv ('priceWithoutTax', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_PRICEWITHOUTTAX').'" data-type="currency"');}
		// Product final price with tax
		if ($parametri["show_salesPrice"] == 1 && $see_price) { echo $currency->showTableField('td','cell_salesPrice','<div class="salesPrice_text">'.$currency->createPriceDiv ('salesPrice', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_SALESPRICE').'" data-type="currency"');}
		// Tax amount
		if ($parametri["show_taxAmount"] == 1 && $see_price) { echo $currency->showTableField('td','cell_taxAmount','<div class="taxAmount_text">'.$currency->createPriceDiv ('taxAmount', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_TAXAMOUNT').'" data-type="currency"');}
		// Discount amount
		if ($parametri["show_discountAmount"] == 1 && $see_price) { echo $currency->showTableField('td','cell_discountAmount','<div class="discountAmount_text">'.$currency->createPriceDiv ('discountAmount', '', $product['prices']).'</div>', 'data-title="'.JText::_('CATPRODUCT_TABLE_DISCOUNTAMOUNT').'" data-type="currency"');}

		// if stock checking is enabled
		if ($check_stock == 1 && $product['child']['product_in_stock'] > 0 || $check_stock == 0){
			//Quantity
			if (!VmConfig::get('use_as_catalog', 0)  ) {
				if ($see_price) {
					echo '<td class="cell_quantity" data-title="'.JText::_('CATPRODUCT_TABLE_QUANTITY').'">
					<span class="quantity-box"><input class="quantity-input" size="2" name="quantity[]" value="0" type="text" ></span>
					<span class="quantity-controls"><input class="quantity-controls quantity-plus" type="button">
					<input class="quantity-controls quantity-minus" type="button"></span>
					</td>';
				} else {
					echo '<td class="cell_quantity" data-title="'.JText::_('CATPRODUCT_TABLE_QUANTITY').'"><input type="hidden" name="quantity[]" value="0">';
					if (isset($product['child']['askquestion_url'])) {
						echo '<a class="ask-a-question bold" href="'.$product['child']['askquestion_url'].'" rel="nofollow" >'.JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE').'</a>';
					}
					echo '</td>';
				}
			}
			// sum weight
			if ($parametri["show_sum_weight"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_product_weight','<span>0.00 '.$product['child']['product_weight_uom'].'</span>', 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_WEIGHT').'" data-type="currency"');}
			// sum base price without tax
			if ($parametri["show_sum_basePrice"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_basePrice',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_BASEPRICE').'" data-type="currency"');}
			// sum base price with tax
			if ($parametri["show_sum_basePriceWithTax"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_basePriceWithTax',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_BASEPRICEWITHTAX').'" data-type="currency"');}
			// sum tax
			if ($parametri["show_sum_taxAmount"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_taxAmount',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_TAXAMOUNT').'" data-type="currency"');}
			// sum discount
			if ($parametri["show_sum_discountAmount"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_discountAmount',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_DISCOUNTAMOUNT').'" data-type="currency"');}
			// sum final price without tax
			if ($parametri["show_sum_priceWithoutTax"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_priceWithoutTax',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_PRICEWITHOUTTAX').'" data-type="currency"');}
			// sum final price with tax
			if ($parametri["show_sum_salesPrice"] == 1 && $see_price) { echo $currency->showTableField('td','cell_sum_salesPrice',$currency->createPriceDiv ('', '', '0.00'), 'data-title="'.JText::_('CATPRODUCT_TABLE_SUM_SALESPRICE').'" data-type="currency"');}
			
			echo $catproduct_product_datas;
			$i++;
		}	
		else {
			if ($stockhandle == 'disableadd') {// if notify button
				echo '<td align="middle" colspan="3">';
				echo '<a href="'.JURI::base().'index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product['child']['virtuemart_product_id'].'" class="notify" iswrapped="1">'.JText::_('CATPRODUCT_NOTIFYME').'</a>';
				echo '</td>';
			}
			else {// if no stock
				echo '<td align="middle" colspan="3"><span style="color:red;">'.JText::_('CATPRODUCT_OUTOFSTOCK').'</span></td>';
			}
		}
		
		// show row addtocartbutton
		echo $see_price?$currency->showAddtocartButton('foreach', $parametri, $colspan, $check_stock, $product['child']['product_in_stock'], 0, 1, $classes):'';

		echo '</tr>';
	  }
	  // if title for attached products
	  else {
		echo '<tr class="row_attached_product">';
		echo '<td colspan="'.$colspan.'" style="text-align:center;">'.$product['child']['catproduct_inline_row'].'</td>';
		echo '</tr>';
	  }
	}
}
 ?>
</tbody>
</table>