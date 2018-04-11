<?php // no direct access
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Product Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined ('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
JHtml::_('jquery.ui');
$doc = JFactory::getDocument();
$doc->addStyleSheet( 'http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );

?>

<div class="vmgroup <?php echo $params->get ('moduleclass_sfx') ?> tabs-product">

    <?php if ($headerText) : ?>
        <div class="vmheader"><?php echo $headerText ?></div>
    <?php endif; ?>
    <div class="vmproduct " id="tab-vmproduct-<?php echo $module->id; ?>">
        <ul>
            <?php
            foreach($products as $key => $value):
                ?>
                <li><a href="#producttabs-<?php echo $key ?>-<?php echo $module->id; ?>"><?php echo ModZtvirtuemarterProductHelper::getTabsText($key); ?></a></li>
                <?php
            endforeach;
            ?>
        </ul>
        <?php
        foreach ($products as $key => $items) :
            ?>
            <div id="producttabs-<?php echo $key ?>-<?php echo $module->id; ?>">
                <?php foreach ($items as $product) : ?>
                    <div class="products  product-<?php echo $product->virtuemart_product_id; ?>">
                        <div class="item">
                            <div class="spacer">
                                <div class="vm-product-media-container zt-product-content ">
                                    <?php
                                    if (!empty($product->images[0])) {
                                        $image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
                                    } else {
                                        $image = '';
                                    }
                                    echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                                    echo '<div class="clear"></div>';
                                    $url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                                        $product->virtuemart_category_id); ?>
                                    <?php
                                    echo ModZtvirtuemarterProductHelper::label($product, $newProductFrom);
                                    ?>
                                </div>
                                <h3 class="product-name">
                                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>
                                </h3>

                                <div class="clear"></div>
                                <?php
                                $saleClass = '';
                                if ($product->prices['product_override_price'] > 0) {
                                    $saleClass = ' product-sale';
                                }
                                echo '<div class="' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency)) . '</div>';
                                ?>
                                <div class="product_hover add-to-link">
                                    <?php
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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="clear"></div>
    <?php
    if ($footerText) : ?>
        <div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
            <?php echo $footerText ?>
        </div>
    <?php endif; ?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('#tab-vmproduct-<?php echo $module->id; ?>').tabs();
    });
</script>
