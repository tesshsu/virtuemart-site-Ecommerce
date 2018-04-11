<?php
/*
# ------------------------------------------------------------------------
# Ulathemes Article Carousel
# ------------------------------------------------------------------------
# Copyright(C) 2014 www.ulathemes.com. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: ulathemes.com
# Websites: http://ulathemes.com
# ------------------------------------------------------------------------
*/
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addScript('modules/' . $module->module . '/assets/js/owl.carousel.js', 'text/javascript');
$doc->addStyleSheet('modules/' . $module->module . '/assets/css/owl.carousel.min.css');
$doc->addStyleSheet('modules/' . $module->module . '/assets/css/owl.theme.default.min.css');

$thumb = JURI::base() . 'modules/mod_ulathemes_carousel_content/libs/timthumb.php?a=c&q=99&z=0&w='.$imagegWidth.'&h='.$imagegHeight;
?>
<style type="text/css">
#ulathemes-carousel-content<?php echo $module->id; ?> {
	<?php echo ($bgImage != '') ? "background: url({$bgImage}) repeat scroll 0 0;" : ''; ?>
	<?php echo ($isBgColor) ? "background-color: {$bgColor};" : '';?>
}
</style>
<div id="ulathemes-carousel-content<?php echo $module->id; ?>" class="ulathemes-carousel-content owl-carousel owl-theme <?php echo $classSuffix; ?>">
	<!-- Items Block -->
	<?php 
		foreach ($list as $item) :
			$title 	= $item->title;
			$link   = $item->link;
			$images = json_decode($item->images);
			$image 	= $images->image_fulltext;
			$image  = (empty($image)) ? $images->image_intro : $image;
			$image 	= (strpos($image, 'http://') === FALSE) ? JURI::base() . $image : $image;
			$image 	= ($resizeImage) ? $thumb . '&src=' . $image : $image;
			$category 	= $item->displayCategoryTitle;
			$hits  		= $item->displayHits;
			$introtext 	= $item->displayIntrotext;
			$created   	= $item->displayDate;
	?>
	<div class="item">
		<!-- Image Block -->
		<?php if($showImage && isset($images)) : ?>
		<div class="image-block">
			<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
				<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>"/>
			</a>
		</div>
		<?php endif; ?>
		
		<!-- Text Block -->
		<?php if($showTitle || $introText || $showCategory || $showCreatedDate || $showHits || $readmore) : ?>
		<div class="text-block">
			<!-- Title Block -->
			<?php if($showTitle) :?>
			<h3 class="title">
				<a href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
			</h3>
			<?php endif; ?>
			
			<!-- Info Block -->
			<?php if($showCategory || $showCreatedDate || $showHits) : ?>
			<div class="info">
				<?php if($showCreatedDate) : ?>
				<span><?php echo JTEXT::_('ULATHEMES_PUBLISHED'); ?>: <?php echo JHTML::_('date', $created, 'F d, Y');?></span>
				<?php endif; ?>
				
				<?php if($showCategory) : ?>
				<span><?php echo JTEXT::_('ULATHEMES_CATEGORY'); ?>: <?php echo $category; ?></span>
				<?php endif; ?>
				
				<?php if($showHits) : ?>
				<span><?php echo JTEXT::_('ULATHEMES_HITS'); ?>: <?php echo $hits; ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			
			<!-- Intro text Block -->
			<?php if($introText) : ?>
			<div class="introtext"><?php echo $introtext; ?></div>
			<?php endif; ?>
			
			<!-- Readmore Block -->
			<?php if($readmore) : ?>
			<div class="readmore">
				<a class="buttonlight morebutton" href="<?php echo $link; ?>" title="<?php echo $title; ?>">
					<?php echo JText::_('ULATHEMES_READ_MORE'); ?>
				</a>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#ulathemes-carousel-content<?php echo $module->id; ?>').owlCarousel({
		margin: <?php echo $itemsSpace; ?>,
		autoplay: <?php echo $autoPlay ? 'true' : 'false'; ?>,
		autoplayTimeout: <?php echo $autoPlayTimeout; ?>,
		autoplayHoverPause: <?php echo $pauseOnHover ? 'true' : 'false'; ?>,
		loop: <?php echo $loop ? 'true' : 'false'; ?>,
		mouseDrag: <?php echo $mouseDrag ? 'true' : 'false'; ?>,
		touchDrag: <?php echo $touchDrag ? 'true' : 'false'; ?>,
		slideBy: <?php echo $scrollItems; ?>,
		dots: <?php echo $pagination ? 'true' : 'false'; ?>,
		nav: <?php echo $navigation ? 'true' : 'false'; ?>,
		navText: [
			'<?php echo $prevNav; ?>',
			'<?php echo $nextNav; ?>'
		],
		responsive: {
		  0: {
			items: <?php echo $colsOnMobile; ?>
		  },
		  479: {
			items: <?php echo $colsOnTablet; ?>
		  },
		  768: {
			items: <?php echo $colsOnDesktopSmall; ?>
		  },
		  991: {
			items: <?php echo $colsOnDesktop; ?>
		  },
		  1199: {
			items: <?php echo $cols; ?>
		  }
		}
	});
});
</script>