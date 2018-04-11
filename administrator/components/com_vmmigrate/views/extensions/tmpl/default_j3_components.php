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

$tick 		= JHtml::_('image','admin/tick.png', '', array('border' => 0), true);
$publish_x 	= JHtml::_('image','admin/publish_x.png', '', array('border' => 0), true);
?>
<table class="adminlist table table-striped table-hover">
    <thead>
        <tr>
            <th class="title nowrap" width="40%">
                <?php echo JText::_('VMMIGRATE_EXTENSION'); ?>
            </th>
            <th class="title nowrap" width="20%">
                <?php echo JText::_('COM_INSTALLER_HEADING_LOCATION'); ?>
            </th>
            <th class="title center" width="20%">
                <?php echo JText::_('VMMIGRATE_VERSION_SRC'); ?>
            </th>
            <th class="title center" width="20%">
                <?php echo JText::_('VMMIGRATE_VERSION_DST'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($this->components as $i => $item) :	?>
        <tr class="<?php echo ($item->version_dst > 0) ? 'success' : 'error';?>">
            <td class="left">
                <span class="bold hasTooltip" title="<?php echo JHtml::tooltipText($item->name, $item->element, 0); ?>">
                    <?php echo $item->name; ?>
                </span>
            </td>
            <td class="left">
                <?php echo $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE'); ?>
            </td>
            <td class="center">
                <?php echo $item->version_src;?>
            </td>
            <td class="center">
                <?php echo ($item->version_dst > 0) ? $item->version_dst : $publish_x;?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

