<?php
/**
*
* Order items view
*
* @package	VirtueMart
* @subpackage Orders
* @author Max Milbers, Valerie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: details_items.php 5432 2012-02-14 02:20:35Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//FIX MAIL BRUNO CAMELEONS
if ($this->orderDetails['details']['BT']->order_language == 'fr-FR') {
    $isEnglish = '';
} else {
    $isEnglish = 'EN_';
}

//FLORIAN CAMELEONS : vardump à com/decom
//var_dump($this->orderDetails['details']['BT']);
//var_dump($this->orderDetails['items']);

$colspan=8;

if ($this->doctype != 'invoice') {
    $colspan -= 4;
} elseif ( ! VmConfig::get('show_tax')) {
    $colspan -= 1;
}

$handled = array();
$discountsBill = false;
$taxBill = false;
$vats = 0;
foreach($this->orderDetails['calc_rules'] as $rule){
    if(isset($sumRules[$rule->virtuemart_calc_id])){	// or $rule->calc_kind=='payment' or $rule->calc_kind=='shipment'){
        continue;
    }
    $handled[$rule->virtuemart_calc_id] = true;
    $r = new stdClass();
    $r->calc_result = $rule->calc_result;
    $r->calc_amount = $rule->calc_amount;
    $r->calc_rule_name = $rule->calc_rule_name;
    $r->calc_kind = $rule->calc_kind;
    $r->calc_value = $rule->calc_value;

    if($rule->calc_kind == 'DBTaxRulesBill' or $rule->calc_kind == 'DATaxRulesBill'){
        $discountsBill[$rule->virtuemart_calc_id] = $r;
    }
    if($rule->calc_kind == 'taxRulesBill' or $rule->calc_kind == 'VatTax' or $rule->calc_kind=='payment' or $rule->calc_kind=='shipment'){
        //vmdebug('method rule',$rule);
        $r->label = shopFunctionsF::getTaxNameWithValue($rule->calc_rule_name,$rule->calc_value);
        if(isset($taxBill[$rule->virtuemart_calc_id])){
            $taxBill[$rule->virtuemart_calc_id]->calc_amount += $r->calc_amount;
        } else {
            $taxBill[$rule->virtuemart_calc_id] = $r;
        }

    }

}


/*if(($item->product_subtotal_discount==0 || $item->product_subtotal_discount==null) && ($this->orderDetails['details']['BT']->order_payment==0 || $this->orderDetails['details']['BT']->order_payment==null) && ($this->orderDetails['details']['BT']->order_discount==0 || $this->orderDetails['details']['BT']->order_discount==null) ){
    $colDiscount=0;
} else {
    $colDiscount=1;
}*/
//FLORIAN CAMELEONS : pour chaque produit on regarde s'il a une remise, s'il en a une, la colonne discount s'affiche
$colDiscount=0;
foreach($this->orderDetails['items'] as $item) {
    if(($item->product_subtotal_discount==0 || $item->product_subtotal_discount==null)){
        //$colDiscount=0;
    } else {
        $colDiscount=1;
    }
}
if($this->orderDetails['details']['BT']->order_billTaxAmount>0){
    $withTVA=true;
}else{
    $withTVA=false;
}

//FLORIAN CAMELEONS : ajout de la date
$time = strtotime($this->orderDetails['details']['BT']->created_on);
$newformat = date('d-m-Y',$time);
echo(vmText::_('COM_VIRTUEMART_'.$isEnglish.'MAIL_DATE_ORDER' )." ".$newformat);


//FLORIAN CAMELEONS : récupérer le nom de la méthode d'expédition
$config = new JConfig();
$db_product = new PDO('mysql:host=localhost;dbname='.$config->db, $config->user, $config->password);

if($isEnglish){
    $product_request = $db_product->query('SELECT shipment_desc 
        FROM h8q2p_virtuemart_shipmentmethods_en_gb
        WHERE virtuemart_shipmentmethod_id = '.$this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id);
    while($product_data = $product_request->fetch()) {
        $shipment_to_display = $product_data[shipment_desc];
    }
    $shipment_string = vmText::_('COM_VIRTUEMART_'.$isEnglish.'CART_SHIPPING');
    if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id!=873){
        $shipment_string = $shipment_to_display;
    }
} else {
    $product_request = $db_product->query('SELECT shipment_desc 
        FROM h8q2p_virtuemart_shipmentmethods_fr_fr
        WHERE virtuemart_shipmentmethod_id = '.$this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id);
    while($product_data = $product_request->fetch()) {
        $shipment_to_display = $product_data[shipment_desc];
    }
    $shipment_string = vmText::_('COM_VIRTUEMART_'.$isEnglish.'CART_SHIPPING');
    if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id!=873){
        $shipment_string = $shipment_to_display;
    }
}

