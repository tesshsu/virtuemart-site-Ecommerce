<?php 
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); 

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

	
?>
<div class="clr"></div>

<?php
if ($this->mode == 'db') {
	?><h2><?php echo JText::_('VMMIGRATE_DATABASE_SETTINGS_LABEL'); ?></h2><?php
	if (!$this->valid_database_connection) {
		?>
		<div class="alert alert-error">
			<strong>Error!</strong> <?php echo JText::_('VMMIGRATE_SOURCE_DATABASE_CONNECTION_WARNING'); ?>.
		</div>    
		<?php
	} else if (!$this->validPrefix) {
		?>
		<div class="alert alert-error">
			<strong>Error!</strong> <?php echo JText::_('VMMIGRATE_SOURCE_DATABASE_CONNECTION_WARNING'); ?>.
		</div>    
		<?php
	} else {
		?>
		<div class="alert alert-success">
			<strong>Success!</strong> <?php echo JText::_('VMMIGRATE_SOURCE_DATABASE_CONNECTION_SUCCESS'); ?>.
		</div>    
		<?php
	}
}
if ($this->mode == 'files') {
	?><h2><?php echo JText::_('VMMIGRATE_FTP_SETTINGS_LABEL'); ?></h2><?php
	if ($this->valid_source_path) {
		?>
		<div class="alert alert-success">
			<strong>Success!</strong> <?php echo JText::_('VMMIGRATE_SOURCE_PATH_STATUS_SUCCESS'); ?>.
		</div>    
		<?php
	} else {
		?>
		<div class="alert alert-error">
			<strong>Error!</strong> <?php echo JText::_('VMMIGRATE_SOURCE_PATH_STATUS_WARNING'); ?>.
		</div>    
        <div class="well">
        	<?php echo JText::_('VMMIGRATE_FTP_SETTINGS_DESC'); ?>
        </div>
		<?php
	}
}
?>
<div class="clr"></div>
<?php 
