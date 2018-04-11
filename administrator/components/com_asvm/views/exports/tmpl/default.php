<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.formvalidator');
$language = JFactory::getLanguage();
$language->load('com_virtuemart');
$language->load('com_virtuemart', JPATH_ADMINISTRATOR, 'en-GB', true);
$language->load('com_virtuemart', JPATH_ADMINISTRATOR.'/components/com_virtuemart', 'en-GB', true);
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_asvm/assets/css/asvm.css');
$document->addScript('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js');
$exportkey = $this->exportkey;
$activetab = "searchbyorder";
$selected = $this->data['selected'];
$countcids = $this->data['countcids'];
$countitems = $this->data['countitems'];
$countallitems = $this->data['countallitems'];
?>
<form action="<?php echo JRoute::_('index.php?option=com_asvm&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">	
 <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => $activetab)); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'searchbyorder', JText::_('COM_ASVM_FIELD_TO_EXPORT', true)); ?>				
		<!--Search by Order -->
			<div class="form-horizontal">		
				<div class="select">
					<select name="export_choose" class="export_choose">
						<option value="selected" <?php echo ($selected == 1)?('selected="1"'):'';?>><?php echo JText::_("COM_ASVM_EXPORT_EXPORT_SELECTED_ROW");?> (<?php echo $countcids;?>) </option>
						<option value="search" <?php echo ($selected == 2)?('selected="1"'):'';?>><?php echo JText::_("COM_ASVM_EXPORT_EXPORT_SEARCH_ROW");?> (<?php echo $countitems;?>)</option>
						<option value="all" <?php echo ($selected == 3)?('selected="1"'):'';?>><?php echo JText::_("COM_ASVM_EXPORT_EXPORT_ROW");?> (<?php echo $countallitems;?>)</option>
					</select>
				</div>
				
				<hr>
			<table class="table table-striped">
			<thead>
				<tr>
					<th width="100"><?php echo JText::_("COM_ASVM_EXPORT_EXPORT_COLUMN_ORDER");?></th>
					<th width="200"><input type="checkbox" class="selectall" style="margin-top: -2px;" checked="1"> <?php echo JText::_("COM_ASVM_EXPORT_EXPORT_COLUMN_EXPORT");?></th>
					<th><?php echo JText::_("COM_ASVM_EXPORT_EXPORT_COLUMN_SUBMISSION");?></th>
					<th></th>
				</tr>
			</thead>
			<tbody class="dragable">
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span> <span class="orderno">1</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="order_id" date-toggle="order_id" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_ID");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">2</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="order_number" date-toggle="order_number" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_NO");?></td>
				<td></td>
			</tr>			
			
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">3</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="product_name" date-toggle="product_name" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_PRODUCT_NAME");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">4</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="sku" date-toggle="sku" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_SKU");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">5</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true" date-toggle="order_date"  value="order_date" date-toggle="order_date" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_DATE");?></td>
				<td>
					<div  class="">
						<select name="date_format">
							<option value="l, d F Y"><?php echo  date('l, d F Y'); ?></option>
							<option value="l, d F Y H:i"><?php  echo date('l, d F Y H:i'); ?></option>
							<option value="d F Y"><?php echo  date('d F Y'); ?></option>
							<option value="Y-m-d"><?php echo  date('Y-m-d'); ?></option>
							<option value="y-m-d"><?php echo  date('y-m-d'); ?></option>
						</select>
					</div>
				</td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">6</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="product_price" date-toggle="product_price" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_PRODUCT_PRICE");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">7</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="qty" date-toggle="qty" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_QTY");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">8</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="product_in_stock" date-toggle="product_in_stock" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_PRODUCT_STOCK");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">9</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="secret_key" date-toggle="secret_key" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_SECRET_KEY");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">10</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="bill_to" date-toggle="bill_to" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_BILL_TO");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">11</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="ship_to" date-toggle="ship_to" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_SHIP_TO");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">12</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="coupon" date-toggle="coupon" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_COUPON");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">13</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="discount" date-toggle="discount" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_DISCOUNT");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">14</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="order_status" date-toggle="order_status" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_STATUS");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">15</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="total_tax" date-toggle="total_tax" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_TOTAL_TAX");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">16</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="order_total" date-toggle="order_total" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_ORDER_TOTAL");?></td>
				<td></td>
			</tr>
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">17</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="payment_method" date-toggle="payment_method" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_PAYMENT_METHOD");?></td>
				<td></td>
			</tr>
			
			<tr>
				<td><span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span><span class="orderno">18</span></td>
				<td><input class="checkbox" type="checkbox" name="export[]" checked="true"  value="comment" date-toggle="comment" ></td>
				<td><?php echo JText::_("COM_ASVM_EXPORT_COMMENT");?></td>
				<td></td>
			</tr>
			</tbody>
			</table>
			</div>
		
   <?php echo JHtml::_('bootstrap.endTab'); ?>
   
   <!--Search by date -->
   <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'searchbydate', JText::_('COM_ASVM_CSV_OPTION')); ?>
		<div class="form-horizontal">			
			<div class="control-group">
				<span class="control-label">
				  <label id="filter_include_columnheader-lbl" for="filter_include_columnheader" class="hasTooltip" title="" data-original-title="Include Column Headers"><?php echo JText::_("COM_ASVM_EXPORT_INCLUDE_COLUMN");?> :</label>
				</span>
				<div class="controls">
			   <input type="checkbox" name="filter[include_columnheader]" id="filter_include_columnheader" value="1" class="clr" checked="true">			</div>
			</div>
			
			<div class="control-group">
				<span class="control-label">
				  <label id="filter_delimiter-lbl" for="filter_delimiter" class="hasTooltip" title="" data-original-title="Delimiter">	<?php echo JText::_("COM_ASVM_EXPORT_DELIMITER");?>:</label>
				</span>
				<div class="controls">
			   <input type="text" name="filter[delimiter]" id="filter_delimiter" value="," class="clr" placeholder="Delimiter">			</div>
			</div>
			<div class="control-group">
				<span class="control-label">
				  <label id="filter_enclosure-lbl" for="filter_enclosure" class="hasTooltip" title="" data-original-title="Field Enclosure"><?php echo JText::_("COM_ASVM_EXPORT_FIELD_ENCLOSURE");?>:</label>
				</span>
				<div class="controls">
					<input type="text" name="filter[enclosure]" id="filter_enclosure" value='"' class="clr" placeholder="Field Enclosure">			 
			   </div>
			  </div>
		</div>
   
  <?php echo JHtml::_('bootstrap.endTab'); ?>
