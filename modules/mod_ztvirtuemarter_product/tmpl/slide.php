<?php // no direct access
defined('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();

$col = 1;
$pwidth = ' width' . floor(100 / $productsPerRow);
if ($productsPerRow > 1) {
    $float = "floatleft";
} else {
    $float = "center";
}
?>
<div class="vmgroup<?php echo $params->get('moduleclass_sfx') ?>" id="slide-product">

    <?php if ($headerText) { ?>
        <div class="vmheader"><?php echo $headerText ?></div>
        <?php
    }
    if ($displayStyle == "div") {
        ?>
        <ul class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?> productdetails">
            <?php foreach ($products as $product) : ?>
                <li class="item">
                    <?php
                    if (!empty($product->images[0])) {
                        $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"', FALSE);
                    } else {
                        $image = '';
                    }
                    echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                    echo '<div class="clear"></div>';
                    $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                        $product->virtuemart_category_id); ?>
                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';
                    // $product->prices is not set when show_prices in config is unchecked
                    if ($showPrice and isset($product->prices)) {
                        echo '<div class="product-price">' . $currency->createPriceDiv('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                        if ($product->prices['salesPriceWithDiscount'] > 0) {
                            echo $currency->createPriceDiv('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                        }
                        echo '</div>';
                    }
                    if ($show_addtocart) {
                        echo shopFunctionsF::renderVmSubLayout('addtocart', array('product' => $product));
                    }
                    ?>
                </li>
                <?php
                if ($col == $productsPerRow && $productsPerRow && $last) {
                    echo '
		</ul><div class="clear"></div>';
                    $col = 1;
                } else {
                    $col++;
                }
                $last--;
            endforeach; ?>
        </ul>
        <br style='clear:both;'/>

        <?php
    } else {
        $last = count($products) - 1;
        ?>


        <div class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?> productdetails">

            <?php foreach ($products as $product) { ?>
                <div class="products">
                    <div class="item">
                        <div class="spacer">
                            <?php
                            if (!empty($product->images[0])) {
                                $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"', FALSE);
                            } else {
                                $image = '';
                            }
                            echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                            echo '<div class="clear"></div>';
                            $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                                $product->virtuemart_category_id); ?>

                            <input class="quick_ids" type="hidden"
                                   value="<?php echo $product->virtuemart_product_id; ?>">
                            <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';

                            if ($showPrice) {
                                // 		echo $currency->priceDisplay($product->prices['salesPrice']);
                                if (!empty($product->prices['salesPrice'])) {
                                    echo $currency->createPriceDiv('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                                }
                                // 		if ($product->prices['salesPriceWithDiscount']>0) echo $currency->priceDisplay($product->prices['salesPriceWithDiscount']);
                                if (!empty($product->prices['salesPriceWithDiscount'])) {
                                    echo $currency->createPriceDiv('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                                }
                            }

                            if ($showAddtocart) {
                                $oder = 0;
                                for ($i = 0; $i < strlen($url); $i++) {
                                    if ($url[$i] == '/') {
                                        $oder = $i;
                                    }
                                }
                                $abc = substr($url, $oder);
                                $urlLink = str_replace($abc, "", $url);
                                echo ModZtvirtuemarterProductHelper::addtocart($product);
                            }
                            ?>

                            <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                            <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                            <?php plgSystemZtvirtuemarter::addQuickviewButton($product); ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($col == $productsPerRow && $productsPerRow && $col < $totalProd) {
                    //echo "	</div>";
                    $col = 1;
                } else {
                    $col++;
                }
            } ?>
        </div>
        <div class="clear"></div>

        <?php
    }
    if ($footerText) : ?>
        <div class="vmfooter<?php echo $params->get('moduleclass_sfx') ?>">
            <?php echo $footerText ?>
        </div>
    <?php endif; ?>
</div>