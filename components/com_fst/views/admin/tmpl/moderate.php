<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo FST_Helper::PageStyle(); ?>
<?php if (FST_Helper::IsTests()) : ?>
	<?php echo FST_Helper::PageTitle('COMMENT_MODERATION'); ?>
<?php else: ?>
	<?php echo FST_Helper::PageTitle('SUPPORT_ADMIN','COMMENT_MODERATION'); ?>
<?php endif; ?>
<?php //  ?>
<?php $this->comments->DisplayModerate(); ?>
<?php include JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'_powered.php'; ?>
<?php echo FST_Helper::PageStyleEnd(); ?>