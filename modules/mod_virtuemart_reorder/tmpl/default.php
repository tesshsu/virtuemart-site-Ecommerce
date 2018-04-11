<?php 
defined('_JEXEC') or die('Restricted access');
/**
 * @package	Joomla.Site
 * @subpackage	mod_virtuemart_reorder
 * @copyright	Copyright (C) EasyJoomla.org. All rights reserved.
 * @author      Jan Linhart
 * @license	GNU General Public License version 2 or later
 */

if(isset($products) && is_array($products) && count($products) > 0){
?>
<div class="vmReorderModule <?php echo $moduleclass_sfx; ?>" id="vmReorderModule">
  <form method="post" class="product js-recalculate" action="index.php" id="vmReorderModuleForm">
    <p><?php echo $pretext; ?></p>
    <div class="addtocart-bar">
      <span class="addtocart-button">
        <i class='fa fa-cart-arrow-down' aria-hidden='true'></i>
        <input type="submit" name="addtocart" class="addtocart-button" value="<?php echo $buttontext; ?>" title="<?php echo $buttontext; ?>">
      </span>
    </div>
    <?php 
    foreach($products as $key => $product){
      $addToCartUrl = JURI::root().'?option=com_virtuemart&controller=cart&task=addJS&virtuemart_product_id[]='.$product->virtuemart_product_id.'&quantity[]='.$product->product_quantity;
      // echo "<pre>";var_dump($product);echo "</pre>";
      if(is_array($product->attributes) && count($product->attributes) > 0)
      {
        foreach($product->attributes as $attribute)
        {
          $addToCartUrl .= '&customPrice['.$key.']['.$attribute->custom_id.']='.$attribute->id;;
          ?>
          <input type="hidden" name="customPrice[<?php echo $key; ?>][<?php echo $attribute->custom_id; ?>]" value="<?php echo $attribute->id; ?>">
          <?php
        }
      }
    ?>
    <input type="hidden" name="quantity[]" value="<?php echo $product->product_quantity; ?>">
    <input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id; ?>">
    <input type="hidden" name="reorderUrl" class="reorderUrl" value="<?php echo $addToCartUrl; ?>">
    <?php } ?>
    <input type="hidden" name="option" value="com_virtuemart">
    <input type="hidden" name="view" value="cart">
    <input type="hidden" name="task" value="add">
    <input type="hidden" name="virtuemart_manufacturer_id" value="Array">
  </form>
</div>

<script type="text/javascript">

// if jQuery isn't loaded, load it.
if (typeof jQuery == 'undefined') {
  
  function getScript(url, success) {
  
    var script = document.createElement('script');
    script.src = url;
    var head = document.getElementsByTagName('head')[0],
    done = false;
    
    // Attach handlers for all browsers
    script.onload = script.onreadystatechange = function() {
      if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
      done = true;
        success();
        script.onload = script.onreadystatechange = null;
        head.removeChild(script);
      };
    };
    head.appendChild(script);
  };
  
  getScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js');
  
}

// jQuery was already loaded
jQuery('#vmReorderModuleForm').submit(function(event){
    event.preventDefault();
    var productToReorder = jQuery('.reorderUrl');
    var productCount = productToReorder.length;
    var productCounter = 0;
    jQuery('.reorderUrl').each(function(){
        var productUrl = jQuery(this).val();
        jQuery.ajax({
            url: productUrl,
            async: false,
            context: document.body,
            success: function(resultData){
                var result = jQuery.parseJSON(resultData);
                productCounter++;
                jQuery('#vmReorderModule').append('<div class="alert alert-success">'+result.msg+'<div>');
                if(productCounter == productCount){
                    window.location.href = "<?php echo JURI::root() ?>?option=com_virtuemart&view=cart";
                }
            }
        });
    });
});

</script>

<?php }

