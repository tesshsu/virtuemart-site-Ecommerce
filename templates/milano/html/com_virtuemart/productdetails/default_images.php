<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_images.php 8657 2015-01-19 19:16:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); 

 
$isSale = (!empty($this->product->prices['salesPriceWithDiscount'])) ? 1 : 0; 
if ($isSale) {
		$dtaxs = array();
		if($this->product->prices["DATax"]) $dtaxs = $this->product->prices["DATax"];
		if($this->product->prices["DBTax"]) $dtaxs = $this->product->prices["DBTax"];			
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
                $this->product->percentage = $percentage;	
			}					
		}
	}  
$features =   $this->product->product_special; 
 $htmlLabel = '';
$dateDiff = date_diff(date_create(), date_create($this->product->product_available_date)); 
if ($features) {
	//$htmlLabel .= '<span class="sticker top-left"><span class="hotsale">New</span></span>';
}
if ($dateDiff->days < 7) {
    //$htmlLabel .= '<span class="sticker top-right"><span class="labelnew">New</span></span>';
}
if (isset($this->product->percentage)) {
    $htmlLabel .= '<span class="sticker top-right"><span class="labelsale">'.$this->product->percentage.'</span></span>';
} 


?>
	
         
<div class="product-img-content">
    <div class="product-image product-image-zoom">
        <div class="product-image-gallery">  
        	
             <?php
             echo $htmlLabel;
    	//$start_image = VmConfig::get('add_img_main', 1) ? 0 : 1; 
    	$start_image = 1; //BRUNO CAMELEON, On affichage la seconde image
		$image1 = $this->product->images[$start_image];

		?>
			<a id="image-main-link" href="<?php echo JURI::root().$image1->file_url; ?>">
				<img  id ="image-main" data-zoom-image ="<?php echo JURI::root().$image1->file_url; ?>" class="gallery-image visible  img-responsive" src="<?php echo JURI::root().$image1->file_url; ?>" alt="Fusce nec facilisi" title="Fusce nec facilisi"  />
			</a>
     
        </div>
    </div>
    <div class="more-views">
        <div class="product-image-thumbs owl-carousel owl-theme viewMore_img" id ="gal_01"> 
  <?php
    	$start_image = VmConfig::get('add_img_main', 1) ? 0 : 1;
    	for ($i = $start_image; $i < count($this->product->images); $i++) {
		$image = $this->product->images[$i];
		?>
        <div class ="item">
            <a class="thumb-link" data-image="<?php echo JURI::root().$image->file_url; ?>" data-zoom-image ="<?php echo JURI::root().$image->file_url; ?>" href="#" title="" >
                 <img class="img-responsive" src="<?php echo JURI::root().$image->file_url; ?>" alt="images" /> 
                 </a>
            </div> 
    	<?php
    	}
    	?> 
                  </div>
    
    </div>
</div>
<?php /*
<div class="listeProduitsTitre">Dans la même catégorie <i class="fa fa-caret-down" aria-hidden="true"></i></div>
<ul class="listeProduitsCat">
	<?php 
		 $productModel = VmModel::getModel('product');
		 $products = $productModel->getProductsInCategory($this->product->virtuemart_category_id);
		 //var_dump($products);
		 foreach ($products as $key => $product) {
		 	echo '<li>';
		 	$mediaModel = VmModel::getModel('media');
    		$media = $mediaModel->createMediaByIds($product->virtuemart_media_id)[0];
    		echo $media->displayMediaThumb ('', FALSE);
		 	echo JHtml::link ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id, $product->product_name);
		 	echo "</li>";
		 } 
	 ?>
</ul>
<?php */ 

$config = new JConfig();
$db_product = new PDO('mysql:host=localhost;dbname='.$config->db, $config->user, $config->password);
$product_request = $db_product->query('SELECT virtuemart_product_id 
								FROM h8q2p_virtuemart_product_categories
								WHERE virtuemart_category_id = '.$this->product->virtuemart_category_id);
$allProducts = array();
?>
<div class="listeProduitsTitre"><?php echo(vmText::_ ('TPL_MILANO_SAME_CATEGORY')) ?><i class="fa fa-chevron-down" aria-hidden="true"></i></div>
<ul class="listeProduitsCat">
	<?php 
	while($product_data = $product_request->fetch()) {
		//$user_group = $group_data['virtuemart_shoppergroup_id'];
		$productModel = VmModel::getModel('product');
		$product =$productModel->getProduct($product_data['virtuemart_product_id']);
		if($product->product_name != '') {
			
			$allProducts[] = array($product->product_name,$product->virtuemart_product_id);
			
		}
	}

	sort($allProducts);

	foreach ($allProducts as $productData) {
		echo '<li>';
		echo JHtml::link ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$productData[1], $productData[0]);
		echo "</li>";	
	}

	 ?>
</ul>