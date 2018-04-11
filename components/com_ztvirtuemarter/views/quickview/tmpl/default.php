<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
    echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs', array('product' => $this->product));

vmJsApi::jDynUpdate();

vmJsApi::addJScript('updDynamicListeners', "
jQuery(document).ready(function() { // GALT: Start listening for dynamic content update.
	// If template is aware of dynamic update and provided a variable let's
	// set-up the event listeners.
	if (Virtuemart.container){
        try{
        Virtuemart.updateDynamicUpdateListeners();
        }
        catch(err) {
            // Handle error(s) here
        }
    }

}); ");
?>
    <div class="productdetails-view productdetails quickview-product">

        <?php // Back To Category Button
        if ($this->product->virtuemart_category_id) :
            $catURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
            $categoryName = $this->product->category_name;
        else :
            $catURL = JRoute::_('index.php?option=com_virtuemart');
            $categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME');
        endif;
        ?>

        <?php // afterDisplayTitle Event
        echo $this->product->event->afterDisplayTitle ?>

        <?php
        // Product Edit Link
        echo $this->edit_link;
        // Product Edit Link END
        ?>
        <div class="vm-product-container">
            <div class="vm-product-media-container">
                <?php
                echo $this->loadTemplate('images');
                $count_images = count($this->product->images);
                if ($count_images > 1) :
                    echo $this->loadTemplate('images_additional');
                endif;

                // event onContentBeforeDisplay
                echo $this->product->event->beforeDisplayContent;

                $sale = isset($this->product->prices['product_override_price']) ? $this->product->prices['product_override_price'] : '';
                $saleClass = ($sale > 0) ? 'product-sale' : '';
                ?>
            </div>

            <div class="vm-product-details-container">
                <div class="spacer-buy-area">

                    <?php // Product Title   ?>
                    <h1><?php echo $this->product->product_name ?></h1>

                    <div class="FlexibleStockNumber">Availability: <span class="stock">In Stock</span> <span
                            class="items"><?php echo $this->product->product_in_stock ?> items</span></div>

                    <?php // Product Title END   ?>
                    <?php
                    echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product));

                    if (is_array($this->productDisplayShipments)) :
                        foreach ($this->productDisplayShipments as $productDisplayShipment) :
                            echo $productDisplayShipment . '<br />';
                        endforeach;
                    endif;
                    if (is_array($this->productDisplayPayments)) :
                        foreach ($this->productDisplayPayments as $productDisplayPayment) :
                            echo $productDisplayPayment . '<br />';
                        endforeach;
                    endif;

                    //In case you are not happy using everywhere the same price display fromat, just create your own layout
                    //in override /html/fields and use as first parameter the name of your file
                    echo '<div class="product-price ' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $this->product, 'currency' => $this->currency)) . '</div>';
                    //echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
                    echo shopFunctionsF::renderVmSubLayout('addtocart', array('product' => $this->product));

                    echo shopFunctionsF::renderVmSubLayout('stockhandle', array('product' => $this->product));

                    // Ask a question about this product
                    if (VmConfig::get('ask_question', 0) == 1) :
                        $askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
                        ?>
                        <div class="ask-a-question">
                            <a class="ask-a-question" href="<?php echo $askquestion_url ?>"
                               rel="nofollow"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Manufacturer of the Product
                    if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) :
                        echo $this->loadTemplate('manufacturer');
                    endif;
                    ?>
                </div>
                <?php
                // Product Short Description
                if (!empty($this->product->product_s_desc)) : ?>
                    <div class="product-short-description">
                        <?php
                        /** @todo Test if content plugins modify the product description */
                        echo nl2br($this->product->product_s_desc);
                        ?>
                    </div>
                    <?php
                endif; // Product Short Description END

                echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'ontop'));
                ?>
            </div>
            <div class="clear"></div>
        </div>


        <div id="zt_tabs" class="tabs">
            <ul class="nav nav-tabs" role="tablist" id="myTab">
                <li class=""><a href="#tab1" role="tab" data-toggle="tab"><?php echo 'DESCRIPTION'; ?></a></li>
                <li class="active"><a href="#tab2" role="tab" data-toggle="tab"><?php echo 'REVIEWS'; ?></a></li>
            </ul>


            <div class="tab-content">
                <div class="tab-pane " id="tab1">
                    <?php
                    // Product Description
                    if (!empty($this->product->product_desc)) :
                        ?>
                        <div class="product-description">
                            <?php /** @todo Test if content plugins modify the product description */ ?>
                            <span class="title"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
                            <?php echo $this->product->product_desc; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane active" id="tab2">
                    <?php echo $this->loadTemplate('reviews'); ?>
                </div>
            </div>
        </div>
        <!--/zt_tabs-->

        <?php
        // Product Description
        if (!empty($this->product->product_desc)) {
            //no description
        }

        // Product Description END
        echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'normal'));

        // Product Packaging
        $product_packaging = '';
        if ($this->product->product_box) :
            ?>
            <div class="product-box">
                <?php
                echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') . $this->product->product_box;
                ?>
            </div>
        <?php endif; // Product Packaging END ?>

        <?php
        echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'onbot'));

        echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'related_products', 'class' => 'product-related-products', 'customTitle' => true));

        echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'related_categories', 'class' => 'product-related-categories'));

        // onContentAfterDisplay event
        echo $this->product->event->afterDisplayContent;

        echo vmJsApi::writeJS();
        ?>

    </div>
    <script>
        // GALT
        /*
         * Notice for Template Developers!
         * Templates must set a Virtuemart.container variable as it takes part in
         * dynamic content update.
         * This variable points to a topmost element that holds other content.
         */
        // If this <script> block goes right after the element itself there is no
        // need in ready() handler, which is much better.
        //jQuery(document).ready(function() {
        Virtuemart.container = jQuery('.productdetails-view');
        Virtuemart.containerSelector = '.productdetails-view';
        //Virtuemart.container = jQuery('.main');
        //Virtuemart.containerSelector = '.main';
        //});
    </script>
<?php if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_countdown == '1') : ?>
    <script>
        (function ($) {
            jQuery("#gallery_image-zoom-product").owlCarousel({
                items: 3,
                navigation: true
            });
            $("#image-zoom-product").elevateZoom({
                gallery: 'gallery_image-zoom-product',
                zoomType	: "inner",
                cursor: "crosshair",
                galleryActiveClass: 'active',
                loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'});
            $("#image-zoom-product").bind("click", function (e) {
                var ez = $('#image-zoom-product').data('elevateZoom');
                $.fancybox(ez.getGalleryList());
                return false;
            });

        })(jQuery)
    </script>
    <?php
endif;
die;
?>