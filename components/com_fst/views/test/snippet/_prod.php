<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php if ($product && $product['title'] != ""): ?>
<div class='comment_product' >
	<?php if ($product['image'] && substr($product['image'],0,1) != "/"): ?>
	<div class='kb_product_image'>
	    <img src='<?php echo JURI::root( true ); ?>/images/fst/products/<?php echo $product['image']; ?>' width='64' height='64'>
	</div>
	<?php elseif ($product['image']) : ?>
		<div class='kb_product_image'>
			<img src='<?php echo JURI::root( true ); ?><?php echo $product['image']; ?>' width='64' height='64'>
		</div>
	<?php endif; ?>
	<div class='kb_product_head <?php if ($this->test_show_prod_mode == "accordian"): ?>accordion_toggler_1<?php endif; ?>'>
		<?php $endlink = false; if (empty($hideprodlink)): ?>
			<?php if ($this->test_show_prod_mode == "accordian"): ?>
		<a class="fst_highlight" href="#" onclick='return false;'>
				<?php $endlink = true; ?>
			<?php elseif ($this->test_show_prod_mode != "inline"): ?>
				<a class='fst_highlight' href='<?php echo FSTRoute::x( '&prodid=' . $product['id'] );?>'>
				<?php $endlink = true; ?>
			<?php endif; ?>	
		<?php endif; ?>	
			<?php echo $product['title'] ?>
		<?php if ($endlink): ?>
			</a>
		<?php endif; ?>	
	</div>
	<div class='kb_product_desc'><?php echo $product['description']; ?></div>
<div class='fst_clear'></div></div>
<?php endif; ?>
