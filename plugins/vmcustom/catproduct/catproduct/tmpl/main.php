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
	$classes = array (
				0 => "addtocart-button", // span class
				1 => "addtocart-button", // button class
				2 => "", // cell class
				3 => "addtocart-button-disabled" // disabled
			);	
	$stockhandle = VmConfig::get('stockhandle','none');
	$check_stock = 0;
	$global_params = $viewData[3];
	$see_price = true;
	if (isset($global_params["show_prices"])) $see_price = $global_params["show_prices"];
	
	if ($global_params['use_default'] == 1) {
		$parametri = $global_params;
	}
	else {
		if (isset($viewData[2]->custom_param)) {
			$parametri = json_decode($viewData[2]->custom_param, true);
		} else {
			$parametri = (array) $viewData[2];
		}
	}
	
	if ($stockhandle == 'disableadd' || $stockhandle == 'disableit_children' || $stockhandle == 'disableit') { $check_stock = 1; }
	$colspan = 0;

	if (!class_exists ('CatproductFunction')) { require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct' . DS . 'helpers' . DS . 'catproductfunctions.php'); }
	$currency = new CatproductFunction();
	
	// for customfields support
	if (isset($viewData[2]->add_plugin_support)) {
		$use_customfields = $viewData[2]->add_plugin_support;
	} else if(isset($viewData[2]->custom_param)) {
		$use_customfields = json_decode($viewData[2]->custom_param, true);
		if (isset($use_customfields["add_plugin_support"])) {
			$use_customfields = $use_customfields["add_plugin_support"];
		} else {
		$use_customfields = 0;
		}
	} else {
		$use_customfields = 0;
	}
	$image_script  = '';
	
	$parent_product = $viewData[5];

	?>
	
<style type="text/css">	
	.product-fields .product-field input { 
left: 0px !important;
}
</style>
	<form  ax:nowrap="1" action="index.php" method="post" name="catproduct_form" class="catproduct_form">
<?php $currency_data = $currency->getCurrencyCP(); ?>
<?php
// add main catproduct data
echo $currency->getCatproductMainData($parametri, $parent_product);

?>

	<table style="width:100%;" class="catproducttable">
<caption><?php echo JText::_('CATPRODUCT_TABLE_TITLE') ?></caption>
<tbody>
<tr><td colspan="5">
<?php
$i = 0;
$group_id = 0;
foreach ($viewData[0] as $group) { 
	if (isset($group['params']['layout']) && $group['params']['layout'] <> '') $layout = $group['params']['layout'];
	else $layout = 'default.php';
	if (isset($group['params']['def_qty']) && $group['params']['def_qty'] <> '') $def_group_qty = $group['params']['def_qty'];
	else $def_group_qty = '1';
	if (isset($group['params']['layout']) && $group['params']['show_qty'] <> '') $show_qty = $group['params']['show_qty'];
	else $show_qty = '1';
	
	ob_start();
	include (JPATH_ROOT.DS.'plugins'.DS.'vmcustom'.DS.'catproduct'.DS.'catproduct'.DS.'tmpl'.DS.'group_layouts'.DS.$layout);
	$output =	ob_get_clean();
	echo $output;
}
echo '</td></tr>';
$colspan = 5;

	// total weight
	if ($parametri["show_total_weight"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'product_weight', JText::_('CATPRODUCT_TABLE_TOTAL_WEIGHT'),'0.00 '.$product['child']['product_weight_uom'], $colspan);
	}
	// total base price without tax
	if ($parametri["show_total_basePrice"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'basePrice', JText::_('CATPRODUCT_TABLE_TOTAL_BASEPRICE'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// total base price with tax
	if ($parametri["show_total_basePriceWithTax"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'basePriceWithTax', JText::_('CATPRODUCT_TABLE_TOTAL_BASEPRICEWITHTAX'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// total tax amount
	if ($parametri["show_total_taxAmount"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'taxAmount', JText::_('CATPRODUCT_TABLE_TOTAL_TAXAMOUNT'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// total discount amount
	if ($parametri["show_total_discountAmount"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'discountAmount', JText::_('CATPRODUCT_TABLE_TOTAL_DISCOUNTAMOUNT'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// total final price without tax
	if ($parametri["show_total_priceWithoutTax"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'priceWithoutTax', JText::_('CATPRODUCT_TABLE_TOTAL_PRICEWITHOUTTAX'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// total final price with tax
	if ($parametri["show_total_salesPrice"] == 1 && $see_price) {
		echo $currency->showTotalRow ( 'salesPrice', JText::_('CATPRODUCT_TABLE_TOTAL_SALESPRICE'),$currency->createPriceDiv ('', '', '0.00'), $colspan);
	}
	// show addtocartbutton
	echo $see_price?$currency->showAddtocartButton('forall', $parametri, $colspan, 0, 1, $classes):'';
?>
	</tbody>
	</table>
  <input name="option" value="com_virtuemart" type="hidden">
  <input name="view" value="cart" type="hidden">
  <input name="task" value="addJS" type="hidden">	
  <input name="format" value="json" type="hidden">	
</form>

<div class="catproduct-loading">
    <img src="<?php echo JURI::root(true) ?>/plugins/vmcustom/catproduct/catproduct/css/ajax-loader.gif" />
 </div>
<?php
	$document = JFactory::getDocument();
	$document->addScriptDeclaration($image_script);
	// preventing 2 x load javascript
	if (JFactory::getApplication()->get('catproduct_js_css') !== true) {
		$document->addScript(JURI::root(true). "/plugins/vmcustom/catproduct/catproduct/js/cp_javascript.js");
		$document->addStyleSheet(JURI::root(true). "/plugins/vmcustom/catproduct/catproduct/css/catproduct.css");
		$document->addStyleSheet(JURI::root(true). "/plugins/vmcustom/catproduct/catproduct/css/res-table.css");
		JFactory::getApplication()->set('catproduct_js_css', true);
	}
 ?>