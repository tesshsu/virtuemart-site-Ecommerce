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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<div id="filter-bar" class="btn-toolbar">
		 <div class="filter-search btn-group pull-left input-append">
			<!--label for="filter_search" class="felement-invisible"><?php echo JText::_( 'JSEARCH_FILTER_LABEL' ); ?></label-->
	        <input type="text" name="filter_search" class="input-medium" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
        </div>
        <div class="btn-group pull-right hidden-phone">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div class="filter-select btn-group pull-right ">
            <select name="filter_extension" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', VMMigrateHelperVMMigrate::GetMigratorsOptions(), 'value', 'text', $this->state->get('filter.extension'));?>
			</select>
            <select name="filter_task" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', VMMigrateHelperVMMigrate::GetMigratorsStepsOptions($this->state->get('filter.extension')), 'value', 'text', $this->state->get('filter.task'));?>
			</select>
            <select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', VMMigrateHelperVMMigrate::GetLogStatesOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>
		</div>
	</div>
	<div class="clearfix"> </div>
        <table class="adminlist table table-striped">
		<thead>
			<tr>
               <th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>                                
				<th class="title" width="10%">
                	<?php echo JHtml::_('grid.sort',   'VMMIGRATE_EXTENSION', 'extension', $listDirn, $listOrder); ?>
				</th>
                <th class="title" width="20%">
                	<?php echo JHtml::_('grid.sort',   'VMMIGRATE_TASK', 'task', $listDirn, $listOrder); ?>
				</th>
				<th width="40%" class="nowrap">
                	<?php echo JText::_('VMMIGRATE_NOTE'); ?>
				</th>
				<th width="5%" class="nowrap">
                	<?php echo JHtml::_('grid.sort',   'VMMIGRATE_STATE', 'state', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'VMMIGRATE_SOURCE_ID', 'source_id', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHtml::_('grid.sort',   'VMMIGRATE_DESTINATION_ID', 'destination_id', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'created', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php 
		$tick 		= JHtml::_('image','admin/tick.png', '', array('border' => 0), true);
		$publish_x 	= JHtml::_('image','admin/publish_x.png', '', array('border' => 0), true);

		foreach ($this->items as $i => $item) :	?>
			<tr class="row<?php echo $i % 2; ?>">
                <td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="left">
					<?php echo JText::_($item->extension); ?>
				</td>
                <td class="left">
					<?php echo JText::_($item->task);?>
				</td>
				<td class="left">
					<?php echo $item->note;?>
				</td>
				<td class="center">
					<?php 
					switch ($item->state) {
						case 2:
							echo JHtml::_('image','admin/publish_y.png', '', array('border' => 0), true);
							break;
						case 3:
							echo JHtml::_('image','admin/publish_x.png', '', array('border' => 0), true);
							break;
						case 1:
						case 4:
						default:
							echo JHtml::_('image','admin/tick.png', '', array('border' => 0), true);
							break;
					};
					?>
				</td>
				<td class="center">
					<?php echo $item->source_id;?>
				</td>
				<td class="center">
					<?php echo $item->destination_id;?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4').' H:i');?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div>
        <input type="hidden" name="option" value="com_vmmigrate" />
        <input type="hidden" name="view" value="log" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
