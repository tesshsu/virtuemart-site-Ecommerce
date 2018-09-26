<?php
/**
 * @package     This is Plugin Enhanced Ecommerce Google Analytics for VirtueMart
 * @version     3.0.2
 * @author      Construction http://www.joomla-service.in.ua
 * @copyright   Copyright (C) 2016 joomla-service.in.ua
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);



class plgSystemEcommerceVmGoogleAnalytics extends JPlugin {


     function plgEcommerceGoogleAnalytics($subject, $config)
    {
        parent::__construct($subject, $config);
        $this->_plugin = JPluginHelper::getPlugin('system', 'EcommerceVmGoogleAnalytics');
        $this->_params = new JParameter($this->_plugin->params);
    
    }

function onBeforeRender(){

$app = JFactory::getApplication();

    if ($app->getName() != 'site' ) {
      return true;
    }
    
      $code = $this->params->get('code', '');
      $affiliation = $this->params->get('affiliation', '');


$input = JFactory::getApplication()->input;
       
       $extension_name = $input->get('option', '', 'cmd');
       $view = $input->get('view', '', 'cmd');
       $task = $input->get('task', '', 'cmd');

if (!class_exists( 'VmConfig' )) require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();




if (!class_exists('CurrencyDisplay'))
  require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();

$doc = JFactory::getDocument();
      $scripts_ga = "


(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '$code', 'auto');
            ga('send', 'pageview');
            ga('require', 'ec');
ga('set', '&cu', '$currency->_vendorCurrency_code_3');
";

$doc->addScriptDeclaration($scripts_ga);


if ($extension_name == 'com_virtuemart' && 'cart' == $view){
  

$cart = VirtueMartCart::getCart(false);

if(count($cart->products)>= 1){
$count_pos = 1;


foreach ($cart->products as $product){

$vm_version  = vmVersion::$RELEASE;
if($vm_version{0} == 3):
 $mf_name =  $product->mf_name;
else: 
 $model = VmModel::getModel('Manufacturer');
$manufacturers = $model->getManufacturers($product->virtuemart_manufacturer_id);
    
foreach ($manufacturers as   $mfname){

if($mfname->virtuemart_manufacturer_id==$product->virtuemart_manufacturer_id){

$mf_name = $mfname->mf_name;

  }
}
endif;


$scripts_cart = "
ga('ec:addProduct', {
            'id': '$product->virtuemart_product_id',
            'name': ' $product->product_name',
            'category': '$product->category_name',
            'brand': '$mf_name',   
            'quantity':$product->quantity,     
            'position':  $count_pos                   
});

";
$count_pos++;

$doc->addScriptDeclaration($scripts_cart);

}

$scripts_step = "
ga('ec:setAction', 'checkout', {'step':1});
ga('send', 'pageview');

";
$doc->addScriptDeclaration($scripts_step);


$payment_name = "";
if($cart->cartData['paymentName']){
$payment_name = $cart->cartData['paymentName'];
$payment_name = strip_tags($cart->cartData['paymentName']);

$script_step_payment = "

jQuery(document).ready(function(){

try {

ga('ec:setAction','checkout', {
    'step': 1,            
    'option': '$payment_name'    
});
ga('send', 'pageview');

} catch(e) { }


});

";

$doc->addScriptDeclaration($script_step_payment);
}else{


}

}




}


if ($extension_name == 'com_virtuemart' && 'productdetails' == $view){
        
$category_id = $input->get('virtuemart_category_id', '');
$product_id = $input->get('virtuemart_product_id', '');

if (!class_exists('CurrencyDisplay'))
  require_once(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();


$productModel = VmModel::getModel('product');

$ids = $productModel->sortSearchListQuery(TRUE);
$products = $productModel->getProducts ($ids);


$categoryModel = VmModel::getModel('category');
$category = $categoryModel->getCategory($category_id);




foreach ($products as $product) {  

if ($product->virtuemart_product_id ==$product_id) {



$scripts_detail = "
ga('ec:addProduct', {
            'id': '$product->virtuemart_product_id',
            'name': ' $product->product_name',
            'category': '$category->category_name',
            'brand': '$product->mf_name'              
                         
});
ga('ec:setAction', 'detail');      
ga('send', 'pageview');

";
$doc->addScriptDeclaration($scripts_detail);
}       
}


$add_to_cards = "

jQuery(document).ready(function(){
jQuery('[name=addtocart]').click(function(e){

var $this = jQuery(this);

var rParent = jQuery(this).parent().parent().parent();

var  id = rParent.find('input[name=\"virtuemart_product_id[]\"]').val();
 
pname = rParent.find('[name=pname]').val();
      
quantity = rParent.find('input[name=\"quantity[]\"]').val();


    var add_to_card = new Array();
 
    add_to_card = {
      'id': id,
      'name': pname,
      'category': 'Category ($category->category_name)',
      'quantity': quantity
    }
 
    ga('ec:addProduct', add_to_card);
    ga('ec:setAction', 'add');
    ga('send', 'event', 'UX', 'click', 'add to cart'); 
});

});

";

$doc->addScriptDeclaration($add_to_cards);

} 


if ($extension_name == 'com_virtuemart' && 'category' == $view) {


$category_id = $input->get('virtuemart_category_id', '');
$product_id = $input->get('virtuemart_product_id', '');


if (!class_exists('CurrencyDisplay'))
  require_once(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();

$productModel = VmModel::getModel('product');

$ids = $productModel->sortSearchListQuery(TRUE);
$products = $productModel->getProducts ($ids);


$categoryModel = VmModel::getModel('category');
$category = $categoryModel->getCategory($category_id);




$count_pos = 1;
foreach ($products as $product) 
{  

if ($product->virtuemart_category_id ==$category_id) {

$scripts_category = "
ga('ec:addImpression', {
            'id': '$product->virtuemart_product_id',
            'name': ' $product->product_name',
            'category': '$category->category_name',
            'brand': '$product->mf_name',  
            'list': 'Category ($category->category_name)',              
           'position':  $count_pos                  
});

";
$count_pos++;

$doc->addScriptDeclaration($scripts_category);

}
       
}

$scripts_category_pageview = "
     
ga('send', 'pageview');

";
$doc->addScriptDeclaration($scripts_category_pageview);


$add_to_cards = "

jQuery(document).ready(function(){
jQuery('[name=addtocart]').click(function(e){


var $this = jQuery(this);

var rParent = jQuery(this).parent().parent().parent();

var  id = rParent.find('input[name=\"virtuemart_product_id[]\"]').val();
 
pname = rParent.find('[name=pname]').val();
     
quantity = rParent.find('input[name=\"quantity[]\"]').val();


    var add_to_card = new Array();
 
    add_to_card = {
      'id': id,
      'name': pname,
      'category': 'Category ($category->category_name)',
      'quantity': quantity
    }
 
    ga('ec:addProduct', add_to_card);
    ga('ec:setAction', 'add');
    ga('send', 'event', 'UX', 'click', 'add to cart');

});
});

";

$doc->addScriptDeclaration($add_to_cards);

} 

}



   
function plgVmOnUpdateOrderPayment($order){

    $code = $this->params->get('code', '');

    if('R' == $order->order_status){


        VmInfo("Order number &nbsp;".$order->order_number."&nbsp; refund"."<script>


        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                    ga('create', '$code', 'auto');
                    ga('require', 'ec');


        ga('ec:setAction', 'refund', {
          'id': '$order->order_number',       
        });

        ga('send', 'pageview');

        </script>");

    }
    if('P' == $order->order_status){
       return false;
    }
}



function plgVmConfirmedOrder($cart, $order) {



$code = $this->params->get('code', '');
        $affiliation = $this->params->get('affiliation', '');

?>

<script>
<?php 

foreach( $order['items'] as $item ) { 

$categoryModel = VmModel::getModel('category');
$category = $categoryModel->getCategory($item->virtuemart_category_id);

$vm_version  = vmVersion::$RELEASE;
if($vm_version{0} == 3):

 $mf_name =  $item->mf_name;

else: 

$productModel = VmModel::getModel('product');

$ids = $productModel->sortSearchListQuery(TRUE);
$products = $productModel->getProducts ($ids);



foreach ($products as $product) {  


if ($product->virtuemart_product_id ==$item->virtuemart_product_id) {

$model = VmModel::getModel('Manufacturer');
$manufacturers = $model->getManufacturers($product->virtuemart_manufacturer_id);
    
foreach ($manufacturers as   $mfname){

if($mfname->virtuemart_manufacturer_id==$product->virtuemart_manufacturer_id){

$mf_name = $mfname->mf_name;

}
}
}
}

endif;

?>

ga('ec:addProduct', {

  'id': '<?php print $item->virtuemart_product_id;?>',
  'name': '<?php print $item->order_item_name;?>',
  'category': '<?php print $category->category_name;?>',
  'brand': '<?php print $mf_name;?>',
  'variant': '',
  'price': '<?php print $item->product_item_price;?>',
  'quantity': <?php print $item->product_quantity;?>
});

<?php }

 ?>


ga('ec:setAction', 'purchase', {
  id: '<?php print $order['details']['BT']->order_number;?>',
  affiliation: '<?php print $affiliation;?>',
  revenue: '<?php print $order['details']['BT']->order_total;?>',
  tax: '<?php print number_format($order['details']['BT']->order_tax,2);?>',
  shipping: '<?php print number_format($order['details']['BT']->order_shipment,2);?>'
  
});


ga('send', 'pageview');
</script>


<?php } } ?>