<?php // no direct access
defined('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();

$productNumber = $jinput->get('product', 0, 'INT');
$productss = array();

$num = $productsPerRow;
if ($productNumber != 0) {
    $num = intval($productNumber) + $productsPerRow;
    if ($num > count($products)) {
        $num = count($products);
    }
    for ($i = 0; $i < $num; $i++) {
        $productss[$i] = $products[$i];
    }
    require('subtmpl/load_ajax.php');
    die;
}
if ($num > count($products)) {
    $num = count($products);
}
for ($i = 0; $i < $num; $i++) {
    $productss[$i] = $products[$i];
}

?>
<div id="products_require">
    <?php
    require('subtmpl/load_ajax.php');
    ?>
</div>
<input type="hidden" class="base_url" value="<?php echo JURI::root(); ?>"/>
<input type="hidden" class="num_plus" value="<?php echo $productsPerRow; ?>"/>
<div class="more-product">
    <a class="more_product readmore" style="cursor:pointer">More Products...</a>
</div>
<!--ajax-->
<script type="text/javascript">
    jQuery(document).ready(function () {
        var base_url = jQuery('.base_url').val();
        var product_per_row = parseInt(jQuery('.num_plus').val());
        jQuery('.more_product').click(function () {
            jQuery.fancybox.showActivity();
            var num = parseInt(jQuery('.num_plus').val());
            jQuery.ajax({
                url: 'index.php?product=' + num,
                type: 'POST',
                cache: false,
                data: 'product=' + num,
                success: function (string) {
                    num = num + product_per_row;
                    if (string) {
                        jQuery('#products_require').html('');
                        jQuery('#products_require').append(string);
                        jQuery('.num_plus').val(num);
                    }

                    var show_quicktext = "Quick View";

                    jQuery(".zt-product-content").each(function (indx, element) {
                        var my_product_id = jQuery(this).find(".quick_ids").val();
                        if (my_product_id) {
                            jQuery(this).append("<div class=\'quick_btn\' onClick =\'quick_btn(" + my_product_id + ")\'><i class=\'fa fa-search\'></i><span>" + show_quicktext + "</span></div>");
                        }
                        jQuery(this).find(".quick_id").remove();
                    });

                    Virtuemart.product(jQuery("form.product"));
                    jQuery.fancybox.hideActivity();
                },
                error: function () {
                    alert("Can not get more product!");
                }
            });
        });
    });
</script>
