<?php defined('_JEXEC') or die('Restricted access');

$related = $viewData['related'];
$customfield = $viewData['customfield'];
$thumb = $viewData['thumb'];
//$showRating = $viewData['showRating'];
$ratingModel = VmModel::getModel('ratings');
$rating = $ratingModel->getRatingByProduct($related->virtuemart_product_id);
$reviews = $ratingModel->getReviewsByProduct($related->virtuemart_product_id);

$isSale = (!empty($related->prices['salesPriceWithDiscount'])) ? 1 : 0;
//juri::root() For whatever reason, we used this here, maybe it was for the mails
 
if ($isSale) {
		$dtaxs = array();
		if($related->prices["DATax"]) $dtaxs = $related->prices["DATax"];
		if($related->prices["DBTax"]) $dtaxs = $related->prices["DBTax"];			
		foreach($dtaxs as $dtax){
			if(!empty($dtax)) {
				$discount = rtrim(rtrim($dtax[1],'0'),'.');
				$operation = $dtax[2];
				$percentage = "";					
				switch($operation) {
					case '-':
						$percentage = "-".$discount;
						break;
					case '+':
						$percentage = "+".$discount;
						break;
					case '-%':
						$percentage = "-".$discount."%";
						break;
					case '+%':
						$percentage = "+".$discount."%";
						break;
					default:
						return true;	
				}
                $related->percentage = $percentage;	
			}					
		}
	}   
$features =   $related->product_special; 
 
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
 
	<div class="images-container">
		<div class="product-hover">
			<?php echo $htmlLabel; ?>
			<a href="#" class="overlay"></a> 
            <?php   
                echo JHtml::link (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id), $thumb , array('class' => 'product-image', 'title' => $related->product_name,'target'=>'_blank'));
               
            ?>
		</div>
		<div class="actions-no hover-box">
			<div class="actions">
			<?php 	echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$related)); ?> 
				<ul class="add-to-links pull-left-none">
					<?php echo plgSystemZtvirtuemarter::addQuickviewButton($related);?>
                    <?php plgSystemZtvirtuemarter::addWishlistButton($related); ?>
                    <?php plgSystemZtvirtuemarter::addCompareButton($related); ?>
				</ul>
			</div>
		</div>
	</div>
     <div class="products-textlink clearfix">
		<h2 class="product-name">
			<?php echo JHtml::link (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id), $related->product_name, array('title' => $related->product_name,'target'=>'_blank')); ?>
		</h2>
		<div class="price-box">
			<span class="regular-price">
				<span class="price">
                <?php     
                    $currency = calculationHelper::getInstance()->_currencyDisplay;
            	    echo shopFunctionsF::renderVmSubLayout('prices', array('product' => $related, 'currency' => $currency)) ;
            	?> 
                </span>                                   
			</span>
		</div>
	</div> 