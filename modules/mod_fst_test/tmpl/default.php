<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php if ($maxheight > 0): ?>
<script>

jQuery(document).ready(function () {
	setTimeout("scrollDown()",3000);
});

function scrollDown()
{
	var settings = { 
		direction: "down", 
		step: 40, 
		scroll: true, 
		onEdge: function (edge) { 
			if (edge.y == "bottom")
			{
				setTimeout("scrollUp()",3000);
			}
		} 
	};
	jQuery(".fst_comments_scroll").autoscroll(settings);
}

function scrollUp()
{
	var settings = { 
		direction: "up", 
		step: 40, 
		scroll: true,    
		onEdge: function (edge) { 
			if (edge.y == "top")
			{
				setTimeout("scrollDown()",3000);
			}
		} 
	};
	jQuery(".fst_comments_scroll").autoscroll(settings);
}
</script>

<style>
#fst_comments_scroll {
	max-height: <?php echo $maxheight; ?>px;
	overflow: hidden;
}
</style>
<?php endif; ?>
<?php if (1/*count($rows) > 0*/) : ?>
<div id="fst_comments_scroll" class="fst_comments_scroll">
<?php $comments->DisplayComments($dispcount, $listtype, $maxlength); ?>
</div>
<?php if ($params->get('show_more')) : ?>
	<?php if ($params->get('morelink')): ?>
		<div class='fst_mod_test_all'><a href='<?php echo JRoute::_( $params->get('morelink') ); ?>'><?php echo JText::_("SHOW_MORE_TESTIMONIALS"); ?></a></div>
	<?php elseif ($prodid == -1): ?>
		<div class='fst_mod_test_all'><a href='<?php echo FSTRoute::_( 'index.php?option=com_fst&view=test' ); ?>'><?php echo JText::_("SHOW_MORE_TESTIMONIALS"); ?></a></div>
	<?php else : ?>
		<div class='fst_mod_test_all'><a href='<?php echo FSTRoute::_( 'index.php?option=com_fst&view=test&prodid=' . $prodid ); ?>'><?php echo JText::_("SHOW_MORE_TESTIMONIALS"); ?></a></div>
	<?php endif; ?>
<?php endif; ?>
<?php else: ?>
No testimonials found!.
<?php endif; ?>
<?php if ($params->get('show_add') && $comments->can_add): ?>
	<?php if ($params->get('addlink')) :?>
		<div class='fst_mod_test_add'><a href='<?php echo JRoute::_( $params->get('addlink') ); ?>'><?php echo JText::_("ADD_A_TESTIMONIAL"); ?></a></div>
	<?php else: ?>
		<div class='fst_mod_test_add'><a class='fst_modal' href='<?php echo FSTRoute::_( 'index.php?tmpl=component&option=com_fst&view=test&layout=create&onlyprodid=' . $prodid ); ?>' rel="{handler: 'iframe', size: {x: 500, y: 500}}"><?php echo JText::_("ADD_A_TESTIMONIAL"); ?></a></div>
	<?php endif; ?>
<?php endif; ?>
