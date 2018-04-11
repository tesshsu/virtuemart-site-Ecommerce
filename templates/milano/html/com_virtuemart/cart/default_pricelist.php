<fieldset class="vm-fieldset-pricelist">
<table class="cart-items">
<thead>
<tr>
    <th class="product-remove"><span><?php //BRUNO CAMELEON echo vmText::_ ('COM_VIRTUEMART_CART_DELETE') ?></span></th>
	<th class="product-image"><?php //BRUNO CAMELEON echo vmText::_ ('COM_VIRTUEMART_PRODUCT_IMAGES') ?></th> 
    <th class="product-name"><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT') ?></th> 
	<th class="product-quantity"><?php echo vmText::_ ('COM_VIRTUEMART_CART_QUANTITY') ?> </th>
	<th class="product-price"><?php echo vmText::_ ('COM_VIRTUEMART_CART_PRICE') ?></th>


	<?php if (VmConfig::get ('show_tax')) {
		$tax = vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT');
		if(!empty($this->cart->cartData['VatTax'])){
			reset($this->cart->cartData['VatTax']);
			$taxd = current($this->cart->cartData['VatTax']);
			$tax = $taxd['calc_name'] .' '. rtrim(trim($taxd['calc_value'],'0'),'.').'%';
		}
		?>
	<th><?php echo "<span  class='priceColor2'>" . $tax . '</span>' ?></th>
	<?php } ?>
	<th class="product-total"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL_HT') ?></th>
</tr>
</thead>
<tbody> 
<?php
$i = 1;

//var_dump($this->cart->pricesUnformatted);
//var_dump($this->cart->cartPrices);

foreach ($this->cart->products as $pkey => $prow) { 
	//var_dump($prow->prices);
 	//BRUNO CAMELEON : Récupération du parent pour les liens et thumbnails
	$productModel = VmModel::getModel('product');
	$mediaModel = VmModel::getModel('media');
	$parent = $productModel->getProduct($prow->product_parent_id);
    $media = $mediaModel->createMediaByIds($parent->virtuemart_media_id)[1];

	?>

<tr>
	<input type="hidden" name="cartpos[]" value="<?php echo $pkey ?>" />
    <td class="product-remove"><button type="submit"  class="fa fa-trash" name="delete.<?php echo $pkey ?>"   ></td>
    <td class="product-image"> 
    		 <?php

    		//BRUNO CAMELEON : Affichage de l'image du produit parent
			if (!empty($media)) {
				echo $media->displayMediaThumb ('', FALSE);
			}
			?> 	
    </td>
	<td class="product-name">  
				
		<?php echo JHtml::link ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$parent->virtuemart_product_id, $prow->product_name);
			echo $this->customfieldsModel->CustomsFieldCartDisplay ($prow);
		 ?>

	</td> 
	<td  class="product-quantity"><?php

				if ($prow->step_order_level)
					$step=$prow->step_order_level;
				else
					$step=1;
				if($step==0)
					$step=1;
				?>
 
            <input type="number" id="prod_qty" class="spinctrl" maxlength="3"  name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" init="<?php echo $prow->quantity ?>" step="<?php echo $step; ?>" <?php echo $maxOrder; ?> />

            <?php
            /*
                           onblur="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED')?>');"
                           onclick="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED')?>');"
                           onchange="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED')?>');"
                           onsubmit="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED')?>');"
            */ ?>
            

			
 
	</td>
	<?php ?>

	<td class="product-price"><?php // MODIFIE PAR BRUNO (salesPrice)
		 echo $this->currencyDisplay->createPriceDiv ('priceBeforeTax', '', $prow->prices, FALSE, FALSE, 1); ?>
	</td>

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td align="right"><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $prow->prices, FALSE, FALSE, $prow->quantity) . "</span>" ?></td>
	<?php } ?> 
	<td class="product-total"><?php // MODIFIE PAR BRUNO (salesPrice)
		echo $this->currencyDisplay->createPriceDiv ('priceBeforeTax', '', $prow->prices, FALSE, FALSE, $prow->quantity);
		 ?></td>
