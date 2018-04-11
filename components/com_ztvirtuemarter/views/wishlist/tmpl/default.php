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
<div class="wishlist_box">
    <div class="wishlist_info">
        <h3 class="module-title">
            <?php echo JText::_('COM_WISHLIST_PRODUCT') ?>
        </h3>
    </div>

    <div class="back-to-category">
        <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=virtuemart'); ?>"
           class="button_back button reset2" title="<?php echo jText::_('COM_VIRTUEMART_SHOP_HOME'); ?>">
            <i class="fa fa-reply"></i><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO', jText::_('COM_VIRTUEMART_SHOP_HOME')) ?>
        </a>
    </div>
    <div class="clear"></div>
    <?php
    if (!empty($this->products)) :
        ?>
        <div id="product_list" class="list">
            <ul id="slider" class="vmproduct layout">
                <li>
                    <?php // Start the Output
                    foreach ($this->products as $product) :
                        $alert = JText::sprintf('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $product->step_order_level);
                        $discont = abs($product->prices['discountAmount']);
                        foreach ($product->categoryItem as $key => $prod_cat) :
                            $virtuemartCategoryId = $prod_cat['virtuemart_category_id'];
                        endforeach;
                        $currency = CurrencyDisplay::getInstance();
                        $show_price = $currency->createPriceDiv('salesPrice', '', $product->prices, true);
                        ?>
                        <div class="prod-row wishlists_prods_<?php echo $product->virtuemart_product_id ?>">
                            <div class="product-box hover spacer <?php echo ($discont > 0) ? 'disc' : ''; ?> ">
                                <input type="hidden" class="quick_ids" name="virtuemart_product_id"
                                       value="<?php echo $product->virtuemart_product_id ?>"/>

                                <div class="left-img">
                                    <div class="browseImage ">
                                        <div class="lbl-box2">
                                            <?php
                                            $stockhandle = VmConfig::get('stockhandle', 'none');
                                            if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and (($product->product_in_stock - $product->product_ordered) < 1) ||
                                                (($product->product_in_stock - $product->product_ordered) < $product->min_order_level)
                                            ) :
                                                ?>
                                                <div class="soldafter"></div>
                                                <div class="soldbefore"></div>
                                                <div class="sold"><?php echo JText::_('DR_SOLD'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="lbl-box">
                                            <?php if ($product->prices['override'] == 1 && ($product->prices['product_price_publish_down'] > 0)) : ?>
                                                <div class="offafter"></div>
                                                <div class="offbefore"></div>
                                                <div class="discount limited"><?php echo JText::_('DR_LIMITED_OFFER'); ?></div>
                                            <?php elseif ($discont > 0 && $product->product_sales < 20) : ?>
                                                <div class="discafter"></div>
                                                <div class="discbefore"></div>
                                                <div class="discount"><?php echo JText::_('DR_SALE'); ?></div>
                                                <?php
                                            elseif ($product->product_sales > 20) : ?>
                                                <div class="hitafter"></div>
                                                <div class="hitbefore"></div>
                                                <div class="hit"><?php echo JText::_('DR_HOT'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="img-wrapper">
                                            <?php
                                            $images = $product->images;
                                            $noimage = JURI::root() . 'images/stories/virtuemart/noimage.gif';
                                            if (!empty($images[0])) $image =  $images[0]->displayMediaThumb('title="' . $images[0]->file_title . '"   alt="' . $images[0]->file_meta . '" class="lazy browseProductImage featuredProductImageFirst" id="Img_to_Js_' . $product->virtuemart_product_id . '" border="0"', FALSE);
                                            else $image = '<img data-original="' . $noimage . '"  src="modules/mod_virtuemart_product/js/images/preloader.gif"  title="' . $images[0]->file_title . '"   alt="' . $images[0]->file_meta . '" class="lazy browseProductImage featuredProductImageFirst" id="Img_to_Js_' . $product->virtuemart_product_id . '"/>';
                                            echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId), '<div class="front">' . $image . '</div>');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="slide-hover">
                                    <div class="wrapper">
                                        <div class="Title">
                                            <?php echo JHTML::link(JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId), shopFunctionsF::limitStringByWord($product->product_name, '40', '...'), array('title' => $product->product_name)); ?>
                                        </div>
                                        <div class="clear"></div>
                                        <?php
                                        $ratingModel = VmModel::getModel('ratings');
                                        $rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
                                        $r = !empty($rating) ? $rating->rating : 0;

                                        $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
                                        $ratingwidth = ($r * 100) / $maxrating; //I don't use round as percetntage
                                        if (!empty($rating)) : ?>
                                            <span class="vote">
                            <span title="" class="vmicon ratingbox" style="display:inline-block;">
                                <span class="stars-orange" style="width:<?php echo $ratingwidth; ?>%"></span>
                            </span>
                            <span
                                class="rating-title"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . round($rating->rating, 2) . '/' . $maxrating; ?></span>
                        </span>
                                        <?php else : ?>
                                            <span class="vote">
                            <span title="" class="vmicon ratingbox" style="display:inline-block;">
                                <span class="stars-orange" style="width:<?php echo $ratingwidth; ?>%"></span>
                            </span>
                            <span
                                class="rating-title"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
                       </span>
                                        <?php endif; ?>
                                        <?php
                                        if ((!empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) :
                                            ?>
                                            <div class="Price">
                                                <div class="product-price marginbottom12"
                                                     id="productPrice<?php echo $this->product->virtuemart_product_id; ?>">
                                                    <?php
                                                    if ($product->prices['basePriceWithTax'] > 0 && $discont > 0)
                                                        echo '<span class="PricebasePriceWithTax">' . $currency->createPriceDiv('basePriceWithTax', '', $product->prices, true) . '</span>';
                                                    if ($product->prices['salesPrice'] > 0)
                                                        echo '<span class="PricesalesPrice">' . $currency->createPriceDiv('salesPrice', '', $product->prices, true) . '</span>';
                                                    ?>
                                                </div>
                                            </div>
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
                                        <div class="clear"></div>
                                        <?php // Product Short Description
                                        if (!empty($product->product_s_desc)) : ?>
                                            <div
                                                class="desc1"><?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, 250, '...') ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wrapper-slide">
                                        <?php if ((!empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) : ?>
                                            <div class="Price product-price list marginbottom12"
                                                 id="productPrice<?php echo $product->virtuemart_product_id ?>">
                                                <?php
                                                if ($product->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel'))
                                                    echo "<strong>" . JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT') . ' (' . $product->product_unit . "):</strong>";

                                                if ($discont > 0)
                                                    echo $currency->createPriceDiv('basePriceWithTax', '', $product->prices);
                                                else
                                                    echo $currency->createPriceDiv('salesPrice', '', $product->prices);
                                                ?>
                                            </div>
                                            <?php
                                        else :
                                            if ($product->prices['salesPrice'] <= 0 and VmConfig::get('askprice', 1)) :
                                                ?>
                                                <div class="call-a-question list">
                                                    <a class="call modal addtocart-button" rel="{handler: 'iframe', size: {x: 460, y: 550}}"
                                                       href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $virtuemartCategoryId . '&tmpl=component'); ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
                                                </div>
                                                <?php
                                            endif;
                                        endif; ?>
                                        <?php if (!empty($show_price)) :
                                            if ((!VmConfig::get('use_as_catalog', 0) and !empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) :
                                                ?>
                                                <div class="addtocart-area2">
                                                    <?php
                                                    $stockhandle = VmConfig::get('stockhandle', 'none');
                                                    if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and (($product->product_in_stock - $product->product_ordered) < 1) || (($product->product_in_stock - $product->product_ordered) < $product->min_order_level)) :
                                                        ?>
                                                        <span class="addtocart_button2">
                                <a class="addtocart-button" title="<?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?>"
                                   href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?>
                                    <span></span>
                                </a>
                            </span>
                                                    <?php else : ?>
                                                        <form method="post" class="product" action="index.php"
                                                              id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
                                                            <input name="quantity" type="hidden" value="<?php echo $step ?>"/>

                                                            <div class="addtocart-bar2">
                                                                <script type="text/javascript">
                                                                    function check(obj) {
                                                                        // use the modulus operator '%' to see if there is a remainder
                                                                        remainder = obj.value % <?php echo $step?>;
                                                                        quantity = obj.value;
                                                                        if (remainder != 0) {
                                                                            alert('<?php echo $alert?>!');
                                                                            obj.value = quantity - remainder;
                                                                            return false;
                                                                        }
                                                                        return true;
                                                                    }
                                                                </script>
                                                                <?php // Display the quantity box
                                                                if (!empty($product->customfields)) :
                                                                    foreach ($product->customfields as $k => $custom) :
                                                                        if (!empty($custom->layout_pos)) :
                                                                            $product->customfieldsSorted[$custom->layout_pos][] = $custom;
                                                                            unset($product->customfields[$k]);
                                                                        endif;
                                                                    endforeach;
                                                                    $product->customfieldsSorted['normal'] = $product->customfields;
                                                                    unset($product->customfields);
                                                                endif;
                                                                $position = 'addtocart';
                                                                if (!empty($product->customfieldsSorted[$position])) :
                                                                    ?>
                                                                    <span class="attributes"><b>*</b> Product has attributes</span>
                                                                    <div class="addtocart_button2">
                                                                        <?php echo JHTML::link($product->link, JText::_('DR_VIRTUEMART_SELECT_OPTION') . '<span>&nbsp;</span>', array('title' => JText::_('DR_VIRTUEMART_SELECT_OPTION'), 'class' => 'addtocart-button')); ?>
                                                                    </div>

                                                                <?php else : ?>
                                                                    <span class="box-quantity">
                                                <span class="quantity-box">
                                                    <input type="text" class="quantity-input js-recalculate"
                                                           name="quantity[]" onblur="check(this);"
                                                           value="<?php if (isset($product->step_order_level) && (int)$product->step_order_level > 0) :
                                                               echo $product->step_order_level;
                                                           elseif (!empty($product->min_order_level)) :
                                                               echo $product->min_order_level;
                                                           else :
                                                               echo '1';
                                                           endif; ?>"/>
                                                </span>
                                                <span class="quantity-controls">
                                                    <i class="quantity-controls quantity-plus">+</i>
                                                    <i class="quantity-controls quantity-minus">-</i>
                                                </span>
                                            </span>
                                                                    <?php // Add the button
                                                                    $buttonLbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
                                                                    $buttonCls = 'addtocart-button cart-click'; //$buttonCls = 'addtocart_button';
                                                                    ?>
                                                                    <?php // Display the add to cart button ?>
                                                                    <div class="clear"></div>
                                                                    <span class="addtocart_button2">
                                                    <?php if ($product->orderable) : ?>
                                                        <input type="submit" value="<?php echo $buttonLbl ?>"
                                                               title="<?php echo JText::_('COM_VIRTUEMART_CART_ADD_TO'); ?>"
                                                               class="addtocart-button cart-click">
                                                    <?php else : ?>
                                                        <span
                                                            title="<?php echo JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT'); ?>"
                                                            class="addtocart-button addtocart-button-disabled cart-click"><?php echo JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT'); ?></span>
                                                    <?php endif; ?>
                                                </span>

                                                                    <input type="hidden" class="pname"
                                                                           value="<?php echo $product->product_name ?>"/>
                                                                    <input type="hidden" name="option" value="com_virtuemart"/>
                                                                    <input type="hidden" name="view" value="cart"/>
                                                                    <noscript><input type="hidden" name="task" value="add"/></noscript>
                                                                    <input type="hidden" class="item_id" name="virtuemart_product_id[]"
                                                                           value="<?php echo $product->virtuemart_product_id ?>"/>
                                                                    <input type="hidden" name="virtuemart_category_id[]"
                                                                           value="<?php echo $virtuemartCategoryId ?>"/>
                                                                <?php endif; ?>
                                                            </div>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <?php
                                            endif;
                                        endif; ?>
                                        <div class="clear"></div>
                                        <div class="remwishlists">
                                            <a class="wishlist_del" title="remove"
                                               onclick="ZtVirtuemarter.wishlist.remove('<?php echo $product->virtuemart_product_id; ?>');">
                                                <i class="fa fa-times"></i><?php echo JText::_('REMOVE'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>

                            </div>
                        </div>
                        <div class="clear"></div>
                    <?php endforeach; ?>
                </li>
            </ul>
        </div>
        <?php
        echo '<h3 class="module-title wishlists no-products" style="display:none;"><i class="fa fa-info-circle"></i>' . JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_WHISHLIST') . '</h3>';
    else :
        echo '<h3 class="module-title no-products"><i class="fa fa-info-circle"></i>' . JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_WHISHLIST') . '</h3>';
    endif;
    ?>
</div>

<script type="text/javascript">
    function tooltip() {
        jQuery('#product_list.list .hasTooltip').tooltip();
    }
    jQuery(document).ready(function ($) {
        tooltip();
        $("#product_list img.lazy").lazyload({
            effect: "fadeIn"
        });

        $(function () {
            $('#product_list div.product-box').each(function () {
                var tip = $(this).find('div.count_holder_small');

                $(this).hover(
                    function () {
                        tip.appendTo('body');
                    },
                    function () {
                        tip.appendTo(this);
                    }
                ).mousemove(function (e) {
                    var x = e.pageX + 60,
                        y = e.pageY + 50,
                        w = tip.width(),
                        h = tip.height(),
                        dx = $(window).width() - (x + w),
                        dy = $(window).height() - (y + h);

                    if (dx < 50) x = e.pageX - w - 60;
                    if (dy < 50) y = e.pageY - h + 130;

                    tip.css({ left: x, top: y });
                });
            });

        });


    });
</script>

