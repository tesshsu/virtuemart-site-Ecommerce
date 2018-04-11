<?php
defined('_JEXEC') or die;

class ZtvirtuemarterViewAjaxcart extends JViewLegacy
{
    public $products;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        if (!class_exists('VirtueMartCart'))
            require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');

        VmConfig::loadConfig();
        $cart = VirtueMartCart::getCart();
        $cart -> prepareCartData();


        $data = new stdClass();
        $data->products = array();
        $data->totalProduct = 0;

        //OSP when prices removed needed to format billTotal for AJAX
        if (!class_exists('CurrencyDisplay'))
            require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
        $currencyDisplay = CurrencyDisplay::getInstance();

        foreach ($cart->products as $i=>$product){

            $category_id = $cart->getCardCategoryId($product->virtuemart_product_id);

            //Create product URL
            $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$category_id, FALSE);
            $data->products[$i]['product_name'] = JHtml::link($url, $product->product_name);

            if(!class_exists('VirtueMartModelCustomfields'))require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');

            //  custom product fields display for cart
            $data->products[$i]['customProductData'] = VirtueMartModelCustomfields::CustomsFieldCartModDisplay($product);
            $data->products[$i]['product_sku'] = $product->product_sku;
            $data->products[$i]['prices'] = $currencyDisplay->priceDisplay( $product->allPrices[$product->selectedPrice]['subtotal']);
            if(!isset($product->images[0])){
                $productModel = VmModel::getModel('Product');
                $productModel->addImages($product, 1);
                $data->products[$i]['image'] = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
                // other possible option to use for display
            } else {
                    $data->products[$i]['image'] = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
                }
            // other possible option to use for display
            $data->products[$i]['subtotal'] = $currencyDisplay->priceDisplay($product->allPrices[$product->selectedPrice]['subtotal']);
            $data->products[$i]['subtotal_tax_amount'] = $currencyDisplay->priceDisplay($product->allPrices[$product->selectedPrice]['subtotal_tax_amount']);
            $data->products[$i]['subtotal_discount'] = $currencyDisplay->priceDisplay( $product->allPrices[$product->selectedPrice]['subtotal_discount']);
            $data->products[$i]['subtotal_with_tax'] = $currencyDisplay->priceDisplay($product->allPrices[$product->selectedPrice]['subtotal_with_tax']);

            // UPDATE CART / DELETE FROM CART
            $data->products[$i]['quantity'] = $product->quantity;
            $data->totalProduct += $product->quantity ;

        }

        if(empty($cart->cartPrices['billTotal']) or $cart->cartPrices['billTotal'] < 0){
            $cart->cartPrices['billTotal'] = 0.0;
        }

        $data->billTotal = $currencyDisplay->priceDisplay( $cart->cartPrices['billTotal'] );
        $data->dataValidated = $cart->_dataValidated ;

        if ($data->totalProduct>1) $data->totalProductTxt = vmText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', $data->totalProduct);
        else if ($data->totalProduct == 1) $data->totalProductTxt = vmText::_('COM_VIRTUEMART_CART_ONE_PRODUCT');
        else $data->totalProductTxt = vmText::_('COM_VIRTUEMART_EMPTY_CART');
        if (false && $data->dataValidated == true) {
            $taskRoute = '&task=confirm';
            $linkName = vmText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
        } else {
            $taskRoute = '';
            $linkName = vmText::_('COM_VIRTUEMART_CART_SHOW');
        }

        $data->cart_show = '<a style ="float:right;" href="'.JRoute::_("index.php?option=com_virtuemart&view=cart".$taskRoute,$cart->useSSL).'" rel="nofollow" >'.$linkName.'</a>';
        $data->billTotal = vmText::sprintf('COM_VIRTUEMART_CART_TOTALP',$data->billTotal);

        echo json_encode($data);
        Jexit();
    }
}
