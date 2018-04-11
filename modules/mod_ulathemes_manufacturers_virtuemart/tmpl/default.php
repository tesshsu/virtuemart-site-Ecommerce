<?php
/*
# ------------------------------------------------------------------------
# Ulathemes Manufacturers Carousel for VirtueMart for Joomla 3
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
?>
<style type="text/css">
#ulathemes-manufacturers-virtuemart-wrapper<?php echo $module->id; ?> {
	<?php echo ($bgImage != '') ? "background: url({$bgImage}) repeat scroll 0 0;" : ''; ?>
	<?php echo ($isBgColor) ? "background-color: {$bgColor};" : '';?>
}
</style>
<div id="ulathemes-manufacturers-virtuemart-wrapper<?php echo $module->id; ?>" class="ulathemes-manufacturers-virtuemart">
	<div id="ulathemes-manufacturers-virtuemart<?php echo $module->id; ?>" class="owl-carousel owl-theme">
		<?php
			$manufacturersCount = count($manufacturers);
			$totalLoop = ceil($manufacturersCount/$rows);
			$keyLoop   = 0;
			for($i = 0; $i < $totalLoop; $i ++) :
		?>
		<?php if($keyLoop <= $manufacturersCount) : ?>
		<div class="item">
			<?php
			for($j = 0; $j < $rows; $j ++) :
				$manufacturer = $manufacturers[$keyLoop];
				$keyLoop = $keyLoop + 1;
				
				/* config */
				$mid   = $manufacturer->virtuemart_manufacturer_id;
				$mlink = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $mid);
				$mname = $manufacturer->mf_name;
				$mlogo = $manufacturer->images[0]->file_url;
				$mlogo = (!empty($mlogo)) ? JURI::base() . $mlogo : $mlogo;
			?>
				<?php if($keyLoop <= $manufacturersCount) : ?>
				<div class="item-row">
					<!-- Image Block -->
					<?php if($showImage): ?>
						<?php if($linkOnImage): ?>
						<a href="<?php echo $mlink; ?>" title="<?php echo $mname; ?>">
							<img src="<?php echo $mlogo; ?>" alt="<?php echo $mname; ?>" />
						</a>
						<?php else: ?>
						<img src="<?php echo $mlogo; ?>" alt="<?php echo $mname; ?>" />
						<?php endif; ?>
					<?php endif; ?>
					
					<!-- Caption Block -->
					<?php if($showName): ?>
					<div class="ulathemes-caption">
						<?php if($linkOnName): ?>
						<a href="<?php echo $mlink; ?>" title="<?php echo $mname; ?>"><?php echo $mname; ?></a>
						<?php else: ?>
						<?php echo $mname; ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
			
		</div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
	<div class="clearfix"></div>	
</div>
<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#ulathemes-manufacturers-virtuemart<?php echo $module->id; ?>').owlCarousel({
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