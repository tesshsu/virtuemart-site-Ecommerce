<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo JHTML::_( 'form.token' ); ?>
<style>
label {
	width: auto !important;
	float: none !important;
	clear: none !important;
	display: inline !important;
}
input {
	float: none !important;
	clear: none !important;
	display: inline !important;
}
</style>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
                submitform( pressbutton );
                return;
        }
        submitform(pressbutton);
}
//-->

// 



</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
	<fieldset class="adminform">
		<legend><?php echo JText::_("DETAILS"); ?></legend>

		<table class="admintable">
		<?php if ($this->showtypes) { ?>
		<tr>
			<td width="135" align="right" class="key">
				<?php echo JText::_("ENTRY_TYPE"); ?>:
			</td>
			<td>
				<?php echo $this->type; ?>
			</td>
		</tr>
		<?php } ?>		
		<tr>
			<td width="135" align="right" class="key">
					<?php echo JText::_("USER_GROUP"); ?>:
			</td>
			<td>
				<div name='users' id="users">
					<?php echo $this->users; ?>
				</div>
			</td>
		</tr>		
	</table>
	</fieldset>
</div>

<div>
	<fieldset class="adminform">
		<legend><?php echo JText::_("COMMENT_MODERATION"); ?></legend>

		<table class="admintable">
		<tr>
			<td width="135" align="right" class="key">
					<?php echo JText::_("MODERATOR"); ?>:
			</td>
			<td>
				<input type='checkbox' name='mod_kb' value='1' <?php if ($this->user->mod_kb) { echo " checked='yes' "; } ?>>
            </td>
		</tr>
	</table>
	</fieldset>
</div>
<!--  -->
<div class="clr"></div>

<input type="hidden" name="option" value="com_fst" />
<input type="hidden" name="id" value="<?php echo $this->user->id; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="fuser" />
</form>

