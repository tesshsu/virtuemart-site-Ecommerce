<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
// No direct access.
defined('_JEXEC') or die;
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
jimport('joomla.application.component.controlleradmin');
/**
 * Orders list controller class.
 */
class AsvmControllerOrders extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'order', $prefix = 'AsvmModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function export(){
		
		
		$currencyarray = array('order_total','total_tax','order_subtotal','discount','product_price');
		$model = $this->getModel('exports');		
		$apps = JFactory::getApplication();	
		$db = JFactory::getDbo();
		$session = JFactory::getSession();
		$jinput = $apps->input;
		$exportkeys = JRequest::getvar('export','array','array');
		$date_format = JRequest::getvar('date_format');
		$dataarray = array();
		$filter = JRequest::getvar('filter',array(),'ARRAY');		
		$export_choose = JRequest::getvar('export_choose');	
		$prev = JRequest::getvar('prev');	
		
		$delimiter = ($filter['delimiter'])?($filter['delimiter']):',';
		$enclosure = ($filter['enclosure'])?($filter['enclosure']):'"';
		$include_columnheader = $filter['include_columnheader'];
		$cids = $session->get('cids');
		
		if($export_choose == 'selected'){
			$cid = implode("','",$cids);
			if($cid){
				$cid = "'".$cid."'";				
			$sql = "SELECT * FROM #__virtuemart_orders WHERE order_number IN (".$cid.")";
			$db->setQuery($sql);
			$items = $db->loadObjectList();
			}
		}
		else if($export_choose == 'search'){
			$query = $session->get('orderquery');
			$query->setLimit(-1);
			$db->setQuery($query);
			$items = $db->loadObjectList();
		}	
		else{
			$sql = "SELECT * FROM #__virtuemart_orders";
			$db->setQuery($sql);
			$items = $db->loadObjectList();
		}
		$filename = 'order_'.date('m-d-Y').'_export.csv';
		$outputpath = JPATH_ROOT.'/media/com_asvm/files/';
		if(!JFolder::exists($outputpath)){
			JFolder::create($outputpath);
		}
		$file = fopen($outputpath.$filename,"w");
		$header_cols = array();
			
		foreach($exportkeys as $ekey){
			if($ekey=='ship_to'){
				$exportkey['ship_to_fname'] =  'Shipping First Name';	
				$exportkey['ship_to_lname'] =  'Shipping Last Name';	
				$exportkey['ship_to_company'] =  'Shipping Company';	
				$exportkey['ship_to_address'] =  'Shipping Address';	
				$exportkey['ship_to_address2'] =  'Shipping Second Address';	
				$exportkey['ship_to_city'] =  'Shipping City';	
				$exportkey['ship_to_state'] =  'Shipping State';	
				$exportkey['ship_to_country'] =  'Shipping Country';	
				$exportkey['ship_to_postal'] =  'Shipping Postal code';	
				$exportkey['ship_to_phone'] =  'Shipping Phone';	
			}
			else if($ekey=='bill_to'){
				$exportkey['bill_to_fname'] =  'Billing First Name';	
				$exportkey['bill_to_lname'] =  'Billing Last Name';	
				$exportkey['bill_to_company'] =  'Billing Company';	
				$exportkey['bill_to_address'] =  'Billing Address';	
				$exportkey['bill_to_address2'] =  'Billing Second Address';	
				$exportkey['bill_to_city'] =  'Billing City';	
				$exportkey['bill_to_state'] =  'Billing State';	
				$exportkey['bill_to_country'] =  'Billing Country';	
				$exportkey['bill_to_postal'] =  'Billing Postal code';	
				$exportkey['bill_to_phone'] =  'Billing Phone';	
				$exportkey['bill_to_email'] =  'Billing Email';
			}
			else{
				$ekey1 = ucwords(str_replace("_"," ",$ekey));
				$exportkey[$ekey] = $ekey1;
			}
		}
		
		if($include_columnheader){	
			$t1 = '';
			foreach($exportkey as $t){
				$tempt = '';
				if($t){
					$tempt = $enclosure.$t.$enclosure;
				}	
				if($delimiter){
					$tempt = $tempt.$delimiter;
				}
				$t1 .= $tempt;
			}
			$t1 = rtrim($t1,",");
			$dataarray[] = $t1;
			fwrite($file,$t1."\n");		 
		}		
		$count = 0;
		
		DEFINE('DS', DIRECTORY_SEPARATOR );
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$config = VmConfig::loadConfig();
		if (!class_exists('CurrencyDisplay'))
					require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
		
		$currency = CurrencyDisplay::getInstance();
		$currencycode = $currency->getCurrencyForDisplay();
		if($currencycode){
			$sql = "SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = ".$db->quote($currencycode);
			$db->setQuery($sql);
			$currencySymbol = $currency->getSymbol();
			$currencySymbol3 = $db->loadResult();
		}
		
		foreach($items as $tmp){		 
			$tmparray = array();$billTO=array();$shipTO=array();
			unset($datas);
			$billTO = $model->billTO($tmp->virtuemart_order_id); 
			$shipTO = $model->shipTO($tmp->virtuemart_order_id); 		 
			$order = $model->getOrder($tmp->virtuemart_order_id); 
			$products = $model->getProduct($tmp->virtuemart_order_id); 
			foreach($billTO as $key=>$value){
			 $datas[$key] = $value;
			}
			foreach($shipTO as $key=>$value){
			 $datas[$key] = $value;
			}
			foreach($order as $key=>$value){
			 $datas[$key] = $value;
			}
			foreach($products as $k => $pro){
				$tmparray = array();	
				$writefile = "";
				$datas['product_name']  = $pro['product_name'];
				$datas['sku']  = $pro['sku'];
				$datas['product_price']  = $pro['product_price'];
				$datas['qty']  = $pro['qty'];
				$datas['product_in_stock']  = $pro['product_in_stock'];
				foreach($exportkey as $ekey => $evalue){				 
					if (array_key_exists($ekey,$datas)){
						if($ekey == 'order_date'){
							$temp1 = date($date_format,strtotime($datas[$ekey]));
						}
						else if(in_array($ekey,$currencyarray)){
							$temp1 = $currency->priceDisplay($datas[$ekey],$datas['order_currency']);
							
							$temp1 = str_replace(trim($currencySymbol),"",$temp1);							
							
						}
						else{
							$temp1 = html_entity_decode(JText::_($datas[$ekey]));
						}						
					}else{
						$temp1 = '';
					}
					
					if($enclosure){
						$temp1 = $enclosure.$temp1.$enclosure;
					}	
					if($delimiter){
						$temp1 = $temp1.$delimiter;
					}
					$writefile .= $temp1;
					$tmparray[] = $temp1;
				}	
				if($k == 0){
					unset($datas['order_total']);
				}
				if($tmparray){
					$count++;	
					$writefile = rtrim($writefile,",");
					$dataarray[] = $writefile;
					fwrite($file,$writefile."\n");					
					//fputcsv($file,$tmparray);
					
					
					if(($prev)&&($count>=10)){						
						break 2;
					}
				}
				
			}
		}
		if($prev && $dataarray){			
		?>
			<div class="table-responsive" style="overflow: auto;max-height:436px;">
			<table class="table table-bordered" width="4400px">
				<?php
					/* if(count($dataarray)>=1){ */
					foreach($dataarray as $i=>$items){
						if($items){
						$item = explode($enclosure.',',$items);							
						echo '<tr>';
							foreach($item as $value){
								$value = $value.$enclosure.$delimiter;
								echo '<td>'.trim(trim($value,'"'),'",').'</td>';
							}
						echo '</tr>';
						}
					}
					/* }
					else{
						echo '<tr><td colspan="10">'.JText::_('COM_ASVM_NO_COLUMN_SELECTES').'</td></tr>';
					}  */
				?>
			</table>
			</div>
		<?php }
		else{
			$downfilename = JPATH_ROOT.'/media/com_asvm/files/'.$filename;
			header('Content-Disposition: attachment; filename="'.basename($downfilename).'"');
			header('Content-Length: ' . filesize($downfilename));
			readfile($downfilename);
		}
		die();
	}
       
}