<br> 
<button  type="button" id="submit_button" class="btn btn-warning" title="<?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?>"><span class="icon-download"></span>
	<?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?>
</button>
<hr>	
</div>
<input type="hidden" name="task" value="orders.export">	
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="myModal" style="display:none">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"><?php echo JText::_("COM_ASVM_EXPORT_PREVIEW");?></h4>
		  </div>
		  <div class="modal-body">
				<div class="downlaod_section">
					
				</div>
		  </div><!-- /.modal-dialog -->
		<div class="modal-footer">
			<div class="left" style="float:left">
			<button  type="submit" id="submit_buttons" class="btn btn-warning" title="<?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?>"><span class="icon-download"></span>
				<?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?>
			</button>  
			<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="icon-cancel"></span><?php echo JText::_("JTOOLBAR_CLOSE");?></button>
 
			</div>			
		</div>
	  
		</div><!-- /.modal -->
	</div><!-- /.modal -->
</div><!-- /.modal -->
</form>
<div class="loader" style="display:none;">
	<div class="loader-inner">
		<img src="<?php echo JURI::root();?>administrator/components/com_asvm/assets/images/loader.gif">
	</div>
</div>
<style>
#myModal .modal-body {overflow: inherit !important;max-height: 430px;}
.loader {position: fixed;top: 0;left: 0;z-index: 99999999;width: 100%;height: 100%;background-color: rgba(85, 85, 85, 0.14);text-align: center;padding-top: 20%;}
table.table.table-bordered tr:first-child td {white-space: nowrap;}
div#myModal {width: 90%;height:90%;    left: 5%;margin-left: 0%;}
</style>
<script language="javascript">
	baseurl = '<?php echo JURI::getInstance();?>';
	jQuery(".selectall").on('change',function(){
		if(jQuery(this).prop( "checked")){
			jQuery(".form-horizontal .checkbox").prop( "checked", true );
		}
		else{
			jQuery(".form-horizontal .checkbox").prop( "checked", false );
		}
	});
	
	jQuery(".form-horizontal input[type=checkbox]").click(function(){
		var toggleClass = jQuery(this).attr('date-toggle');
		jQuery("."+toggleClass).toggle();
	});	
	
	jQuery("#submit_button").on('click',function(){
		 jQuery(".loader").show()
		 jQuery.ajax({
		  url: jQuery('#adminForm').attr('action')+'&prev=1',
		  type: 'post',
		  data :jQuery('#adminForm').serialize(),
		  success: function(response){
			  jQuery(".loader").hide()
			  jQuery('#myModal').modal('show');
			  jQuery(".downlaod_section").html(response);
		  }});
	});
	
	
	jQuery(".dragable").sortable({
		items: "> tr",
		appendTo: "parent",
		helper: "clone",
		update :  function(event, ui) {
			jQuery(".orderno").each(function(i){
				jQuery(this).text((i + 1));
			})
		},
	}).disableSelection();
</script>