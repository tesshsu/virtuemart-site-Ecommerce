<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
 
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.formvalidator');
$language = JFactory::getLanguage();
$language->load('com_virtuemart');
$language->load('com_virtuemart', JPATH_ADMINISTRATOR, 'en-GB', true);
$language->load('com_virtuemart', JPATH_ADMINISTRATOR.'/components/com_virtuemart', 'en-GB', true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_asvm/assets/css/asvm.css');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$filters 	= $this->filterForm->getGroup('filter');
$apps = JFactory::getApplication();
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components'.'/com_virtuemart'.'/helpers'.'/config.php');
$config = VmConfig::loadConfig();
DEFINE('DS', DIRECTORY_SEPARATOR );
if (!class_exists('CurrencyDisplay'))
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();
		
		
$currency_model = VmModel::getModel('currency');
/* $displayCurrency = $currency_model->getCurrency($this->product->product_currency);
echo $displayCurrency->currency_name;
echo $displayCurrency->currency_code_3;
echo $displayCurrency->currency_symbol;
*/
 
 //echo "<pre>";print_r($this->items);exit;
	$db = JFactory::getDBO();
?>
<form action="<?php echo JRoute::_('index.php?option=com_asvm&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar" style="display:none">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
					<?php echo $filters['filter_search']->input; ?>
				</div>
				<div class="btn-group pull-left">					
					<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear clearbtnnew" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
						<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
					</button>	
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php //echo $this->pagination->getLimitBox(); ?>
				</div>
		</div>
		<div class="clearfix"></div>
		<div class="clearfix"></div>			
		<?php echo $this->loadTemplate('searchtabs');
		 if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
		?>
		<div>
			<table width="30%">
			<?php foreach($_REQUEST['filter'] as $k=>$v){ 
				if(!empty($v)){					
			?>
					<tr>
						<td width="35%">
							<?php echo ucwords(str_replace('_',' ',$k)); ?> 
						</td>		
						<td>:</td>	
						<td>
							<?php 
							if(is_array($v)){
								switch($k){
									case 'product_sku' : 
										$multiselectvalue =  implode(',',$v);
									break;
									
									case 'produt_name' : 
										$pid = implode(',',$v);
										$db->setQuery("SELECT product_name FROM #__virtuemart_products_en_gb WHERE virtuemart_product_id IN($pid)");
										$resut = $db->loadObjectList();
										$multiselectvalue = '';
										if(!empty($resut)){
											foreach($resut as $p=>$pv){
												$multiselectvalue .= ($p <= 0) ? $pv->product_name : ', '.$pv->product_name;
											}
										}
									break;
									
									case 'order_status' : 
										
										$orstatus = '';
										foreach($v as $k=>$os){
											if($k == 0){
												$orstatus .= " order_status_code = ".$db->quote($os);
											}else{
												$orstatus .= " OR order_status_code = ".$db->quote($os);
											}
											
										}				
										
										$db->setQuery("SELECT order_status_name FROM #__virtuemart_orderstates WHERE $orstatus");
										$resut = $db->loadObjectList();
										$multiselectvalue = '';
										if(!empty($resut)){
											foreach($resut as $p=>$pv){
												$multiselectvalue .= ($p <= 0) ? JText::_(str_replace('COM_VIRTUEMART','COM_ASVM',$pv->order_status_name )): ', '.JText::_(str_replace('COM_VIRTUEMART','COM_ASVM',$pv->order_status_name ));
											}
										} 
									break;
									
									case 'payment_method' : 
										$pmtid = implode(',',$v);
										$db->setQuery("SELECT payment_name FROM #__virtuemart_paymentmethods_en_gb WHERE virtuemart_paymentmethod_id IN($pmtid)");
										$resut = $db->loadObjectList();
										$multiselectvalue = '';
										if(!empty($resut)){
											foreach($resut as $p=>$pv){
												$multiselectvalue .= ($p <= 0) ? $pv->payment_name : ', '.$pv->payment_name;
											}
										}
									break;
									
									case 'country' : 
										$cid = implode(',',$v);
										$db->setQuery("SELECT country_name FROM #__virtuemart_countries WHERE virtuemart_country_id IN($cid)");
										$resut = $db->loadObjectList();
										$multiselectvalue = '';
										if(!empty($resut)){
											foreach($resut as $p=>$cv){
												$multiselectvalue .= ($p <= 0) ? $cv->country_name : ', '.$cv->country_name;
											}
										}
									break;
									
									default : 										
										$multiselectvalue = '';										
									break;
								}
								echo $multiselectvalue;
							}else{
								echo $v;
							}
							
							 ?>
						</td>
					</tr>
				<?php } 
				}	
			?>	
			</table>
		</div>
		<div class="clearfix"></div>
		<?php } ?>
		<div class="clearfix"></div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_ASVM_ORDER_RESULT_NOT_FOUND'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="15%" >
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_FORM_ORDER_NUMBER', 'a.order_number', $listDirn, $listOrder); ?>
						</th>
						<th width="15%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_FORM_LBL_ORDER_NAME', 'vou.first_name', $listDirn, $listOrder); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_FORM_LBL_ORDER_EMAIL', 'vou.email', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_PAYMENT_METHOD', 'vpeg.payment_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_ORDER_DATE', 'a.created_on', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_LAST_MODIFIED', 'a.modified_on', $listDirn, $listOrder); ?>
						</th>
						<th width="12%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_ORDER_STATUS', 'a.order_status', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_ORDER_TOTAL', 'a.order_total', $listDirn, $listOrder); ?>
						</th>
						<th width="3%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_ASVM_ORDER_ID_LABLE', 'a.virtuemart_order_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="13">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) :	
								
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->order_number); ?>
							</td>
						
							<td class="small hidden-phone">
								<?php
								if($item->order_number){ ?>
									<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id='.$item->virtuemart_order_id); ?> " target="_blank">
									<?php echo $item->order_number; ?> 
									</a>			
								<?php }else{
									echo '-----';
								}	
								
								?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo $item->name; ?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo $item->email; ?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo $item->payment_method; ?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo date('l , d F Y h:i ',strtotime($item->created_on)); ?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo date('l , d F Y h:i ',strtotime($item->modified_on)); ?>
							</td>
							
							<td class="small hidden-phone">
								<?php echo JText::_(str_replace('COM_VIRTUEMART','COM_ASVM',$item->order_status)) ?>
							</td>
							
							<td class="small hidden-phone">
								<?php 
								
								/* if(!empty($item->order_currency)){
									$displayCurrency = $currency_model->getCurrency($item->order_currency);
									echo $displayCurrency->currency_symbol;
								}
								echo number_format($item->order_total,2); */
								//echo $item->order_total;
								echo $currency->priceDisplay($item->order_total);
								
								?>
							</td>							
							<td class="small hidden-phone">
								<?php echo $item->virtuemart_order_id; ?>
							</td>
							
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>			
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />		
		<?php 
		
		echo JHtml::_('form.token'); ?>
	</div>
<input type="hidden" name="order_selection" value="1" />
</form>
<script language="javascript">
baseurl = '<?php echo JURI::getInstance();?>';
jQuery("#adminForm").attr('action','<?php echo JURI::getInstance();?>');
jQuery(document).ready(function(){
	jQuery("button#toolbar-download").on('click',function(e){
		jQuery("#adminForm").submit();
		action = 'index.php?option=com_asvm&view=exports';
		jQuery("#adminForm").attr('action',action);
		 jQuery("#adminForm").submit();
	 });	 
	jQuery('#filter_order_id').keyup(function () { 
		this.value = this.value.replace(/[^0-9.]/g,'');
	});
});
</script>