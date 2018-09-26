<?php // no direct access
defined('_JEXEC') or die('Restricted access');

vmJsApi::removeJScript("/modules/mod_virtuemart_cart/assets/js/update_cart.js");

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<!-- Virtuemart 2 Ajax Card -->
<div class="miniCartWrap ">
<div class="mini-maincart vmCartModule <?php echo $params->get('moduleclass_sfx'); ?>" id="vmCartModule<?php echo $params->get('moduleid_sfx'); ?>">
	<?php if ($show_product_list) { ?>
		<div class="block-mini-cart">			                       		
			<div class="mini-cart mini-cart-body">	 
                 <div class="cartSummary">
                	<div class="crat-icon" onclick = "void(0)">
                		<span class="icon_cart_alt"></span>
                		<span class="number zero"><?php echo  $data->totalProduct ?></span> 
                	</div>
                	<div class="cart-header">
                		<p class="cart-tolatl">
                			<span><span class="price total"><?php echo $data->billTotal; ?></span></span>
                		</p>
                	</div>	
                </div>
				<div id="hiddencontainer" class="hiddencontainer" style=" display: none; ">
					<div class="vmcontainer">
						<div class="product_row">
							<span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>
							<?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
								<div class="subtotal_with_tax" style="float: right;"></div>
							<?php } ?>
							<div class="customProductData"></div><br>
						</div>
					</div>
				</div>
				<div class="mini-contentCart">
                    <div class="block-content">
                        <p class="block-subtitle"><?php echo vmText::_ ('COM_VIRTUEMART_RECENTLY_ADDED_ITEMS'); ?></p>
    					<div class="vm_cart_products">
    						<ol class="vmcontainer mini-products-list clearfix">
    							<?php if(empty($data->products)) { ?>
    								<li class="empty"><?php echo JText::_('VM_LANG_CART_EMPTY')?></li>
    							<?php } else { ?>
    								<?php foreach ($data->products as $product){ ?>
    									<li class="item clearfix">
    										<span class="quantity"><?php echo  $product['quantity'] ?></span>&nbsp;x&nbsp;<span class="product_name"><?php echo  $product['product_name'] ?></span>
    										<?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
    											<div class="subtotal_with_tax" style="float: right;"><?php echo $product['subtotal_with_tax'] ?></div>
    										<?php } ?>
    										<?php if ( !empty($product['customProductData']) ) { ?>
    											<div class="customProductData"><?php echo $product['customProductData'] ?></div><br>
    										<?php } ?>
    									</li>
    								<?php } ?>
    							<?php } ?>
    						</ol>
    					</div>
    					
    					<div class="total">
    						<?php if ($data->totalProduct and $show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
    							<?php echo $data->billTotal; ?>
    						<?php } ?>
    					</div>
    
    					<!--<div class="total_products"><?php //echo  $data->totalProductTxt ?></div>-->
    					
    					<div class="show_cart">
    						<?php if ($data->totalProduct) { ?>
    							<?php echo  $data->cart_show; ?>
    						<?php } ?>
    					</div>
    					
    					<div style="clear:both;"></div>
    					<div class="payments-signin-button" ></div>
                    </div>
				</div>			
			</div>			
		</div>
	<?php } ?>
	<noscript>
		<?php echo vmText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>
	</noscript>
	
	<script>
		if (typeof Virtuemart === "undefined")
		Virtuemart = {};
		
		jQuery(function($) {
			Virtuemart.customUpdateVirtueMartCartModule = function(el, options){
				var base 	= this;
				var $this	= $(this);
				base.$el 	= $(".vmCartModule");

				base.options 	= $.extend({}, Virtuemart.customUpdateVirtueMartCartModule.defaults, options);
					
				base.init = function(){
					$.ajaxSetup({ cache: false })
					$.getJSON(window.vmSiteurl + "index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json" + window.vmLang,
						function (datas, textStatus) {
							base.$el.each(function( index ,  module ) {
								if (datas.totalProduct > 0) {
									$(module).find(".vm_cart_products").html("");
									$.each(datas.products, function (key, val) {
										//jQuery("#hiddencontainer .vmcontainer").clone().appendTo(".vmcontainer .vm_cart_products");
										$(module).find(".hiddencontainer .vmcontainer .product_row").clone().appendTo( $(module).find(".vm_cart_products") );
										$.each(val, function (key, val) {
											$(module).find(".vm_cart_products ." + key).last().html(val);
										});
									});
								}
								$(module).find(".show_cart").html(datas.cart_show);
								//$(module).find(".total_products").html(	datas.totalProductTxt);
								$(module).find(".number").html(datas.totalProduct);
								$(module).find(".total").html(datas.billTotal);
							});
						}
					);			
				};
				base.init();
			};
			// Definition Of Defaults
			Virtuemart.customUpdateVirtueMartCartModule.defaults = {
				name1: 'value1'
			};

		});

		jQuery(document).ready(function( $ ) {
			jQuery(document).off("updateVirtueMartCartModule","body",Virtuemart.customUpdateVirtueMartCartModule);
			jQuery(document).on("updateVirtueMartCartModule","body",Virtuemart.customUpdateVirtueMartCartModule);
		});
	</script>
</div>
</div>