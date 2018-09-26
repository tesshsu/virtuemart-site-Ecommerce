<?php // no direct access
defined('_JEXEC') or die('Restricted access');
vmJsApi::jPrice();

?>
<div class="signle-product <?php echo $params->get('moduleclass_sfx') ?>">

    <?php if ($headerText) : ?>
        <div class="vmheader"><?php echo $headerText ?></div>
    <?php endif; ?>
        <?php $i = 0; ?>
        <?php foreach ($products as $product) : ?>
            <?php $i++; ?>
            <div class="item">
                <div class="product-item"> 
                    <div class="per-product">
                        <div class="images-container">
                            <div class="product-hover">
                                <?php 
                                    $features =   $product->product_special;  
                                    $htmlLabel = '';
                                    $dateDiff = date_diff(date_create(), date_create($product->product_available_date)); 
                                    if ($features) {
                        				$htmlLabel .= '<span class="sticker top-left"><span class="hotsale">New</span></span>';
                        			}
                                    if ($dateDiff->days < 7) {
                                        $htmlLabel .= '<span class="sticker top-right"><span class="labelnew">New</span></span>';
                                    }
                                    if (isset($product->percentage)) {
                                        $htmlLabel .= '<span class="sticker top-right"><span class="labelsale">'.$product->percentage.'</span></span>';
                                    } 
                                    echo $htmlLabel;
                                ?> 
                                <a class="overlay" href="#"></a>
            					<a class="product-image" title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
            						<?php
            						echo $product->images[0]->displayMediaThumb('class="img-responsive"', false);
                                    if (!empty($product->images[1])) { 
            						?>
                                        <span class="product-img-back">
                                        <?php echo $product->images[1]->displayMediaThumb ('class="img-responsive"', FALSE); ?>
                                        </span>
                                    <?php  
            							} 
                                    ?>
            					</a>
                            </div>
                            <div class="actions-no hover-box btn-cart-tyle2">
                                <div class="actions">
                                <?php 	echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row])); ?>
                					<?php echo plgSystemZtvirtuemarter::addQuickviewButton($product);?>
                                    <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                                    <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                				</div>
                            </div>
            			</div> 
                        <div class="products-textlink clearfix">
                        <h2 class="product-name"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h2>
                        <div class="price-box"> 
                            <?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
            			</div> 
                    </div> 
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        <?php $width = 100/$i; ?>
        <?php if ($footerText) : ?>
            <div class="vmheader"><?php echo $footerText ?></div>
        <?php endif; ?> 
</div>
<style>
.signle-product.<?php echo str_replace(' ', '', $params->get('moduleclass_sfx')); ?> {
    overflow: hidden;
    margin: 0 -10px;
}
.signle-product.<?php echo str_replace(' ', '', $params->get('moduleclass_sfx')); ?> .item{
    width: <?php echo $width; ?>%;
    float: left;
    padding: 0 10px;
} 
</style>