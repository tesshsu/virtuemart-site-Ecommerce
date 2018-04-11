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
$doc->addStyleSheet( 'https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );

?>

<div class="vmgroup <?php echo $params->get ('moduleclass_sfx') ?> tabs-product">

    <?php if ($headerText) : ?>
        <div class="vmheader"><?php echo $headerText ?></div>
    <?php endif; ?> 
    <div role="tabpanel" class="product-wapper-tab clearfix">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs" role="tablist" id="dscr_tab">
    		<?php
            $first = true;   // last row  
            $val_delay = 1;
            foreach($products as $key => $value):
                ?>
                <li role="presentation" class="<?php echo ($first) ? 'active' : '' ?>"><a href="#producttabs-<?php echo $key ?>" aria-controls="producttabs-<?php echo $key ?>" role="tab" data-toggle="tab" aria-expanded="false"><?php echo ModZtvirtuemarterProductHelper::getTabsText($key); ?></a></li>
                <?php $first = false; ?>
                <?php
            endforeach;
            ?>
        </ul>
    	
    	<!-- Tab panes -->
    	<div class="tab-content">
        <?php
        $first = true;
        foreach ($products as $key => $items) :
        $group_nr = 1;                  // first group number
        $last_row = count($rows) -1; 
        
            ?>
    		<div role="tabpanel" class="producttabs tab-pane fade in owl-carousel owl-theme <?php echo ($first) ? 'active' : '' ?>" id="producttabs-<?php echo $key ?>">
                <?php foreach ($items as $id => $product) : ?>
             <?php  $val_delay = $val_delay + 1; 
            $delay = $val_delay*100; ?>
             <?php if ($id % $productsPerRow == 0) {print '<div class="item wow fadeInUp  animated" data-wow-duration="1s" data-wow-delay="'.$delay.'ms">'; $i = 0; $group_nr++; } ?>
            <div class="product-item">
                <div class="per-product">
        			<div class="images-container">
                        <div class="product-hover">
                            <?php 
                                $features =   $product->product_special;  
                                $htmlLabel = '';
                                $dateDiff = date_diff(date_create(), date_create($product->product_available_date)); 
                                if ($features) {
                    				//$htmlLabel .= '<span class="sticker top-left"><span class="hotsale">New</span></span>';
                    			}
                                if ($dateDiff->days < 7) {
                                    //$htmlLabel .= '<span class="sticker top-right"><span class="labelnew">New</span></span>';
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
                                    <?php //FLORIAN CAMELEONS : bloquer le changement d'image en hover sur la page d'accueil
                                    //echo $product->images[1]->displayMediaThumb ('class="img-responsive"', FALSE); ?>
                                    </span>
                                <?php  
        							} 
                                ?>
        					</a>
                        </div>
                        <div class="actions-no hover-box">
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
                        <p class="product-category"><?php echo JHtml::link ($product->link.$ItemidStr, $product->category_name); ?></p>
						<div class="price-box"> 
                            <?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
            			</div> 
                    </div> 
                </div>
            </div>
  <?php $i++; if ($i == $productsPerRow || $id == $last_row) print '</div>'; ?>
                <?php endforeach; ?>
                <?php $first = false; ?>
            </div>
        <?php endforeach; ?>
    	</div>
        
            <script>
                jQuery(document).ready(function(){ 
                    jQuery(".producttabs").owlCarousel({ 
                         responsive:{
                            0:{
                                items:1 
                            },
                            480:{
                                items:2 
                            },
                            768:{
                                items:3 
                            },
                            1200:{
                                items:4,
                                loop:false
                            }
                        },
                        nav:true,
                        dots: false,
                        loop:true,
                        margin:25,
                        autoplay:true,
                        autoplayTimeout:2500,
                        autoplayHoverPause:true
                    });
                });

            </script>
    </div> 
    
    
     
    <div class="clear"></div>
    <?php
    if ($footerText) : ?>
        <div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
            <?php echo $footerText ?>
        </div>
    <?php endif; ?>
</div> 
