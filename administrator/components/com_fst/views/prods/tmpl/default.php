<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<form action="<?php echo FSTRoute::x( 'index.php?option=com_fst&view=prods' );?>" method="post" name="adminForm" id="adminForm">
<?php $ordering = ($this->lists['order'] == "c.ordering"); ?>
<div id="editcell">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_("FILTER"); ?>:
				<input type="text" name="search" id="search" value="<?php echo JViewLegacy::escape($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_("FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID");?>"/>
				<button onclick="this.form.submit();"><?php echo JText::_("GO"); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('ispublished').value='-1';this.form.submit();"><?php echo JText::_("RESET"); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
				echo $this->lists['published'];
				?>
				<?php FSTAdminHelper::LA_Filter(true); ?>
			</td>
		</tr>
	</table>

    <table class="adminlist table table-striped">
    <thead>

        <tr>
			<th width="5">#</th>
            <th width="20">
   				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->data ); ?>);" />
			</th>
            <th>
                <?php echo JHTML::_('grid.sort',   'Title', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
            </th>
            <th>
                <?php echo JHTML::_('grid.sort',   'Image', 'image', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
            </th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Published', 'published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
<!--  -->
			<?php FSTAdminHelper::LA_Header($this, true); ?>
             <th width="<?php echo FSTJ3Helper::IsJ3() ? '130px' : '8%'; ?>">
				<?php echo JHTML::_('grid.sort',   'Order', 'ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				<?php if ($ordering) echo JHTML::_('grid.order',  $this->data ); ?>
			</th>
		</tr>
    </thead>
    <?php

    $k = 0;
    for ($i=0, $n=count( $this->data ); $i < $n; $i++)
    {
        $row =& $this->data[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
        $link = FSTRoute::x( 'index.php?option=com_fst&controller=prod&task=edit&cid[]='. $row->id );
    	
		$published = FST_GetPublishedText($row->published);
    	$iskb = "<a href='#' class='toggle_me' field='inkb' row='{$row->id}' value='{$row->inkb}'>".FST_GetYesNoText($row->inkb)."</a>";
    	$issupport = "<a href='#' class='toggle_me' field='insupport' row='{$row->id}' value='{$row->insupport}'>".FST_GetYesNoText($row->insupport)."</a>";
    	$istest = "<a href='#' class='toggle_me' field='intest' row='{$row->id}' value='{$row->intest}'>".FST_GetYesNoText($row->intest)."</a>";
    	
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->id; ?>
            </td>
           	<td>
   				<?php echo $checked; ?>
			</td>
			<td>
			    <a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
           	<td>
   				<?php echo $row->image; ?>
</td>
<td align="center">
				<a href="javascript:void(0);" class="jgrid btn btn-micro" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? 'unpublish' : 'publish' ?>')">
					<?php echo $published; ?>
				</a>
			</td>
<!--  -->
			<?php FSTAdminHelper::LA_Row($row, true); ?>
			<td class="order">
			<?php if ($ordering) : ?>
				<span><?php echo $this->pagination->orderUpIcon( $i ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n ); ?></span>
			<?php endif; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php if (!$ordering) {echo 'disabled="disabled"';} ?> class="text_area" style="text-align: center" />
			</td>
		</tr>
        <?php
        $k = 1 - $k;
    }
    ?>
	<tfoot>
		<tr>
			<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>

    </table>
</div>

<input type="hidden" name="option" value="com_fst" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="prod" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<script>
jQuery(document).ready(function () {
	jQuery('.toggle_me').click(function (ev) {
		ev.preventDefault();
		var val = jQuery(this).attr('value');
		var field = jQuery(this).attr('field');
		var rowid = jQuery(this).attr('row');
		val = 1 - val;
		var t = this;

		jQuery(this).attr('value',val);

		var url = '<?php echo FSTRoute::x('index.php?option=com_fst&view=prods&what=togglefield',false); ?>&id=' + rowid + '&val=' + val + '&field=' + field;
		jQuery(t).html('--');
		jQuery.ajax({
			url: url,
			context: document.body,
			success: function(result){
				jQuery(t).html(result);
			}
		});
	});
});

</script>
