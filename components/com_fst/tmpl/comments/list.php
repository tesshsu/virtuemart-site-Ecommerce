<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php $comm_ref = "comments_" . $this->ident; ?>
<a name="<?php echo $comm_ref; ?>"></a>
<?php if ($this->showheader): ?>
<div class="fst_spacer"></div>
<?php echo FST_Helper::PageSubTitle("USER_COMMENTS"); ?>
<?php endif; ?>

<?php if ($this->opt_show_add): ?>
<div class='fst_comment_add' id='add_comment'>
	<?php include $this->tmplpath . DS . 'addcomment.php' ?>
</div>

<div class="fst_spacer"></div>
<?php endif; ?>

<div id="comments" class="fst_comments_result_<?php echo $this->uid; ?>">
<?php $offset = 0; ?>
<?php $page = JRequest::getInt('comm_page', 1); ?>
<?php if ($this->opt_disable_pages) $page = 1; ?>
<?php $perpage = $this->perpage; ?>
<?php $start = ($page - 1) * $perpage + 1; ?>
<?php $end = $start + $perpage - 1; ?>

	<?php foreach ($this->_data as $this->comment): ?>
		<?php $offset ++ ; ?>

		<?php if ($offset < $start) continue; ?>
		<?php if ($offset > $end) continue; ?>
		
		<?php include $this->tmplpath . DS .'comment.php' ?>
		
	<?php endforeach; ?>
</div>

<?php 
$pages = ceil(count($this->_data) / $perpage);

if ($pages > 1)
{
	echo JText::_("PAGE") .  ": ";

	for ($i = 1 ; $i <= $pages ; $i++)
	{
		if ($i == $page)
		{
			echo "<b>$i</b> ";	
		} else {
			echo "<a href='" . FSTRoute::_('&comm_page=' . $i). "#$comm_ref'>$i</a> ";
		}	
	}
}
?>
 
<?php $this->IncludeJS() ?>
