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
<?php echo FST_Helper::PageTitle('SUPPORT_ADMIN',"NO_PERM"); ?>
<div class="fst_clear"></div>
<p class="notice">
<?php echo JText::_("YOU_DO_NOT_HAVE_PERMISSION_TO_PERFORM_AND_SUPPORT_ADMINISTRATION_ACTIVITIES"); ?>
</p>

<?php $user = JFactory::getUser(); if ($user->id == 0): ?>
<div class="fst_ticket_login_head"><?php echo JText::_("LOGIN"); ?></div>
<div class="fst_ticket_login_subtext"><?php echo JText::_("LOG_IN_TO_AN_EXISTING_ACCOUNT"); ?></div>
<form action="<?php echo FSTRoute::x("index.php?option=com_user"); ?>"  method="post" name="fst_login" id="fst_login">
<table class="fst_table" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo JText::_("USERNAME"); ?></th>
		<td><input name="username" id="username" class="inputbox" alt="username" size="18" type="text" /></td>
	</tr>
	<tr>
		<th><?php echo JText::_("PASSWORD"); ?></th>
<?php if (FST_Helper::Is16()): ?>
	<td><input id="password" name="password" class="inputbox" size="18" alt="password" type="password" /></td>
<?php else: ?>
	<td><input id="passwd" name="passwd" class="inputbox" size="18" alt="password" type="password" /></td>
<?php endif; ?>
	</tr>
	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	<tr>
		<td colspan="2" align="center"><label for="remember"><?php echo JText::_("REMEMBER_ME"); ?> </label><input id="remember" name="remember" class="inputbox" value="yes" alt="Remember Me" type="checkbox" /></td>	
</tr>
	<?php endif; ?>
<tr>
		<td colspan="2" align="center"><input class='button' type="submit" value="<?php echo JText::_("LOGIN"); ?>" /></td>	
</tr>
</table>

<?php if (FST_Helper::Is16()): ?>
	<input name="option" value="com_users" type="hidden">
	<input name="task" value="user.login" type="hidden">
<?php else: ?>
<input name="option" value="com_user" type="hidden">
<input name="task" value="login" type="hidden">
<?php endif; ?>
	<input name="return" value="<?php echo $this->return; ?>" type="hidden">
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php endif; ?>

<?php include JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'_powered.php'; ?>
<?php echo FST_Helper::PageStyleEnd(); ?>
