<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo $this->tmpl ? FST_Helper::PageStylePopup() : FST_Helper::PageStyle(); ?>


	<?php echo $this->tmpl ? FST_Helper::PageTitlePopup("TESTIMONIALS","ADD_A_TESTIMONIAL") : FST_Helper::PageTitle("TESTIMONIALS","ADD_A_TESTIMONIAL"); ?>
	<div class='fst_kb_comment_add' id='add_comment'>
		<?php $this->comments->DisplayAdd(); ?>
	</div>

	<div id="comments"></div>

	<div class='fst_comments_result_<?php echo $this->comments->uid; ?>'></div>

<?php $this->comments->IncludeJS() ?>

<?php include JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'_powered.php'; ?>

<?php echo $this->tmpl ? FST_Helper::PageStylePopupEnd() : FST_Helper::PageStyleEnd(); ?>