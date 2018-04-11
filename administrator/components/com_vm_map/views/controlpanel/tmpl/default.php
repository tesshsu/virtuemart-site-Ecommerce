<?php
/*------------------------------------------------------------------------
 * com_vmg_export - Virtuemart 2 google export tool
 * ------------------------------------------------------------------------
 * St42 - P. Kohl
 * copyright Copyright (C) 2011 st42.fr. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.st42.fr
 */

defined('_JEXEC') or die();
VmConfig::loadConfig();
$vmlangs = VmConfig::get('active_languages');
if (empty($vmlangs)) {
	$lang = JFactory::getLanguage();
	$vmlangs = array($lang->getDefault());
}

?>
<fieldset class="adminform">
<legend><?php echo JText::_('COM_VM_MAP') ?></legend>
 <div style="width:256px;float:left;padding-left:20px">
			<img src="<?php echo juri::root(); ?>administrator/components/com_vm_map/images/sitemap256.png" />
</div>
<div style="float:left;padding:20px">
	<div><?php
	foreach ($vmlangs as $lg) { 
		$tag = substr($lg,0,2) ?>
		<div><a class="button btn btn-default" href="<?php echo jRoute::_(juri::root().'index.php?option=com_vm_map&lang='.$tag) ?>">SiteMap <?php echo $lg ?></a><br>
		<label> GOOGLE SiteMap > </label><input type='text' size="100" value='<?php echo jRoute::_(juri::root(true).'/index.php?option=com_vm_map&lang='.$tag) ?>'/>
		</div>
	<?php } ?>
	</div>
	<div style="clear:both;"><i><?php echo JText::_( "COM_VM_MAP_DESCRIPTION" ) ; ?></i></div>
	<br>
	<p>
	<?php echo JText::_( "COM_VM_MAP_XML_DESCRIPTION" ) ; ?>
	</p>
	<small>&copy; <a href="http://www.st42.fr" target="_new">Studio 42 - P. Kohl</a> </small>

</div>
<div style="clear:both;"></div>
</fieldset>