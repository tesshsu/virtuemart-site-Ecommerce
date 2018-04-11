<?php // no direct access
defined('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();
error_reporting(E_ALL);
$col = 1;
$pwidth = ' width' . floor(100 / $productsPerRow);
if ($productsPerRow > 1) :
    $float = "floatleft";
else:
    $float = "center";
endif;

?>

<div class="vmgroup<?php echo $params->get('moduleclass_sfx') ?>">

    <?php if ($headerText) : ?>
        <div class="vmheader"><?php echo $headerText ?></div>
        <?php
    endif;
    if ($displayStyle == "div") :
        ?>
        <div id="vmproduct" class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?> productdetails ">
            <?php foreach ($productss as $product) :
                $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id);
                ?>
                <div class="col-md-3 col-sm-3 product-item product-grid-item">
                    <div class="spacer ">
                        <h3><a href="<?php echo $url ?>"><?php echo $product->product_name ?></a></h3>
                        <?php
                        echo ModZtvirtuemarterProductHelper::label($product, $newProductFrom);

                        $ratingModel = VmModel::getModel('ratings');
                        $product->showRating = $ratingModel->showRating($product->virtuemart_product_id);
                        if ($product->showRating) {
                            $rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
                            if (!empty($rating)) {
                                $r = $rating->rating;
                            } else {
                                $r = 0;
                            }
                            $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
                            $ratingwidth = ($r * 100) / $maxrating;
                            $rateStar = '';
                            $rateStar .= '<div class="comare_rating">';
                            $rateStar .= '<div class="rating">';
                            $rateStar .= '<span class="vote">';
                            $rateStar .= '<span title="" class="vmicon ratingbox" style="display:inline-block;">';
                            $rateStar .= '<span class="stars-orange" style="width:' . $ratingwidth . '%">';
                            $rateStar .= '</span>';
                            $rateStar .= '</span>';
                            $rateStar .= '</span>';
                            $rateStar .= '</div>';
                            $rateStar .= '</div>';
                            echo $rateStar;
                        }
                        echo '<div class="vm-product-media-container">';
                        if (!empty($product->images[0])) {
                            $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"', FALSE);
                        } else {
                            $image = '';
                        }
                        $saleClass = '';

                        if ($showPrice && $product->prices['product_override_price'] > 0) {
                            $saleClass = ' product-sale';
                        }
                        echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                        echo '<div class="clear"></div>';
                        echo '</div>';
                        echo '<div class="clear"></div>';
                        echo '<div class="product-bottom"><div class="price">';
                        if($showPrice)
                            echo '<div class="product-price"' . $saleClass . '" id="productPrice"'.$product->virtuemart_product_id.'">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency)) . '</div>';

                        echo '</div>';
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
                        echo '</div>';
                        ?>
                        <div class="product_hover zt-product-content">
                            <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                            <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                            <?php plgSystemZtvirtuemarter::addQuickviewButton($product); ?>
                            <input class="quick_ids" type="hidden"
                                   value="<?php echo $product->virtuemart_product_id; ?>">
                        </div>
                    </div>
                </div>
                <?php
                if ($col == $productsPerRow && $productsPerRow && $col < $totalProd) {
                    echo "	</div><div style='clear:both;'>";
                    $col = 1;
                } else {
                    $col++;
                }
            endforeach; ?>
        </div>
        <br style='clear:both;'/>
        <?php
    else :
    $last = count($productss) - 1;
    ?>
    <ul id="vmproduct" class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?> productdetails ">
        <?php foreach ($productss as $product) : ?>
            <li class="col-md-3 col-sm-3 product-item">
                <div class="spacer">
                    <h3><a href="<?php echo $url ?>"><?php echo $product->product_name ?></a></h3>
                    <?php
                    echo ModZtvirtuemarterProductHelper::label($product, $newProductFrom);
                    if (!empty($product->images[0])) {
                        $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"', FALSE);
                    } else {
                        $image = '';
                    }
                    echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                    echo '<div class="clear"></div>';
                    $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                        $product->virtuemart_category_id);
                    ?>
                    <div class="clear"></div>
                    <?php
                    // $product->prices is not set when show_prices in config is unchecked
                    if ($showPrice && isset($product->prices)) {
                        echo '<div class="product-price"  id="productPrice"'.$product->virtuemart_product_id.'">"' . $currency->createPriceDiv('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                        if ($product->prices['salesPriceWithDiscount'] > 0) {
                            echo $currency->createPriceDiv('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
                        }
                        echo '</div>';
                    }
                    if ($showAddtocart) {
                        echo ModZtvirtuemarterProductHelper::addtocart($product);
                    }
                    ?>
                    <div class="product_hover zt-product-content">
                        <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                        <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                        <?php plgSystemZtvirtuemarter::addQuickviewButton($product); ?>
                        <input class="quick_ids" type="hidden" value="<?php echo $product->virtuemart_product_id; ?>">
                    </div>
                </div>
            </li>
            <?php
            if ($col == $productsPerRow && $productsPerRow && $last) {
                echo '
                    </ul><div class="clear"></div>
                    <ul  class="vmproduct' . $params->get('moduleclass_sfx') . ' productdetails">';
                $col = 1;
            } else {
                $col++;
            }
            $last--;
        endforeach; ?>
</div>
    <div class="clear"></div>
<?php
endif;
if ($footerText) : ?>
    <div class="vmfooter<?php echo $params->get('moduleclass_sfx') ?>">
        <?php echo $footerText ?>
    </div>
<?php endif; ?>
</div>

<!--ajax-->
