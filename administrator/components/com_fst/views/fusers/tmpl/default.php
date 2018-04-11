<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

function GetArtPermText($id)
{
	switch ($id)
	{
		case 1:
			return JText::_("AUTHOR");
		case 2:
			return JText::_("EDITOR");
		case 3:
			return JText::_("PUBLISHER");
		default:
			return JText::_('ART_NONE');	
	}	
}
?>
<form action="<?php echo FSTRoute::x( 'index.php?option=com_fst&view=fusers' );?>" method="post" name="adminForm" id="adminForm">
<?php $ordering = ($this->lists['order'] == "ordering"); ?>
<?php JHTML::_('behavior.modal'); ?>
<div id="editcell">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_("FILTER"); ?>:
				<input type="text" name="search" id="search" value="<?php echo JViewLegacy::escape($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_("FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID");?>"/>
				<button onclick="this.form.submit();"><?php echo JText::_("GO"); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_("RESET"); ?></button>
			</td>
			<td nowrap="nowrap">
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
                <?php echo JHTML::_('grid.sort',   'Username', 'username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
            </th>
<!--  -->
            <th width="8%">
                <?php echo JHTML::_('grid.sort',   'MODERATOR', 'mod_kb', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
<!--  -->
		</tr>
    </thead>
    <?php

    $k = 0;
    for ($i=0, $n=count( $this->data ); $i < $n; $i++)
    {
        $row =& $this->data[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
        $link = FSTRoute::x( 'index.php?option=com_fst&controller=fuser&task=edit&cid[]='. $row->id );

		if ($row->mod_kb)
		{
			$kb_img = "tick";
		} else {
			$kb_img = "cross";
		}
		
		if ($row->mod_test)
		{
			$test_img = "tick";
		} else {
			$test_img = "cross";
		}
		
		if ($row->support)
		{
			$supp_img = "tick";
		} else {
			$supp_img = "cross";
		}
    	
    	if ($row->seeownonly)
    	{
    		$own_img = "tick";
    	} else {
    		$own_img = "cross";
    	}
    	
    	if ($row->autoassignexc)
    	{
    		$auto_img = "tick";
    	} else {
    		$auto_img = "cross";
    	}
    	
    	if ($row->groups)
    	{
    		$group_img = "tick";
    	} else {
    		$group_img = "cross";
    	}
    	
    	if (isset($row->reports) && $row->reports)
    	{
    		$reports_img = "tick";
    	} else {
    		$reports_img = "cross";
    	}

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->id; ?>
            </td>
           	<td>
   				<?php echo $checked; ?>
			</td>
			<td>
			    <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
			</td>
<!--  -->
			<td align='center'>
				<img src='<?php echo JURI::base(); ?>/components/com_fst/assets/<?php echo $kb_img; ?>.png' width='16' height='16' />
			</td>
<!--  -->
		</tr>
        <?php
        $k = 1 - $k;
    }
    ?>
	<tfoot>
		<tr>
			<td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>

    </table>
</div>

<input type="hidden" name="option" value="com_fst" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="fuser" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

