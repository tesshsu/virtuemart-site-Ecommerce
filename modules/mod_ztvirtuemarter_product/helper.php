<?php
defined('_JEXEC') or die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/*
* Module Helper
*
* @package VirtueMart
* @copyright (C) 2010 - Patrick Kohl
* @copyright (C) 2011 - 2014 The VirtueMart Team
* @ Email: max@virtuemart.net
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
if (!class_exists('ModZtvirtuemarterProductHelper')) {
    class ModZtvirtuemarterProductHelper
    {

        public function __constructs()
        {

            if (!class_exists('VmConfig')) {
                require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
            }

            VmConfig::loadConfig();

            // Load the language file of com_virtuemart.
            VmConfig::loadJLang('com_virtuemart', true);
            if (!class_exists('calculationHelper')) {
                require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/calculationh.php');
            }
            if (!class_exists('CurrencyDisplay')) {
                require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/currencydisplay.php');
            }
            if (!class_exists('VirtueMartModelVendor')) {
                require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/vendor.php');
            }
            if (!class_exists('VmImage')) {
                require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/image.php');
            }
            if (!class_exists('shopFunctionsF')) {
                require(JPATH_SITE . '/components/com_virtuemart/helpers/shopfunctionsf.php');
            }
            if (!class_exists('calculationHelper')) {
                require(JPATH_COMPONENT_SITE . '/helpers/cart.php');
            }
            if (!class_exists('VirtueMartModelProduct')) {
                JLoader::import('product', JPATH_ADMINISTRATOR . '/components/com_virtuemart/models');
            }
        }

        public static function addtocart($product)
        {

            if (!VmConfig::get('use_as_catalog', 0)) {
                $stockhandle = VmConfig::get('stockhandle', 'none');
                if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($product->product_in_stock - $product->product_ordered) < 1) {
                    $button_lbl = vmText::_('COM_VIRTUEMART_CART_NOTIFY');
                    $button_cls = 'notify-button';
                    $button_name = 'notifycustomer';
                    ?>
                    <div style="display:inline-block;">
                        <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $product->virtuemart_product_id); ?>"
                           class="notify"><?php echo vmText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
                    </div>
                <?php
                } else {
                    ?>
                    <div class="addtocart-area">

                        <form method="post" class="product" action="index.php">
                            <?php
                            // Product custom_fields
                            if (!empty($product->customfieldsCart)) {
                                ?>
                                <div class="product-fields">
                                    <?php foreach ($product->customfieldsCart as $field) { ?>

                                        <div style="display:inline-block;"
                                             class="product-field product-field-type-<?php echo $field->field_type ?>">
                                            <?php if ($field->show_title == 1) { ?>
                                                <span
                                                    class="product-fields-title"><b><?php echo $field->custom_title ?></b></span>
                                                <?php echo JHTML::tooltip($field->custom_tip, $field->custom_title, 'tooltip.png'); ?>
                                            <?php } ?>
                                            <span class="product-field-display"><?php echo $field->display ?></span>
                                            <span class="product-field-desc"><?php echo $field->custom_desc ?></span>
                                        </div>

                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <div class="addtocart-bar">

                        <span class="quantity-box">
                        <input type="text" class="quantity-input" name="quantity[]" value="1"/>
                        </span>
                        <span class="quantity-controls">
                        <input type="button" class="quantity-controls quantity-plus"/>
                        <input type="button" class="quantity-controls quantity-minus"/>
                        </span>

                        <span class="addtocart-button">
                            <?php echo shopFunctionsF::getAddToCartButton($product->orderable); ?>
                        </span>

                                <div class="clear"></div>
                            </div>

                            <input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
                            <input type="hidden" name="option" value="com_virtuemart"/>
                            <input type="hidden" name="view" value="cart"/>
                            <noscript><input type="hidden" name="task" value="add"/></noscript>
                            <input type="hidden" name="virtuemart_product_id[]"
                                   value="<?php echo $product->virtuemart_product_id ?>"/>
                            <input type="hidden" name="virtuemart_category_id[]"
                                   value="<?php echo $product->virtuemart_category_id ?>"/>
                        </form>
                        <div class="clear"></div>
                    </div>
                <?php
                }
            }
        }

        public static function label($product, $newProductFrom = 7)
        {
        
            $sale = (isset($product->prices['product_override_price'])) ?  $product->prices['product_override_price'] : 0;

            $htmlLabel = '';
            $dateDiff = date_diff(date_create(), date_create($product->product_available_date));
            if ($dateDiff->days < $newProductFrom) {
                $htmlLabel .= '<div class="label-product label-new">New</div>';
            }
            if ($sale > 0) {
                $htmlLabel .= '<div class="label-product label-sale">Sale</div>';
            }

            return $htmlLabel;
        }

        public static function getProducts($productGroup, $maxItems, $showPrice, $filterCategory, $categoryIds) {
            $products = array();
            $productModel = VmModel::getModel('Product');
            if(is_array($categoryIds) && count($categoryIds) > 1 ) {
                foreach($categoryIds as $categoryId) {
                    if($maxItems > count($products)) {
                        $prods = $productModel->getProductListing($productGroup, ($maxItems - count($products)), $showPrice, true, false, $filterCategory, $categoryId);
                        $products = array_merge($prods, $products);
                    }
                }
            }else {
                //$products = $productModel->getProductListing($productGroup, $maxItems, $showPrice, true, false, $filterCategory, $categoryIds[0]);
                $products = $productModel->getProductListing($productGroup, $maxItems, $showPrice, true, false, $filterCategory, $categoryIds );
            }
            $productModel->addImages($products);
            return $products;
        }

        public static function getTabsText($key) {
            if($key == 'featured') return JText::_('MOD_ZTVIRTUEMARTER_PRODUCT_FEATURED_PRODUCTS');
            elseif($key == 'latest') return JText::_('MOD_ZTVIRTUEMARTER_PRODUCT_LATEST_PRODUCTS');
            elseif($key == 'random') return JText::_('MOD_ZTVIRTUEMARTER_PRODUCT_RANDOM_PRODUCTS');
            elseif($key == 'topten') return JText::_('MOD_ZTVIRTUEMARTER_PRODUCT_BEST_SALES');
            elseif($key == 'recent') return JText::_('MOD_ZTVIRTUEMARTER_PRODUCT_RECENT_PRODUCTS');
            else {
                $categoryModel = VmModel::getModel('category');
                $category = $categoryModel->getCategory($key);
                return isset($category->category_name) ? $category->category_name : $key;
            }
        }
    }
}