?>
<table class="html-email" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr align="left" class="sectiontableheader">
        <td align="left" width="5%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_SKU') ?></strong></td>
        <td align="left" colspan="2" width="38%" ><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'PRODUCT_NAME_TITLE') ?></strong></td>
        <td align="center" width="10%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_PRODUCT_STATUS') ?></strong></td>
        <?php if ($this->doctype == 'invoice') { ?>
        <?php if ($withTVA){ ?>
        <td align="right" width="10%" ><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_PRICE_VAT') ?></strong></td>
        <?php } else { ?>
        <td align="right" width="10%" ><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_PRICE') ?></strong></td>
        <?php } ?>
        <?php } ?>
        <td align="right" width="6%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_QTY') ?></strong></td>
        <?php if ($this->doctype == 'invoice') { ?>
        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right" width="10%" ><strong><?php
    if(is_array($taxBill) and count($taxBill)==1){
        reset($taxBill);
        $t = current($taxBill);
        echo shopFunctionsF::getTaxNameWithValue($t->calc_rule_name,$t->calc_value);
    } else {
        echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_PRODUCT_TAX');
    }


            ?></strong></td>
        <?php } ?>
        <!-- FLORIAN CAMELEONS ajout du if qui supprime le head de la colonne -->
        <?php //print_r($this->orderDetails['details']['BT']); ?>
        <?php if($colDiscount!=0): ?>
        <td align="right" width="11%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_SUBTOTAL_DISCOUNT_AMOUNT') ?></strong></td>
        <td align="right" width="11%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_TOTAL') ?></strong></td>
        <?php endif; ?>

        <?php if($colDiscount==0): ?>
        <td align="right" width="11%"><strong><?php echo vmText::_(' ') ?></strong></td>
        <td align="right" width="11%"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_TOTAL') ?></strong></td>
        <?php endif; ?>

        <?php } ?>
    </tr>

    <?php
    $menuItemID = shopFunctionsF::getMenuItemId($this->orderDetails['details']['BT']->order_language);
    if(!class_exists('VirtueMartModelCustomfields'))require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');
    VirtueMartModelCustomfields::$useAbsUrls = ($this->isMail or $this->isPdf);
    foreach($this->orderDetails['items'] as $item) {
        $qtt = $item->product_quantity ;
        $product_link = JURI::root().'index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $item->virtuemart_category_id .
            '&virtuemart_product_id=' . $item->virtuemart_product_id . '&Itemid=' . $menuItemID;

    ?>
    <tr valign="top">
        <td align="left">
            <?php echo $item->order_item_sku; ?>
        </td>
        <td align="left" colspan="2" >
            <div float="right" ><a href="<?php echo $product_link; ?>"><?php echo $item->order_item_name; ?></a></div>
            <?php
        $product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'FE');
        echo $product_attribute;
            ?>
        </td>
        <td align="center">
            <?php echo $this->orderstatuses[$item->order_status]; ?>
        </td>
        <?php if ($this->doctype == 'invoice') { ?>
        <td align="right"   class="priceCol" >
            <?php
                $item->product_discountedPriceWithoutTax = (float) $item->product_discountedPriceWithoutTax;
                if (!empty($item->product_priceWithoutTax) && $item->product_discountedPriceWithoutTax != $item->product_priceWithoutTax) {
                    if($withTVA){
                        echo '<span class="line-through">'.$this->currency->priceDisplay($item->product_basePriceWithTax, $this->user_currency_id) .'</span><br />';
                        echo '<span >'.$this->currency->priceDisplay($item->product_discountedPriceWithoutTax, $this->user_currency_id) .'</span><br />';
                    } else {
                        echo '<span class="line-through">'.$this->currency->priceDisplay($item->product_item_price, $this->user_currency_id) .'</span><br />';
                        echo '<span >'.$this->currency->priceDisplay($item->product_discountedPriceWithoutTax, $this->user_currency_id) .'</span><br />';
                    }
                } else {
                    if($withTVA){
                        echo '<span >'.$this->currency->priceDisplay($item->product_basePriceWithTax, $this->user_currency_id) .'</span><br />';
                    } else {
                        echo '<span >'.$this->currency->priceDisplay($item->product_item_price, $this->user_currency_id) .'</span><br />';
                    }
                }
            ?>
        </td>
        <?php } ?>
        <td align="right" >
            <?php echo $qtt; ?>
        </td>
        <?php if ($this->doctype == 'invoice') { ?>
        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right" class="priceCol"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($item->product_tax ,$this->user_currency_id, $qtt)."</span>" ?></td>
        <?php } ?>

        <td align="right" class="priceCol" >
            <!-- FLORIAN CAMELEONS on vide la case si c'est à zéro -->
            <?php if($item->product_subtotal_discount!=0){
                echo  $this->currency->priceDisplay( $item->product_subtotal_discount, $this->user_currency_id );  //No quantity is already stored with it
            } ?>
        </td>

        <td align="right"  class="priceCol">
            <?php
                                                $item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
                                                $class = '';
                                                if(!empty($item->product_basePriceWithTax) && $item->product_basePriceWithTax != $item->product_final_price ) {
                                                    echo '<span class="line-through" >'.$this->currency->priceDisplay($item->product_basePriceWithTax,$this->user_currency_id,$qtt) .'</span><br />' ;
                                                }
                                                elseif (empty($item->product_basePriceWithTax) && $item->product_item_price != $item->product_final_price) {
                                                    echo '<span class="line-through">' . $this->currency->priceDisplay($item->product_item_price,$this->user_currency_id,$qtt) . '</span><br />';
                                                }

                                                echo $this->currency->priceDisplay(  $item->product_subtotal_with_tax ,$this->user_currency_id); //No quantity or you must use product_final_price ?>
        </td>
        <?php } ?>
    </tr>

    <?php
    }
    ?>
    <?php if ($this->doctype == 'invoice') { ?>
    <tr><td colspan="<?php echo $colspan ?>"></td></tr>
    <tr class="sectiontableentry1">
        <td colspan="6" align="right"><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>

        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_tax, $this->user_currency_id)."</span>" ?></td>
        <?php } ?>

        <!-- FLORIAN CAMELEONS ajout du if pour vider la colonne si c'est à zéro -->
        <?php if($this->orderDetails['details']['BT']->order_discountAmount!=0): ?>
        <td align="right"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_discountAmount, $this->user_currency_id)."</span>" ?></td>
        <?php endif; ?>
        <?php if($this->orderDetails['details']['BT']->order_discountAmount==0): ?>
        <td align="right"><?php echo "<span  class='priceColor2'></span>" ?></td>
        <?php endif; ?>


        <td align="right"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_salesPrice, $this->user_currency_id) ?></td>
    </tr>
    <?php
    if ($this->orderDetails['details']['BT']->coupon_discount <> 0.00) {
        $coupon_code=$this->orderDetails['details']['BT']->coupon_code?' ('.$this->orderDetails['details']['BT']->coupon_code.')':'';
    ?>
    <tr>
        <td align="right" class="pricePad" colspan="6"><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'COUPON_DISCOUNT').$coupon_code ?></td>
        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"> </td>
        <?php } ?>
        <td align="right"></td>
        <td align="right"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->coupon_discount, $this->user_currency_id); ?></td>
    </tr>
    <?php  } ?>

    <?php

                                            if($discountsBill){
                                                foreach($discountsBill as $rule){ ?>
    <tr >
        <td colspan="6" align="right" class="pricePad"><?php echo $rule->calc_rule_name ?> </td>
        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"> </td>
        <?php } ?>
        <td align="right"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
        <td align="right"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
    </tr>
    <?php
                                                                                }
                                            }



    ?>
    <tr>
        <!--<td align="right" class="pricePad" colspan="6"><?php //echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'CART_SHIPPING'); ?></td>-->
        <td align="right" class="pricePad" colspan="6"><?php echo $shipment_string; ?></td>

        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"><span class='priceColor2'><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment_tax, $this->user_currency_id) ?></span> </td>
        <?php } ?>
        <td align="right"></td>
        <td align="right"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment + $this->orderDetails['details']['BT']->order_shipment_tax, $this->user_currency_id); ?></td>
    </tr>

    <!-- FLORIAN CAMELEONS : condition sur le discount pour enlever la ligne si c'est égal à 0 -->
    <?php if($this->orderDetails['details']['BT']->order_payment!=0):  ?>
    <tr>
        <td align="right" class="pricePad" colspan="6"><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'CART_SUBTOTAL_DISCOUNT_PAYMENT'); ?></td>

        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"><span class='priceColor2'><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment_tax, $this->user_currency_id) ?></span> </td>
        <?php } ?>
        <td align="right"></td>
        <td align="right"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment + $this->orderDetails['details']['BT']->order_payment_tax, $this->user_currency_id); ?></td>
    </tr>
    <?php endif; ?>

    <tr>
        <td align="right" class="pricePad" colspan="6"><strong><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'ORDER_PRINT_TOTAL') ?></strong></td>

        <?php if ( VmConfig::get('show_tax')) { ?>
        <td align="right"><span class='priceColor2'><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billTaxAmount, $this->user_currency_id); ?></span></td>
        <?php } ?>

        <!-- FLORIAN CAMELEONS ajout du if pour vider la case si 0 -->
        <?php //if($this->orderDetails['details']['BT']->order_billDiscountAmount!=0): ?>
        <?php if($colDiscount!=0): ?>
        <td align="right"><span class='priceColor2'><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billDiscountAmount, $this->user_currency_id); ?></span></td>
        <?php endif; ?>
        <?php //if($this->orderDetails['details']['BT']->order_billDiscountAmount==0): ?>
        <?php if($colDiscount==0): ?>
        <td align="right"><span class='priceColor2'></span></td>
        <?php endif; ?>

        <td align="right"><strong><?php 
        echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total, $this->user_currency_id); 
        //BRUNO CAMELEONS : Si devise différente, alors prix indicatif en EUR
        if($this->user_currency_id != 47) {
            echo $this->currencyP->priceDisplay($this->orderDetails['details']['BT']->order_total, $this->orderDetails['details']['BT']->payment_currency_id);
        }
        ?></strong></td>
    </tr>
    <?php
                                            if($this->doVendor){
                                                $comp = $this->orderDetails['details']['BT']->order_currency;
                                            } else {
                                                $comp = $this->user_currency_id;
                                            }
                                            if(!empty($this->orderDetails['details']['BT']->payment_currency_rate)
                                               and $this->orderDetails['details']['BT']->payment_currency_id!=$comp and $this->orderDetails['details']['BT']->payment_currency_rate!=1.0){
    ?><tr>
    <td align="right" class="pricePad" colspan="7"><strong><?php echo vmText::_('COM_VM_'.$isEnglish.'TOTAL_IN_PAYMENT_CURRENCY') ?></strong></td>
    <td align="right" class="pricePad" colspan="2"><?php

        if($this->orderDetails['details']['BT']->order_currency==$this->orderDetails['details']['BT']->user_currency_id and $this->orderDetails['details']['BT']->user_currency_id!=$this->orderDetails['details']['BT']->payment_currency_id){
            echo $this->orderDetails['details']['BT']->payment_currency_rate;
        } else if ($this->orderDetails['details']['BT']->order_currency==$this->orderDetails['details']['BT']->payment_currency_id and $this->orderDetails['details']['BT']->payment_currency_id!=$this->orderDetails['details']['BT']->user_currency_id){
            echo $this->orderDetails['details']['BT']->user_currency_rate;
        }
                                                echo ' <strong>';
                                                echo $this->currencyP->priceDisplay($this->orderDetails['details']['BT']->order_total, $this->orderDetails['details']['BT']->payment_currency_id); ?>
    </strong></td>
</tr>
<?php
                                            }

                                            if($taxBill){
?><?php
                                                foreach($taxBill as $rule){
                                                    if ($rule->calc_kind == 'taxRulesBill' or $rule->calc_kind == 'VatTax' ) { ?>
<tr >
    <td colspan="6"  align="right" class="pricePad"><?php echo vmText::_('COM_VIRTUEMART_'.$isEnglish.'TOTAL_INCL_TAX') ?> </td>
    <td colspan="2"  align="right" class="pricePad"><?php echo $rule->label ?> </td>
    <?php if ( VmConfig::get('show_tax')) {  ?>
    <td align="right"><?php echo $this->currency->priceDisplay($rule->calc_result, $this->user_currency_id); ?></td>
    <?php } ?>
</tr>
<?php
                                                                                                                             }
                                                }
                                            }

?>
<?php } ?>
</table>
