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
// no direct access
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"> </div>
    
    <p class="lead"><?php echo JText::_('VMMIGRATE_EXTENSIONS_DESC');?></p>

	<?php

    echo JHtml::_('bootstrap.startAccordion', 'statsAccordion', array('active' => ''));
    echo JHtml::_('bootstrap.addSlide', 'statsAccordion', JText::_('COM_INSTALLER_TYPE_PACKAGE'), 'packages');
	echo $this->loadTemplate('packages');
    echo JHtml::_('bootstrap.endSlide');
    echo JHtml::_('bootstrap.addSlide', 'statsAccordion', JText::_('COM_INSTALLER_TYPE_COMPONENT'), 'components');
	echo $this->loadTemplate('components');
    echo JHtml::_('bootstrap.endSlide');
    echo JHtml::_('bootstrap.addSlide', 'statsAccordion', JText::_('COM_INSTALLER_TYPE_MODULE'), 'modules');
	echo $this->loadTemplate('modules');
    echo JHtml::_('bootstrap.endSlide');
    echo JHtml::_('bootstrap.addSlide', 'statsAccordion', JText::_('COM_INSTALLER_TYPE_PLUGIN'), 'plugins');
	echo $this->loadTemplate('plugins');
    echo JHtml::_('bootstrap.endSlide');
    echo JHtml::_('bootstrap.addSlide', 'statsAccordion', JText::_('COM_INSTALLER_TYPE_TEMPLATE'), 'template');
	echo $this->loadTemplate('templates');
    echo JHtml::_('bootstrap.endSlide');
    echo JHtml::_('bootstrap.endAccordion');
	?>
    
	<div>
        <input type="hidden" name="option" value="com_vmmigrate" />
        <input type="hidden" name="view" value="extensions" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
