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
<div class="product-detail">
    <div class="product-view">
        <div class="product-essential"> 

        <?php
        // Product Navigation
        if (VmConfig::get('product_navigation', 1)) {
    	?>
            <div class="product-neighbours">
    	    <?php
    	    if (!empty($this->product->neighbours ['previous'][0])) {
    		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
    		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
    			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
    	    }
    	    if (!empty($this->product->neighbours ['next'][0])) {
    		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
    		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
    	    }
    	    ?>
        	<div class="clear"></div>
            </div>
        <?php } // Product Navigation END 
        ?>   
            <div class="product-img-box clearfix col-md-5 col-sm-5 col-xs-12">
                <?php    echo $this->loadTemplate('images');  ?>
            </div>
            <div class="product-shop col-md-7 col-sm-7 col-xs-12">
                <div class="product-shop-content">
                    <div class="product-name"><h2><?php echo $this->product->product_name ?></h2></div>
                    <?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product)); ?>
                     <div class="product-type-data"> 
                        <div class="price-box">
                            <?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency)); ?>
                         </div> 
                    </div> 
                    <?php 	if (!empty($this->product->product_s_desc)):  ?> 
                    <div class="short-description" style="max-width: 520px;">
                    	       <?php  echo nl2br($this->product->product_s_desc);  ?>  
                    </div> 
                    <?php endif; ?> 
                    <div class="product-options-bottom">
                        <?php 	echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product)); ?>
                        
                        <div class="add-to-links"> 
                            <?php plgSystemZtvirtuemarter::addWishlistButton($this->product); ?>
                            <?php plgSystemZtvirtuemarter::addCompareButton($this->product); ?>
            			</div>
        		    </div>
        		
                </div>
            </div> 
        <div class="clr"></div>
        <div role="tabpanel" class="product-detail-tab col-xs-12">
        	<!-- Nav tabs -->
        	<ul class="nav nav-tabs" role="tablist" id="dscr_tab">
        		<li role="presentation" class="active"><a href="#prod_dscr_quickview" aria-controls="prod_dscr_quickview" role="tab" data-toggle="tab" aria-expanded="true">Description</a></li>
        		<li role="presentation" class=""><a href="#prod_reviews_quickview" aria-controls="prod_reviews" role="tab" data-toggle="tab" aria-expanded="false"><?php echo vmText::_( 'COM_VIRTUEMART_WRITE_REVIEW' ); ?></a></li>
            </ul>
        	
        	<!-- Tab panes -->
        	<div class="tab-content">
        		<div role="tabpanel" class="tab-pane fade active in" id="prod_dscr_quickview"  style="max-width: 789px;">
        			<?php  echo $this->product->product_desc; ?>
        		</div>
        		<div role="tabpanel" class="tab-pane fade" id="prod_reviews_quickview">
        			 <?php echo $this->loadTemplate('reviews');?>
        		</div> 
        	</div>
        </div> 
        </div>
    </div>
</div>
 
        <?php
    // onContentAfterDisplay event
        echo $this->product->event->afterDisplayContent;

        echo vmJsApi::writeJS();
        ?>
 
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
 
<?php 
die;
?>