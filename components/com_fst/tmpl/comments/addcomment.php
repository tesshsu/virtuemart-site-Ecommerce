<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php if ($this->can_add): ?>

<script type="text/javascript">
 var RecaptchaOptions = {
	theme : '<?php echo FST_Settings::get('recaptcha_theme'); ?>'
 };
 jQuery(document).ready(function () {
		var addCommentBtn = jQuery('#commentaddbutton');
		// check if login, show need to login for add comment text
		
	});
</script>
<div class='fst_kb_comment_add' id='add_comment'>
<?php if ($this->comments_hide_add): ?>
	<a id="commentaddbutton" href='#' onclick='return false;' class='fst_kb_comment_add_text'><?php echo $this->add_a_comment; ?></a>
	<div id="commentadd" style="display:none;">

<script>
	jQuery(document).ready(function () {
		jQuery('#commentaddbutton').click( function (ev) {
			ev.preventDefault();
			jQuery('#commentadd').css('display','block');
			jQuery('#commentaddbutton').css('display','none');
		});
	});
</script>

<?php endif; ?>

	<?php echo FST_Helper::PageSubTitle2($this->add_a_comment); ?>
	<form id='addcommentform' action="<?php echo FSTRoute::x( '&view=test&tmpl=component&task=commentpost' );?>" method="post">
	<input type='hidden' name='comment' value='add' >
	<input type='hidden' name='uid' value='<?php echo $this->uid; ?>' >
	<input type='hidden' name='ident' value='<?php echo  $this->ident ?>' >
	<?php if ($this->itemid): ?>
	<input type='hidden' name='itemid' value='<?php echo  $this->itemid ?>' >
	<?php endif; ?>
	<table class="fsj_comment_table">
	<?php if ($this->show_item_select) : ?>
		<tr>
			<th><?php echo $this->handler->email_article_type; ?>&nbsp;</th>
			<td>
				<?php echo $this->GetItemSelect(); ?>
			</td>
			<?php if ($this->errors['itemid']): ?><td class='fst_must_have_field'><?php echo $this->errors['itemid'] ?></td><?php endif; ?>
		</tr>
	<?php endif; ?>
		<tr>
			<th><?php echo JText::_('Name'); ?>&nbsp;</th>
		<td>
			<?php if (!$this->_permissions['mod_kb'] && $this->loggedin) : ?>
			<?php echo $this->post['name'] ?><input name='name' type='hidden' id='comment_name' value='<?php echo FST_Helper::escape($this->post['name']) ?>' >
			<?php else: ?>
			<input name='name' id='comment_name' value='<?php echo FST_Helper::escape($this->post['name']) ?>' >
			<?php endif; ?>
			</td>
			<?php if ($this->errors['name']): ?><td class='fst_must_have_field'><?php echo $this->errors['name'] ?></td><?php endif; ?>
		</tr>
	<?php if ($this->use_email && !($this->loggedin)): ?>
		<tr>
			<th><?php echo JText::_('EMail'); ?>&nbsp;</th>
			<td><input name='email' value='<?php echo FST_Helper::escape($this->post['email']) ?>'></td>
			<td>
			<?php if ($this->errors['email']): ?>
				<div class='fst_must_have_field'><?php echo $this->errors['email'] ?></div>
			<?php else: ?>
				(<?php echo JText::_('WILL_NOT_BE_PUBLISHED'); ?>)
			<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($this->use_website): ?>
		<!--tr>
			<th><?php echo JText::_('Website'); ?>&nbsp;</th>
			<td><input name='website' value='<?php echo FST_Helper::escape($this->post['website']) ?>'></td>
			<?php if ($this->errors['website']): ?><td class='fst_must_have_field'><?php echo $this->errors['website'] ?></td><?php endif; ?>
		</tr-->
	<?php endif; ?>

	<?php foreach ($this->customfields as $custfield): ?>
		<tr>
			<th><?php echo $custfield['description']; ?>&nbsp;</th>
			<td><?php echo FSTCF::FieldInput($custfield, $this->errors,'comments') ?></td>
		</tr>
	<?php endforeach; ?>

	<?php if ($this->errors['body']): ?><tr><td></td><td class='fst_must_have_field'><?php echo $this->errors['body'] ?></td></tr><?php endif; ?>
		<tr>
			<th><?php echo JText::_('COMMENT_BODY'); ?>&nbsp;</th>
			<td colspan=2><textarea name='body' rows='5' cols='40' id='comment_body'><?php echo FST_Helper::escape($this->post['body']) ?></textarea></td>
		</tr>
		
		<?php if ($this->captcha) : ?>
		<?php if ($this->errors['captcha']): ?><tr><td></td><td class='fst_must_have_field'><?php echo $this->errors['captcha'] ?></td></tr><?php endif; ?>
		<tr>
			<th><?php echo JText::_('Verification'); ?></th>
			<td colspan=2 id='captcha_cont'><?php echo $this->captcha ?></td>
		</tr>	
		<?php endif; ?>	
		<tr>
			<td></td>
			<td>
				<input type=submit value=' <?php echo $this->post_comment ?> ' id='addcomment' class='addcomment' >
			</td>
		</tr>
	</table>
	</form>
	<?php if ($this->comments_hide_add): ?>
	</div>
<?php endif; ?>
</div>
<?php else : ?>
<p class="loginMessage"><?php echo JText::_('YOU_NEED_TO_LOGIN_FOR_ADDING_COMMENT'); ?></p>
<?php endif; ?>