<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
defined('JPATH_BASE') or die;
use Joomla\Registry\Registry;
$data = array();
// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();
if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}
// Options
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);
$filterFields = $_REQUEST;
$form = JForm::getInstance('form', JPATH_ROOT.'/administrator/components/com_asvm/models/forms/filter_orders.xml');
JHtml::_('behavior.tooltip');
$activetab = (isset($_REQUEST['active_tab']) && !empty($_REQUEST['active_tab'])) ? $_REQUEST['active_tab'] : "searchbyorder";
?>
 <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => $activetab)); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'searchbyorder', JText::_('COM_ASVM_ORDER_SEARCH_BY_ORDER', true)); ?>				
		<!--Search by Order -->
		<div>	
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('order_number','filter'); ?>
				</span>
			   <?php echo $form->getInput('order_number','filter',(isset($filterFields['filter']['order_number']) ? $filterFields['filter']['order_number'] : '')); ?>
			  </div>
			</div>
			
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('order_id','filter'); ?>
				</span>
			   <?php echo $form->getInput('order_id','filter',(isset($filterFields['filter']['order_id']) ? $filterFields['filter']['order_id'] : '')); ?>
			  </div>
			</div>
			
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('product_sku','filter'); ?>
				</span>
			   <?php echo $form->getInput('product_sku','filter',(isset($filterFields['filter']['product_sku']) ? $filterFields['filter']['product_sku'] : '')); ?>
			  </div>
			</div>
			
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('produt_name','filter'); ?>
				</span>
			   <?php echo $form->getInput('produt_name','filter',(isset($filterFields['filter']['produt_name']) ? $filterFields['filter']['produt_name'] : '')); ?>
			  </div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('order_status','filter'); ?>
				</span>
			   <?php echo $form->getInput('order_status','filter',(isset($filterFields['filter']['order_status']) ? $filterFields['filter']['order_status'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('payment_method','filter'); ?>
				</span>
			   <?php echo $form->getInput('payment_method','filter',(isset($filterFields['filter']['payment_method']) ? $filterFields['filter']['payment_method'] : '')); ?>
			  </div>
			</div>
		</div>
		
   <?php echo JHtml::_('bootstrap.endTab'); ?>
   
   <!--Search by date -->
   <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'searchbydate', JText::_('COM_ASVM_ORDER_SEARCH_BY_DATE')); ?>
		<div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('from','filter'); ?>
				</span>
			   <?php echo $form->getInput('from','filter',(isset($filterFields['filter']['from']) ? $filterFields['filter']['from'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('to','filter'); ?>
				</span>
			   <?php echo $form->getInput('to','filter',(isset($filterFields['filter']['to']) ? $filterFields['filter']['to'] : '')); ?>
			  </div>
			</div>
		</div>
   <?php echo JHtml::_('bootstrap.endTab'); ?>
   
   <!--Search by Customer -->
   <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'searchbycustomer', JText::_('COM_ASVM_ORDER_SEARCH_BY_CUSTOMER')); ?>
		<div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('first_name','filter'); ?>
				</span>
			   <?php echo $form->getInput('first_name','filter',(isset($filterFields['filter']['first_name']) ? $filterFields['filter']['first_name'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('last_name','filter'); ?>
				</span>
			   <?php echo $form->getInput('last_name','filter',(isset($filterFields['filter']['last_name']) ? $filterFields['filter']['last_name'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('email','filter'); ?>
				</span>
			   <?php echo $form->getInput('email','filter',(isset($filterFields['filter']['email']) ? $filterFields['filter']['email'] : '')); ?>
			  </div>
			</div>
			
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('address','filter'); ?>
				</span>
			   <?php echo $form->getInput('address','filter',(isset($filterFields['filter']['address']) ? $filterFields['filter']['address'] : '')); ?>
			  </div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('city','filter'); ?>
				</span>
			   <?php echo $form->getInput('city','filter',(isset($filterFields['filter']['city']) ? $filterFields['filter']['city'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('state','filter'); ?>
				</span>
			   <?php echo $form->getInput('state','filter',(isset($filterFields['filter']['state']) ? $filterFields['filter']['state'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('country','filter'); ?>
				</span>
			   <?php echo $form->getInput('country','filter',(isset($filterFields['filter']['country']) ? $filterFields['filter']['country'] : '')); ?>
			  </div>
			</div>
			<div class="span3">
			  <div class="input-group">
				<span class="input-group-addon">
				  <?php echo $form->getLabel('zip','filter'); ?>
				</span>
			   <?php echo $form->getInput('zip','filter',(isset($filterFields['filter']['zip']) ? $filterFields['filter']['zip'] : '')); ?>
			  </div>
			</div>
		</div>
   <?php echo JHtml::_('bootstrap.endTab'); ?>
	<input type="hidden" name="active_tab" id="active_tab" value="<?php echo $activetab; ?>" />
  <?php echo JHtml::_('bootstrap.endTab'); ?>
<div class="clearfix"></div><div class="clearfix"></div>
<br>
<button  type="submit" class="btn btn-info" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
<button id="toolbar-download" type="button" class="btn btn-warning" title="<?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?>"><span class="icon-download"></span><?php echo JText::_('COM_ASVM_EXPORT_BUTTON'); ?></button>
<hr>
<script>
	jQuery(function(){
		jQuery('.clearbtnnew').click(function(){
			jQuery('#adminForm input.clr').val('');
			jQuery('#adminForm select').val('');
			jQuery('#adminForm').submit();
		});
		jQuery('#myTabTabs li').live('click',function(){
			var activeTab 	= jQuery(this).find('a').attr('href');	
			var activeTabId = activeTab.replace("#", "");
			jQuery('#active_tab').val(activeTabId);
		});	
	})
</script>