</tr>
	<?php
	$i = ($i==1) ? 2 : 1;
} ?>
</tbody>
<tfoot>
    <tr>
    	<td colspan="6">
    		<div class="row">
    			<div class="col-lg-6 col-md-6 col-xs-12 col-xsm-12"> 
                    <?php // Continue Shopping Button
        			if (!empty($this->continue_link_html)) {
        				echo $this->continue_link_html;
        			} ?> 
    			</div>
    			<div class="col-lg-6 col-md-6 col-xs-12 col-xsm-12"> 
    				<button name="updatecart.<?php echo $pkey ?>" type="submit" class="button pull-right"><?php echo  vmText::_ ('COM_VIRTUEMART_CART_UPDATE') ?></button>
    			</div>
    		</div>
    	</td>
    </tr>
</tfoot>
</table>
<div class="row">
<?php //BRUNO MODIF SUPPRESSION COUPON ?>
<div class="shipping-tax-block col-lg-7 col-md-7">
<?php if ( 	VmConfig::get('oncheckout_opc',true) or
	!VmConfig::get('oncheckout_show_steps',false) or
	(!VmConfig::get('oncheckout_opc',true) and VmConfig::get('oncheckout_show_steps',false) and
		!empty($this->cart->virtuemart_shipmentmethod_id) )
) { ?> 
	<?php //BRUNO CAMELEON AFFICHER CHOIX LIVRAISON if (!$this->cart->automaticSelectedShipment) { ?> 
    <div class="select_shipment">
			<?php
				echo '<h4>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_SHIPMENT').'</h4>'; 
        /*Issue #201 Add free delivery over 60euro in Fr language */
        $lang = JFactory::getLanguage();
        if($lang->getTag() == 'fr-FR') {
            echo '<dt>Livraison offerte à partir de 60€ (TTC) d&rsquo;achat. </dt>';
            echo '<dt>( L&rsquo;adresse de livraison doit &ecirc;tre en France m&eacute;tropolitaine )</dt>';
        }
        /*Issue #201 end */
		if (!empty($this->layoutName) and $this->layoutName == 'default') {
			if (VmConfig::get('oncheckout_opc', 0)) {
				$previouslayout = $this->setLayout('select');
				echo $this->loadTemplate('shipment');
				$this->setLayout($previouslayout);
			} else {
				echo JHtml::_('link', JRoute::_('index.php?option=com_virtuemart&view=cart&task=edit_shipment', $this->useXHTML, $this->useSSL), $this->select_shipment_text, 'class=""');
			}
		} else {
			echo vmText::_ ('COM_VIRTUEMART_CART_SHIPPING');
		} ?>
    </div>

<?php	//} ?>
    
<?php } ?>
<?php if ($this->cart->pricesUnformatted['salesPrice']>0.0 and
	( 	VmConfig::get('oncheckout_opc',true) or
		!VmConfig::get('oncheckout_show_steps',false) or
		( (!VmConfig::get('oncheckout_opc',true) and VmConfig::get('oncheckout_show_steps',false) ) and !empty($this->cart->virtuemart_paymentmethod_id))
	)
) { ?>  
    <div class="select_payment">
			<?php
				echo '<h4>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_PAYMENT').'</h4>'; 

		if (!empty($this->layoutName) && $this->layoutName == 'default') {
			if (VmConfig::get('oncheckout_opc', 0)) {
				$previouslayout = $this->setLayout('select');
				echo $this->loadTemplate('payment');
				$this->setLayout($previouslayout);
			} else {
				echo JHtml::_('link', JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment', $this->useXHTML, $this->useSSL), $this->select_payment_text, 'class=""');
			}
		} else {
		echo vmText::_ ('COM_VIRTUEMART_CART_PAYMENT');
	} ?>  
   </div> 
	<?php if (VmConfig::get ('show_tax')) { ?>
	 <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->cartPrices['paymentTax'], FALSE) . "</span>"; ?>  
	<?php } ?> 
<?php  } ?> 
</div>
<!--div class="coupon-block col-lg-4 col-md-4">
<?php 
if (VmConfig::get ('coupons_enable')) { ?>  
    <h4><?php echo vmText::_ ('COM_VIRTUEMART_DISCOUNT_CODE'); ?></h4>
    <p><?php echo vmText::_ ('COM_VIRTUEMART_DISCOUNT_CODE_DESCRIPTION'); ?></p>
    <hr /> 
		<?php if (!empty($this->layoutName) && $this->layoutName == 'default') {
		echo $this->loadTemplate ('coupon');
		} ?>

		<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
		<?php
		echo $this->cart->cartData['couponCode'];
		echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : '';
		?> 

		<?php if (VmConfig::get ('show_tax')) { ?>
	<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ('couponTax', '', $this->cart->cartPrices['couponTax'], FALSE); ?> </td>
		<?php }  ?>
	<td align="right"> </td>
	<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], FALSE); ?> </td>
	<?php } else { ?>

	&nbsp;</td>
	<td colspan="<?php echo $colspan ?>" align="left">&nbsp;</td>
	<?php }	?> 
