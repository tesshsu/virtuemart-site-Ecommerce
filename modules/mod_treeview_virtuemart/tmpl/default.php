<?php 

// no direct access
defined('_JEXEC') or die;

$document = JFactory::getDocument();

$count_root_category = count($categories);
$i = 1;
$count_parentCatRoot = 1;

foreach($categories as $item) {
	$cidRoot   = $item->virtuemart_category_id;
	$parentCatRoot = $categoryModel->getCategoryRecurse($cidRoot,0); 
	$count_parentCatRoot = count($parentCatRoot);	
}
?>
<div id="ula-treeview-virtuemart<?php echo $module->id; ?>" class="ula-treeview-virtuemart">
	<?php if($params->get('showControl', 1)) { ?>
	<div id="ula-treeview-treecontrol<?php echo $module->id; ?>" class="treecontrol">
        <a href="#" title="<?php echo JTEXT::_('ULA_TREEVIEW_VMART_COLLAPSE_ALL_DESC'); ?>"><?php echo JTEXT::_('ULA_TREEVIEW_VMART_COLLAPSE_ALL'); ?></a> | 
        <a href="#" title="<?php echo JTEXT::_('ULA_TREEVIEW_VMART_EXPAND_ALL_DESC'); ?>"><?php echo JTEXT::_('ULA_TREEVIEW_VMART_EXPAND_ALL'); ?></a> | 
        <a href="#" title="<?php echo JTEXT::_('ULA_TREEVIEW_VMART_TOGGLE_ALL_DESC'); ?>"><?php echo JTEXT::_('ULA_TREEVIEW_VMART_TOGGLE_ALL'); ?></a>
    </div>
	<?php } ?>
	
	<ul class="level0 <?php echo $params->get('moduleStyle', ''); ?>">
		<?php require JModuleHelper::getLayoutPath($module->module, 'default_items'); ?>
		
		<?php if( $count_root_category > $max_vmcategory_menu ) : ?>
		<li class="vmcategory-more">
			<div class="more-inner">
				<span class="more-view"><em class="more-categories"><?php echo JTEXT::_('ULA_TREEVIEW_VMART_MORE_CATEGORIES'); ?></em></span>
			</div>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php
if( $count_root_category > $max_vmcategory_menu ) {
	$js="
	//<![CDATA[
	jQuery(document).ready(function() {
		jQuery('#ula-treeview-virtuemart" . $module->id . " li.extra_menu').hide();
		jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more').click(function() {
			jQuery('#ula-treeview-virtuemart" . $module->id . " li.extra_menu').slideToggle();
			jQuery('.extra_menu').css('overflow','visible');
			
			if(jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more .more-view').hasClass('open'))
			{
				jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more .more-view').removeClass('open');
				jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more .more-view').html('<em class=\"more-categories\">" . JTEXT::_('ULA_TREEVIEW_VMART_MORE_CATEGORIES') . "</em>');
			}
			else
			{
				jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more .more-view').addClass('open');
				jQuery('#ula-treeview-virtuemart" . $module->id . " .vmcategory-more .more-view').html('<em class=\"closed-menu\">" . JTEXT::_('ULA_TREEVIEW_VMART_CLOSE_MENU') . "</em>');
			}
		});
	});
	//]]>
	" ;

	$document = JFactory::getDocument();
	$document->addScriptDeclaration($js);
}
?>

<script type="text/javascript">
jQuery("#ula-treeview-virtuemart<?php echo $module->id; ?> ul").treeview({
	animated: 	"<?php echo $params->get('animated', 1); ?>",
	persist: 	"<?php echo $params->get('persist', 'cookie'); ?>",
	collapsed: 	<?php echo $params->get('collapsed', 1) ? "true" : "false"; ?>,
	unique:		<?php echo $params->get('unique', 1) ? "true" : "false"; ?>,
	<?php if($params->get('showControl', 1)) { ?>
	control: "#ula-treeview-treecontrol<?php echo $module->id; ?>",
	<?php } ?>
});
</script>