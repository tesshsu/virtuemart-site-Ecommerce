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
// ##NOT_FAQS_START##	
	ResetElement('test');
// ##NOT_FAQS_END##	

// 
	
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
<input type="hidden" name="view" value="settingsview" />
<input type="hidden" name="tab" id='tab' value="<?php echo $this->tab; ?>" />
<div class='ffs_tabs'>


<?php //  ?>

<?php //  ?>

<?php // ##NOT_FAQS_START## ?>
<a id='link_test' class='ffs_tab' href='#' onclick="ShowTab('test');return false;"><?php echo JText::_("TESTIMONIALS_LIST"); ?></a>
<?php // ##NOT_FAQS_END## ?>

</div>

<?php //  ?>

<?php //  ?>

<?php // ##NOT_FAQS_START## ?>
<div id="tab_test">

	<fieldset class="adminform">
		<legend><?php echo JText::_("TEST_WHEN_SHOWING_PRODUCT_LIST"); ?></legend>

		<table class="admintable">
		
			<tr>
				<td align="right" class="key">
					<?php echo JText::_("test_show_prod_mode"); ?>:
					
				</td>
				<td>
					<select name="test_test_show_prod_mode">
						<option value="list" <?php if ($this->settings['test_test_show_prod_mode'] == 'list') echo " SELECTED"; ?> ><?php echo JText::_('test_show_prod_mode_list'); ?></option>
						<option value="inline" <?php if ($this->settings['test_test_show_prod_mode'] == 'inline') echo " SELECTED"; ?> ><?php echo JText::_('test_show_prod_mode_inline'); ?></option>
						<option value="accordian" <?php if ($this->settings['test_test_show_prod_mode'] == 'accordian') echo " SELECTED"; ?> ><?php echo JText::_('test_show_prod_mode_accordian'); ?></option>
					</select>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('VIEWHELP_test_show_prod_mode'); ?></div>
				</td>
			</tr>
			
			<tr>
				<td align="right" class="key" style="width:250px;">
					<?php echo JText::_("test_pages"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='test_test_pages' value='1' <?php if ($this->settings['test_test_pages'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('VIEWHELP_test_pages'); ?></div>
				</td>
			</tr>
			
			<tr>
				<td align="right" class="key" style="width:250px;">
					<?php echo JText::_("test_always_prod_select"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='test_test_always_prod_select' value='1' <?php if ($this->settings['test_test_always_prod_select'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fst_help'><?php echo JText::_('VIEWHELP_test_always_prod_select'); ?></div>
				</td>
			</tr>

		</table>
	</fieldset>

</div>
<?php // ##NOT_FAQS_END## ?>

</form>

<script>

window.addEvent('domready', function(){
	if (location.hash)
	{
		ShowTab(location.hash.replace('#',''));
	}
	else
	{
		var els = jQuery('a.ffs_tab');
		var el = jQuery(els[0]);
		var firsttab = el.attr('id').replace("link_","");
		ShowTab(firsttab);
	}
});
 
</script>