<?php } ?>
</div-->
<?php  //Par BRUNO CAMELEON : TOTAL PANIER  
//var_dump($this->cart->cartPrices); ?>
<div class="col-lg-5 col-md-5">
    <div class="total-block">

        <div class="col-xs-6 subtotal text-right total_title">
        	<?php echo vmText::_ ('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
        </div>
        <div class="col-xs-6 subtotal text-right">
    		<?php echo $this->currencyDisplay->createPriceDiv ('', '', $this->cart->cartPrices[discountedPriceWithoutTax], FALSE);
				  //echo $this->currencyDisplay->createPriceDiv ('priceWithoutTax', '', $this->cart->cartPrices, FALSE);    			
    		 ?>
        </div>

        <?php if($this->cart->cartPrices['taxAmount'] > 0){ //TVA, PAS DE TAX SI PRO ?>
        <div class="col-xs-6 total_tva text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT_TOTAL') ?>: 
        </div>
        <div class="col-xs-6 subtotal text-right">
    		<?php echo $this->currencyDisplay->createPriceDiv ('taxAmount', '', $this->cart->cartPrices, FALSE) ?>
        </div>
       <?php } ?>

       <?php if($this->cart->cartPrices['salesPriceCoupon'] != 0){ //Réduction code promo ?>
        <div class="col-xs-6 total_tva text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:
        </div>
        <div class="col-xs-6 subtotal text-right">
    		<?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices, FALSE) ?>
        </div>
       <?php } ?>    

       <?php
        if ($this->cart->cartPrices['salesPriceShipment'] != '') { //Frais de port ?>

        <div class="col-xs-6 total_tva text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_CART_SHIPPING') ?>: 
        </div>
        <div class="col-xs-6 subtotal text-right">
    		<?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices, FALSE); ?>
        </div>
        <?php } ?>

       <?php if($this->cart->cartPrices['salesPricePayment'] != 0){ //Réduction paiement ?>
        <div class="col-xs-6 total_tva text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_PAYMENT') ?>: 
        </div>
        <div class="col-xs-6 subtotal text-right">
            <?php echo $this->currencyDisplay->priceDisplay ($this->cart->cartPrices['salesPricePayment']) ?>
        </div>
       <?php } ?>

        <div class="col-xs-6 total text-right">
        <?php 
        if($this->cart->cartPrices['taxAmount'] > 0){
        	echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL');
        } else {
        	echo 'TOTAL ';
        }
         ?>: 
        </div>
        <div class="col-xs-6 total text-right"> 
        <?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?>
        <?php
        if ($this->totalInPaymentCurrency) {
        echo $this->totalInPaymentCurrency;   
        }
        ?>
        </div>
        <div class="checkout-link-html  btn btn-primary"><?php 	echo $this->checkout_link_html; 	?></div>
        <div class="clearfix"></div>         
    </div>
</div>
<?php /*
<div class="col-lg-4 col-md-4">
    <div class="total-block">
        <div class="col-xs-8 subtotal text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
        </div>
        <div class="col-xs-4 subtotal text-right">
        <?php if (VmConfig::get ('show_tax')) { ?>
    	<?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $this->cart->cartPrices, FALSE) . "</span>" ?>
    	<?php } ?> 
    	<?php echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->cartPrices, FALSE) ?>
        </div>
        <div class="col-xs-8 total text-right">
        <?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL') ?>: 
        </div>
        <div class="col-xs-4 total text-right"> 
        <?php if (VmConfig::get ('show_tax')) { ?>
	       <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], FALSE) . "</span>" ?>  
	    <?php } ?>  
        <?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?>
        <?php
        if ($this->totalInPaymentCurrency) {
        echo $this->totalInPaymentCurrency;   
        }
        ?>
        </div>
        <div class="checkout-link-html  btn btn-primary"><?php 	echo $this->checkout_link_html; 	?></div>
        <div class="clearfix"></div>         
    </div>
</div>

*/ ?>
</div> 
</fieldset>
