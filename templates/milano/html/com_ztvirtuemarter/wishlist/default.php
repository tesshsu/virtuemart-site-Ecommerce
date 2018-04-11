<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

?>
<div class="wishlist_box cart-page">
<div class="wishlist_info">
    <h3 class="module-title">
        <?php echo JText::_('COM_WISHLIST_PRODUCT') ?>
    </h3>
</div> 
<div class="clear"></div>
<?php
if (!empty($this->products)) :
    ?>
    
    
     <table class="cart-items">
        <thead>
        <tr>
            <th class="product-remove"></th>
            <th class="product-image"><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT_IMAGES') ?></th> 
        	<th class="product-name"><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT') ?></th> 
        	<th class="product-price"><?php echo vmText::_ ('COM_VIRTUEMART_CART_PRICE') ?></th> 
        	<th class="product-quantity"></th>
        </tr>
        </thead>
        <tbody> 
        <?php
        $i = 1;
        foreach ($this->products as $pkey => $product)  :
            $alert = JText::sprintf('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $product->step_order_level);
            $discont = abs($product->prices['discountAmount']);
            foreach ($product->categoryItem as $key => $prod_cat) :
                $virtuemartCategoryId = $prod_cat['virtuemart_category_id'];
            endforeach;
            $currency = CurrencyDisplay::getInstance();
            $show_price = $currency->createPriceDiv('salesPrice', '', $product->prices, true);
             ?>
        <tr class="prod-row wishlists_prods_<?php echo $product->virtuemart_product_id ?>"> 
            <input type="hidden" class="quick_ids" name="virtuemart_product_id"
               value="<?php echo $product->virtuemart_product_id ?>"/>
            <td class="product-remove"> 
                <div class="remwishlists">
                    <button class="wishlist_del" 
                       onclick="ZtVirtuemarter.wishlist.remove('<?php echo $product->virtuemart_product_id; ?>');">
                        <i class="fa fa-trash"></i> 
                    </button>
                </div> 
            </td> 
            <td  class="product-image">
            <?php
                    $images = $product->images[0]->displayMediaThumb('',false); 
                    echo $images;
                    $mainImageUrl1 = !empty($images[0]->file_url_thumb) ?  JURI::root() . '' . $images[0]->file_url_thumb : JURI::root() . 'images/stories/virtuemart/noimage.gif';
                //    $image = '<img src="' . $mainImageUrl1 . '"     title="' . $product->product_name . '"   alt="' . $images[0]->file_meta . '" class="lazy browseProductImage featuredProductImageFirst" id="Img_to_Js_' . $product->virtuemart_product_id . '"/>';
                    echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId), '<div class="front">' . $image . '</div>');
                    ?>
        	</td>
        	<td class="product-name">
        	    <?php echo JHTML::link(JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId), shopFunctionsF::limitStringByWord($product->product_name, '40', '...')); ?>  
         	</td>
            <td class="product-total ">
        	    <?php
                if ((!empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) :
                    ?>  
                    <?php
                    if ($product->prices['basePriceWithTax'] > 0 && $discont > 0)
                        echo '<span class="PricebasePriceWithTax">' . $currency->createPriceDiv('basePriceWithTax', '', $product->prices, true) . '</span>';
                    if ($product->prices['salesPrice'] > 0)
                        echo '<span class="PricesalesPrice">' . $currency->createPriceDiv('salesPrice', '', $product->prices, true) . '</span>';
                    ?> 
                <?php
                else :
                    if ($product->prices['salesPrice'] <= 0 and VmConfig::get('askprice', 1)) :
                        ?>
                        <div class="call-a-question">
                            <a class="call modal" rel="{handler: 'iframe', size: {x: 460, y: 550}}"
                               href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId . '&tmpl=component'); ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
                        </div>
                    <?php
                    endif;
                endif; ?>  
         	</td>  
        	<td  class="product-quantity">
                 <?php if (!empty($show_price)) :
                        if ((!VmConfig::get('use_as_catalog', 0) and !empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) :
                            ?>
                            <div class="addtocart-area2"> 
                                    <form method="post" class="product" action="index.php"
                                          id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
                                        <input name="quantity" type="hidden" value="<?php echo $step ?>"/>
        
                                        <div class="addtocart-bar2">  
                                                <?php // Add the button
                                                $buttonLbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
                                                $buttonCls = 'addtocart-button cart-click'; //$buttonCls = 'addtocart_button';
                                                ?>
                                                <?php // Display the add to cart button ?> 
                                                <div class="addtocart_button2">
                                                    <?php if ($product->orderable) : ?>
                                                        <input type="submit" value="<?php echo $buttonLbl ?>" 
                                                               class="addtocart-button cart-click" />
                                                    <?php else : ?>
                                                        <div
                                                            title="<?php echo JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT'); ?>"
                                                            class="addtocart-button addtocart-button-disabled cart-click"><?php echo JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT'); ?></span>
                                                    <?php endif; ?>
                                                </div>
        
                                                <input type="hidden" class="pname"
                                                       value="<?php echo $product->product_name ?>"/>
                                                <input type="hidden" name="option" value="com_virtuemart"/>
                                                <input type="hidden" name="view" value="cart"/>
                                                <input type="hidden" name="task" value="add"/>
                                                <input type="hidden" class="item_id" name="virtuemart_product_id[]"
                                                       value="<?php echo $product->virtuemart_product_id ?>"/>
                                                <input type="hidden" name="virtuemart_category_id[]"
                                                       value="<?php echo $virtuemartCategoryId ?>"/> 
                                        </div>
                                    </form> 
                            </div>
                        <?php
                        endif;
                    endif; ?>
        	</td> 
         </tr>
        	<?php
        	$i = ($i==1) ? 2 : 1;
        endforeach; ?>
        </tbody> 
        </table>
    
     
    <?php
    echo '<h3 class="module-title wishlists no-products" style="display:none;"><i class="fa fa-info-circle"></i>' . JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_WHISHLIST') . '</h3>';
else :
    echo '<h3 class="module-title no-products"><i class="fa fa-info-circle"></i>' . JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_WHISHLIST') . '</h3>';
endif;
?>
</div>
 

