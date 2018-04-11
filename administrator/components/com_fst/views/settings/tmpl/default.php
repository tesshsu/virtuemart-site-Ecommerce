<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<script>
function ResetElement(tabid)
{
	document.getElementById('tab_' + tabid).style.display = 'none';
	document.getElementById('link_' + tabid).style.backgroundColor = '';

	document.getElementById('link_' + tabid).onmouseover = function() {
		this.style.backgroundColor='<?php echo FST_Settings::get('css_hl'); ?>';
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
		this.style.backgroundColor='';
	}

}
function ShowTab(tabid)
{
	ResetElement('general');
// ##NOT_FAQS_START##
// 
	ResetElement('test');
// 
	ResetElement('visual');
// 
	
	location.hash = tabid;
	jQuery('#tab').val(tabid);
	document.getElementById('tab_' + tabid).style.display = 'inline';
	document.getElementById('link_' + tabid).style.backgroundColor = '#f0f0ff';
	
	document.getElementById('link_' + tabid).onmouseover = function() {
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
	}
}
</script>

<style>
.fst_custom_warn
{
	color: red;
}
.fst_help
{
	border: 1px solid #CCC;
	float: left;
	padding: 3px;
	background-color: #F8F8FF;
}
.admintable td
{
	border-bottom: 1px solid #CCC;
	padding-bottom: 4px;
	padding-top: 2px;
}
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="what" value="save">
<input type="hidden" name="option" value="com_fst" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="settings" />
<input type="hidden" name="tab" id='tab' value="<?php echo $this->tab; ?>" />
<input type="hidden" name="version" value="<?php echo $this->settings['version']; ?>" />
<input type="hidden" name="fsj_username" value="<?php echo $this->settings['fsj_username']; ?>" />
<input type="hidden" name="fsj_apikey" value="<?php echo $this->settings['fsj_apikey']; ?>" />
<input type="hidden" name="content_unpublished_color" value="<?php echo $this->settings['content_unpublished_color']; ?>" />
<div class='ffs_tabs'>

<!--<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;">General</a>-->

<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;"><?php echo JText::_("GENERAL_SETTINGS"); ?></a> 
<?php // ##NOT_FAQS_START## ?>
<?php //  ?>
<a id='link_test' class='ffs_tab' href='#' onclick="ShowTab('test');return false;"><?php echo JText::_("TESTIMONIALS"); ?></a>
<?php //  ?>
<?php // ##NOT_FAQS_END## ?>
<a id='link_visual' class='ffs_tab' href='#' onclick="ShowTab('visual');return false;"><?php echo JText::_("VISUAL"); ?></a>
<?php //  ?>

</div>

