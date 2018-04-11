<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<form action="<?php echo FSTRoute::x( 'index.php?option=com_fst&view=tests' );?>" method="post" name="adminForm" id="adminForm">
<?php $ordering = ($this->lists['order'] == "ordering"); ?>
<div id="editcell">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_("FILTER"); ?>:
				<input type="text" name="search" id="search" value="<?php echo JViewLegacy::escape($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_("FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID");?>"/>
				<button onclick="this.form.submit();"><?php echo JText::_("GO"); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();this.form.getElementById('prod_id').value='0';this.form.getElementById('ispublished').value='-1';"><?php echo JText::_("RESET"); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
// 
				if (array_key_exists("published",$this->lists)) echo $this->lists['published'];
				?>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped">
	<thead>

		<tr>
			<th width="5">#</th>
			<th width="20" class="title">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th  class="title" width="8%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Name', 'name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<!-- If not got a type selected -->
			
			<!-- If a type has been selected, then be more specific -->
			<?php if ($this->ident): ?>
<!--//  -->
			<?php else: ?>
<!--//  -->
				<th  class="title" width="8%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'Article/Product', 'itemid', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			<?php endif; ?>
			
			<th class="title">
				<?php echo JText::_("BODY"); ?>
			</th>
			<th  class="title" width="8%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Added', 'added', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'MOD_STATUS', 'published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<?php

	$k = 0;
	for ($i=0, $n=count( $this->data ); $i < $n; $i++)
	{
		$row =& $this->data[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		$link = FSTRoute::x( 'index.php?option=com_fst&controller=test&task=edit&cid[]='. $row->id );

		$published = FST_GetModerationText($row->published);

		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $row->name; ?>
			</td>
			<!--//  -->
			<td>
				<?php  if (!empty($this->comment_objs[$row->ident]->handler)) echo $this->comment_objs[$row->ident]->handler->GetItemTitle($row->itemid); ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php
					$body = strip_tags($row->body);
					if (strlen($body) > 250)
						$body = substr($body,0,250) . "...";

					echo $body;
				?></a>
			</td>
			<td>
				<?php echo FST_Helper::Date($row->added,FST_DATETIME_MID); ?>
			</td>
			<td align="center">
				<a href="#" class="modchage" id="comment_<?php echo $row->id;?>" current='<?php echo $row->published; ?>'>
					<?php echo $published; ?>
				</a>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	<tfoot>
		<tr>
			<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>

	</table>
</div>

<input type="hidden" name="option" value="com_fst" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="test" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<script>
jQuery(document).ready(function () {
	jQuery('.modchage').click( function () {
		var id = jQuery(this).attr('id').split('_')[1];
		var current = jQuery(this).attr('current');
		if (current == 1)
		{
			fst_remove_comment(id);		
		} else {
			fst_approve_comment(id);
		}
	});
});


function fst_remove_comment(commentid) {
	var obj = jQuery('#comment_' + commentid);
	obj.attr('current',2);
	var img = jQuery('#comment_' + commentid + ' img');
	var src = img.attr('src');
	
	var curimg = src.split("/").pop();
	src = src.replace(curimg, "declined.png");
	img.attr('src',src);
	
	var url = "<?php echo FSTRoute::x('index.php?option=com_fst&view=tests&task=removecomment&commentid=XXCIDXX',false); ?>";
	url = url.replace("XXCIDXX",commentid);
	jQuery.get(url);
}
function fst_approve_comment(commentid) {
	var obj = jQuery('#comment_' + commentid);
	obj.attr('current',1);
	var img = jQuery('#comment_' + commentid + ' img');
	var src = img.attr('src');
	
	var curimg = src.split("/").pop();
	src = src.replace(curimg, "accepted.png");
	img.attr('src',src);
	
	var url = "<?php echo FSTRoute::x('index.php?option=com_fst&view=tests&task=approvecomment&commentid=XXCIDXX',false); ?>";
	url = url.replace("XXCIDXX",commentid);
	jQuery.get(url);
}

</script>