<?php
/**
* sublayout products
*
* @package	VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle'])? $viewData['customTitle']: false;;
if(isset($viewData['class'])){
	$class = $viewData['class'];
} else {
	$class = 'related_product';
}

if (!empty($product->customfieldsSorted[$position])) {
	?>
	<div class="<?php echo $class?>  upsell_product">
		<?php
		if($customTitle and isset($product->customfieldsSorted[$position][0])){
			$field = $product->customfieldsSorted[$position][0]; ?>
		<div class="block-title">
        <h3>
			<span class="title-top"> 
    			<?php echo vmText::_ ($field->custom_title) ?>  
            </span>
		</h3>
		</div> <?php
		}
		?>
		<div id="featured-product" class="owl-carousel owl-theme btn-cart-tyle2"> 
					<?php					
					$custom_title = null;
					foreach ($product->customfieldsSorted[$position] as $field) {
						if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
						continue;
						?>		 															
						<?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
							<span class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
								<?php if ($field->custom_tip) {
									echo JHtml::tooltip ($field->custom_tip, vmText::_ ($field->custom_title), 'tooltip.png');
								} ?></span>
						<?php }
						if (!empty($field->display)){
        					?> 
    						<div class="item">
    							<div class="product-item">
                                    <div class="per-product">
        								<?php echo $field->display ?>		
                                    </div>							
    							</div>
    						</div> 
						<?php
						} 
						?>							
					
						<?php $custom_title = $field->custom_title; 
                	} ?>  
		 
		</div>
	</div>
<?php } ?>