<div id="tab_general">

	<fieldset class="adminform">
		<legend><?php echo JText::_("GENERAL_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("HIDE_POWERED"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='hide_powered' value='1' <?php if ($this->settings['hide_powered'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_hide_powered'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("jquery_include"); ?>:
				</td>
				<td style="width:250px;">
					<select name="jquery_include">
						<option value="auto" <?php if ($this->settings['jquery_include'] == "auto") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_auto'); ?></option>
						<option value="yes" <?php if ($this->settings['jquery_include'] == "yes") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_yes'); ?></option>
						<option value="yesnonc" <?php if ($this->settings['jquery_include'] == "yesnonc") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_yesnonc'); ?></option>
						<option value="no" <?php if ($this->settings['jquery_include'] == "no") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_no'); ?></option>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_jquery_include'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ##NOT_FAQS_START## ?>

	<fieldset class="adminform">
		<legend><?php echo JText::_("PERMISSIONS_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("USE_JOOMLA_PERM_COMMENT"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='perm_mod_joomla' value='1' <?php if ($this->settings['perm_mod_joomla'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_perm_mod_joomla'); ?></div>
				</td>
			</tr>
<?php //  ?>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_("COMMENTS_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("CAPTCHA_TYPE"); ?>:
				</td>
				<td style="width:250px;">
					<select name="captcha_type">
						<option value="none" <?php if ($this->settings['captcha_type'] == "none") echo " SELECTED"; ?> ><?php echo JText::_('FNONE'); ?></option>
						<option value="fsj" <?php if ($this->settings['captcha_type'] == "fsj") echo " SELECTED"; ?> ><?php echo JText::_('BUILT_IN'); ?></option>
						<option value="recaptcha" <?php if ($this->settings['captcha_type'] == "recaptcha") echo " SELECTED"; ?> ><?php echo JText::_('RECAPTCHA'); ?></option>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_captcha_type'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("HIDE_ADD_COMMENT"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='comments_hide_add' value='1' <?php if ($this->settings['comments_hide_add'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_comments_hide_add'); ?></div>
				</td>
			</tr>
<?php //  ?>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("RECAPTCHA_PUBLIC_KEY"); ?>:
				</td>
				<td>
					<input name='recaptcha_public' size="40" value='<?php echo $this->settings['recaptcha_public'] ?>'>
				</td>
				<td rowspan="2">
					<div class='fst_help'><?php echo JText::_('SETHELP_recaptcha_public'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("RECAPTCHA_PRIVATE_KEY"); ?>:
				</td>
				<td>
					<input name='recaptcha_private' size="40" value='<?php echo $this->settings['recaptcha_private'] ?>'>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("RECAPTCHA_THEME"); ?>:
				</td>
				<td>
					<select name="recaptcha_theme">
						<option value="red" <?php if ($this->settings['recaptcha_theme'] == "red") echo " SELECTED"; ?> ><?php echo JText::_('RED'); ?></option>
						<option value="white" <?php if ($this->settings['recaptcha_theme'] == "white") echo " SELECTED"; ?> ><?php echo JText::_('WHITE'); ?></option>
						<option value="blackglass" <?php if ($this->settings['recaptcha_theme'] == "blackglass") echo " SELECTED"; ?> ><?php echo JText::_('BLACK_GLASS'); ?></option>
						<option value="clean" <?php if ($this->settings['recaptcha_theme'] == "clean") echo " SELECTED"; ?> ><?php echo JText::_('CLEAN'); ?></option>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_recaptcha_theme'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ##NOT_FAQS_END## ?>

<?php if (FST_Helper::Is16()): ?>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_("DATE_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("SHORT_DATETIME"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_dt_short' id='date_dt_short' size="40" value='<?php echo $this->settings['date_dt_short'] ?>'>
					<div class="fst_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC4') . ', H:i'; ?></b></div>
					<div id="test_date_dt_short"></div>
				</td>
				<td rowspan="4" valign="top">
					<div class='fst_help'>
					<?php echo JText::_('SETHELP_DATE_FORMATS'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("LONG_DATETIME"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_dt_long' id='date_dt_long' size="40" value='<?php echo $this->settings['date_dt_long'] ?>'>
					<div class="fst_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC3') . ', H:i'; ?></b></div>
					<div id="test_date_dt_long"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("SHORT_DATE"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_d_short' id='date_d_short' size="40" value='<?php echo $this->settings['date_d_short'] ?>'>
					<div class="fst_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC4'); ?></b></div>
					<div id="test_date_d_short"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("LONG_DATE"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_d_long' id='date_d_long' size="40" value='<?php echo $this->settings['date_d_long'] ?>'>
					<div class="fst_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC3'); ?></b></div>
					<div id="test_date_d_long"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("TIMEZONE_OFFSET"); ?>:
				</td>
				<td>
					<input name='timezone_offset' size="40" value='<?php echo $this->settings['timezone_offset'] ?>'>
					<div class="fst_clear"></div>
					<div id="test_timezone_offset"></div>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_timezone_offset'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("TEST_DATE_FORMATS"); ?>:
				</td>
				<td style="width:250px;">
					<button id="test_date_formats"><?php echo JText::_('TEST_DATE_FORMATS_BUTTON'); ?></button>
				</td>
				<td valign="top">
					<div class='fst_help'>
					<?php echo JText::_('SETHELP_DATE_TEST'); ?>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

<?php endif; ?>

<?php // ##NOT_FAQS_START## ?>
<?php //  ?>

</div>

<?php // ##NOT_FAQS_START## ?>
<?php //  ?>

<div id="tab_test" style="display:none;">

	<fieldset class="adminform">
		<legend><?php echo JText::_("TESTIMONIAL_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("TESTIMONIALS_ARE_MODERATED_BEFORE_DISPLAY"); ?>:
					
				</td>
				<td style="width:250px;">
					<select name="test_moderate">
						<option value="all" <?php if ($this->settings['test_moderate'] == "all") echo " SELECTED"; ?> ><?php echo JText::_('ALL_TESTIMONIALS_MODERATED'); ?></option>
						<option value="guests" <?php if ($this->settings['test_moderate'] == "guests") echo " SELECTED"; ?> ><?php echo JText::_('GUEST_TESTIMONIALS_MODERATED'); ?></option>
						<option value="registered" <?php if ($this->settings['test_moderate'] == "registered") echo " SELECTED"; ?> ><?php echo JText::_('REGISTERED_AND_GUEST_TESTIMONIALS_MODERATED'); ?></option>
						<option value="none" <?php if ($this->settings['test_moderate'] == "none") echo " SELECTED"; ?> ><?php echo JText::_('NO_TESTIMONIALS_ARE_MODERATED'); ?></option>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_moderate'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("ALLOW_NO_PRODUCT_TESTS"); ?>:
				</td>
				<td>
					<input type='checkbox' name='test_allow_no_product' value='1' <?php if ($this->settings['test_allow_no_product'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_allow_no_product'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("HIDE_EMPTY_PROD_WHEN_LISTING"); ?>:
				</td>
				<td>
					<input type='checkbox' name='test_hide_empty_prod' value='1' <?php if ($this->settings['test_hide_empty_prod'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_hide_empty_prod'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("WHO_CAN_ADD_TESTIMONIALS"); ?>:
				</td>
				<td>
					<select name="test_who_can_add">
						<option value="anyone" <?php if ($this->settings['test_who_can_add'] == "anyone") echo " SELECTED"; ?> ><?php echo JText::_('ANYONE'); ?></option>
						<option value="registered" <?php if ($this->settings['test_who_can_add'] == "registered") echo " SELECTED"; ?> ><?php echo JText::_('REGISTERED_USERS_ONLY'); ?></option>
						<option value="moderators" <?php if ($this->settings['test_who_can_add'] == "moderators") echo " SELECTED"; ?> ><?php echo JText::_('MODERATORS_ONLY'); ?></option>
						
						<!-- add access levels here too -->
						<?php if (FST_Helper::Is16()): ?>
							<?php 
								FSTAdminHelper::LoadAccessLevels(); 
								$options = FSTAdminHelper::$access_levels;		
							foreach ($options as $option): ?>
								<option value="<?php echo $option->value; ?>" <?php if ($this->settings['test_who_can_add'] == $option->value) echo " SELECTED"; ?>>ACL: <?php echo $option->text; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_who_can_add'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("EMAIL_ON_SUBMITTED"); ?>:
					
				</td>
				<td>
					<input name='test_email_on_submit' size="40" value='<?php echo $this->settings['test_email_on_submit']; ?>'>
					</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_email_on_submit'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("TEST_USE_EMAIL"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='test_use_email' value='1' <?php if ($this->settings['test_use_email'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_use_email'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("TEST_USE_WEBSITE"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='test_use_website' value='1' <?php if ($this->settings['test_use_website'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_use_website'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("TEST_COMMENTS_PER_PAGE"); ?>:
				</td>
				<td>
					<?php $this->PerPage('test_comments_per_page'); ?>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_test_comments_per_page'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>

</div>
<?php //  ?>

<div id="tab_visual" style="display:none;">

	<fieldset class="adminform">
		<legend><?php echo JText::_("VISUAL_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("USE_SKIN_STYLING_FOR_PAGEINATION_CONTROLS"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='skin_style' value='1' <?php if ($this->settings['skin_style'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_skin_style'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("title_prefix"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='title_prefix' value='1' <?php if ($this->settings['title_prefix'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_title_prefix'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					
					<?php echo JText::_("USE_JOOMLA_SETTING_FOR_PAGE_TITLE_VISIBILITY"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='use_joomla_page_title_setting' value='1' <?php if ($this->settings['use_joomla_page_title_setting'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_use_joomla_page_title_setting'); ?></div>
			</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_("CSS_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("HIGHLIGHT_COLOUR"); ?>:
					
				</td>
				<td style="width:250px;">
					<input name='css_hl' value='<?php echo $this->settings['css_hl'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_hl)">
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_css_hl'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					
						<?php echo JText::_("BORDER_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_bo' value='<?php echo $this->settings['css_bo'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_bo)">
					</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_css_bo'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key">
					
						<?php echo JText::_("TAB_BACKGROUND_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_tb' value='<?php echo $this->settings['css_tb'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_tb)">
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_css_tb'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ##NOT_FAQS_START## ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_("SUPPORT_COLOR_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
						<?php echo JText::_("USER_MESSAGE_COLOR"); ?>:
				</td>
				<td style="width:250px;">
					<input name='support_user_message' value='<?php echo $this->settings['support_user_message'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].support_user_message)">
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_support_user_message'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("HANDLER_MESSAGE_COLOR"); ?>:
				</td>
				<td>
					<input name='support_admin_message' value='<?php echo $this->settings['support_admin_message'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].support_admin_message)">
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_support_admin_message'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("PRIVATE_MESSAGE_COLOR"); ?>:
				</td>
				<td>
					<input name='support_private_message' value='<?php echo $this->settings['support_private_message'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].support_private_message)">
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('SETHELP_support_private_message'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ##NOT_FAQS_END## ?>	
</div>
<?php //  ?>

</form>

<script>

function testreference()
{
	$('testref').innerHTML = "<?php echo JText::_('PLEASE_WAIT'); ?>";
	var format = $('support_reference').value
	var url = '<?php echo FSTRoute::x("index.php?option=com_fst&view=settings&what=testref",false); ?>&ref=' + format;
	
<?php if (FST_Helper::Is16()): ?>
	$('testref').load(url);
<?php else: ?>
	new Ajax(url, {
	method: 'get',
	update: $('testref')
	}).request();
<?php endif; ?>
}

window.addEvent('domready', function(){

	if (location.hash)
	{
		ShowTab(location.hash.replace('#',''));
	}
	else
	{
		ShowTab('general');
	}
	
<?php if (FST_Helper::Is16()): ?>
	jQuery('#test_date_formats').click(function (ev) {
		ev.preventDefault();
			
		var url = '<?php echo FSTRoute::x("index.php?option=com_fst&view=settings&what=testdates",false); ?>';

		url += '&date_dt_short=' + encodeURIComponent(jQuery('#date_dt_short').val());
		url += '&date_dt_long=' + encodeURIComponent(jQuery('#date_dt_long').val());
		url += '&date_d_short=' + encodeURIComponent(jQuery('#date_d_short').val());
		url += '&date_d_long=' + encodeURIComponent(jQuery('#date_d_long').val());

		jQuery.get(url, function (data) {
			var result = jQuery.parseJSON(data);
			jQuery('#test_date_dt_short').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_dt_short);
			jQuery('#test_date_dt_long').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_dt_long);
			jQuery('#test_date_d_short').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_d_short);
			jQuery('#test_date_d_long').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_d_long);
			jQuery('#test_timezone_offset').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.timezone_offset);
		});
	});
<?php endif; ?>
});
 
</script>
