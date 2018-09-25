<?php // no direct access
defined ('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();

$col = 1;
$pwidth = ' width' . floor (100 / $products_per_row);
if ($products_per_row > 1) {
	$float = "floatleft";
} else {
	$float = "center";
}
?>
<div class="merge-product vmgroup<?php echo $params->get ('moduleclass_sfx') ?>">

	<?php if ($headerText) { ?>
	<div class="vmheader"><?php echo $headerText ?></div>
	<?php
}
	if ($display_style == "div") {
		?>
		<ul class="list-inline vmproduct<?php echo $params->get ('moduleclass_sfx'); ?> productdetails">
			<?php foreach ($products as $product) { ?>
			<li class="<?php echo $pwidth ?> <?php echo $float ?>">
					<?php
					echo '<div class="image-block">';
					if (!empty($product->images[0])) {
						$image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
					} else {
						$image = '';
					}
					echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
					echo '</div>';
					echo '<div class="productdetails">';
					$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .$product->virtuemart_category_id); ?>
					<h3 class="product-name"><a href="<?php echo $url ?>"><?php echo $product->product_name ?></a></h3>
					<p class="product-category"><?php echo JHtml::link ($product->link.$ItemidStr, $product->category_name); ?></p>					
			</li>
			<?php
			
		} ?>
		</ul>
		<?php
	} else {
		$last = count ($products) - 1;
		?>

		<ul class="vmproduct<?php echo $params->get ('moduleclass_sfx'); ?> productdetails">
			<?php foreach ($products as $product) : ?>
			<li class="<?php echo $pwidth ?> <?php echo $float ?>">
				<?php
				echo '<div class="image-block">';
				if (!empty($product->images[0])) {
					$image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
				} else {
					$image = '';
				}
				echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
				echo '</div>';
				echo '<div class="productdetails">';
				$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .$product->virtuemart_category_id); ?>
				<h3 class="product-name"><a href="<?php echo $url ?>"><?php echo $product->product_name ?></a></h3>
				<?php echo '';
				// $product->prices is not set when show_prices in config is unchecked
				if ($show_price and  isset($product->prices)) {
					echo '<div class="price-box">';
					echo '<div class="product-price">'.$currency->createPriceDiv ('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
					if ($product->prices['salesPriceWithDiscount'] > 0) {
						echo $currency->createPriceDiv ('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
					}
					echo '</div>';
					echo '</div';
				}
				if ($show_addtocart) {
					echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product));
				}
				echo '</div>';
				?>
			</li>
			<?php
			if ($col == $products_per_row && $products_per_row && $last) {
				echo '
		</ul>
		<ul  class="vmproduct' . $params->get ('moduleclass_sfx') . ' productdetails">';
				$col = 1;
			} else {
				$col++;
			}
			$last--;
		endforeach; ?>
		</ul>
		

		<?php
	}
	if ($footerText) : ?>
		<div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
			<?php echo $footerText ?>
		</div>
		<?php endif; ?>
</div>