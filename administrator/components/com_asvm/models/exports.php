<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
// No direct access

defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of order records.
 *
 * @since  1.6
 */
class AsvmModelExports extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
	
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'order_id','a.order_id','a.virtuemart_order_id',
				'order_number','a.order_number', 
				'product_sku',
				'produt_name',
				'order_status','a.order_status',
				'payment_method',
				'email','vou.email','vpeg.payment_name',
				'a.modified_on','a.created_on','a.order_total',
				'first_name','vou.first_name','last_name','address',
				'city','state','country','zip','from','to',
			);
		}
		
		parent::__construct($config);
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	public function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);		
		return $query;
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Order', $prefix = 'AsvmTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItems() {
        $items = parent::getItems();        
        return $items;
    }
	public function getData(){
		$selected = 3;
		$session = JFactory::getSession();
		$db = JFactory::getDbo();
		$cids = JRequest::getvar('cid');
		$order_selection = JRequest::getvar('order_selection');
		if($order_selection){
			$session->set('cids',$cids);
		}
		else{
			$cids = $session->get('cids');
		}
		if($cids){
			$selected = 1;
			$cid = implode("','",$cids);
			if($cid){
				$cid = "'".$cid."'";
			}
			$sql = "SELECT * FROM #__virtuemart_orders WHERE order_number IN (".$cid.")";
			$db->setQuery($sql);
			$items = $db->loadObjectList();
		}
		else{
			$query = $session->get('orderquery');
			$db->setQuery($query);
			$items = $db->loadObjectList();
			if(count($items)){
				$selected = 2;
			}
		}
		$countcids = count($cids);
		$sql = "SELECT count(*) FROM #__virtuemart_orders";
		$db->setQuery($sql);
		
		$countallitems = $db->loadResult();
		$query = $session->get('orderquery');
		$query->setLimit(-1);
		$db->setQuery($query);
		$tmpitems = $db->loadObjectList();
		$countitems = count($tmpitems);
		
		return array('items'=>$items,'selected'=>$selected,'countcids'=>$countcids,'countallitems'=>$countallitems,'countitems'=>$countitems);
		
	}
	public function billTO($orderid){
		$db = JFactory::getDbo();
		$sql = "SELECT `first_name` as bill_to_fname,`last_name` as bill_to_lname,`company` as bill_to_company,`address_1` as bill_to_address, `address_2` as bill_to_address2, `city` as bill_to_city,`virtuemart_state_id` as bill_to_states,(SELECT `state_name` FROM `#__virtuemart_states` WHERE `virtuemart_state_id` = bill_to_states) as bill_to_state ,`virtuemart_country_id` as bill_to_countrys, (SELECT `country_name` FROM #__virtuemart_countries WHERE `virtuemart_country_id` = bill_to_countrys) as bill_to_country,`zip` as bill_to_postal, `phone_1` as bill_to_phone,`email` as bill_to_email FROM `#__virtuemart_order_userinfos` WHERE address_type = 'BT' AND `virtuemart_order_id` = ".$db->quote($orderid);
		$db->setQuery($sql);
		$results = $db->loadAssoc();
		
		return $results;
		
	}
	public function shipTO($orderid){
		$db = JFactory::getDbo();
		$sql = "SELECT `first_name` as ship_to_fname,`last_name` as ship_to_lname,`company` as ship_to_company,`address_1` as ship_to_address, `address_2` as ship_to_address2, `city` as ship_to_city, `virtuemart_state_id` as ship_to_states,(SELECT `state_name` FROM `#__virtuemart_states` WHERE `virtuemart_state_id` = ship_to_states) as ship_to_state, `virtuemart_country_id` as ship_to_countrys, (SELECT `country_name` FROM #__virtuemart_countries WHERE `virtuemart_country_id` = ship_to_countrys) as ship_to_country, `zip` as ship_to_postal, `phone_1` as ship_to_phone,`email` as ship_to_email FROM `#__virtuemart_order_userinfos` WHERE address_type = 'ST' AND `virtuemart_order_id` = ".$db->quote($orderid);
		$db->setQuery($sql);
		$results = $db->loadAssoc();
		
		if($results)
			return $results;
		else{
			$sql = "SELECT `first_name` as ship_to_fname,`last_name` as ship_to_lname,`company` as ship_to_company,`address_1` as ship_to_address, `address_2` as ship_to_address2, `city` as ship_to_city, `virtuemart_state_id` as ship_to_states,(SELECT `state_name` FROM `#__virtuemart_states` WHERE `virtuemart_state_id` = ship_to_states) as ship_to_state, `virtuemart_country_id` as ship_to_countrys, (SELECT `country_name` FROM #__virtuemart_countries WHERE `virtuemart_country_id` = ship_to_countrys) as ship_to_country, `zip` as ship_to_postal, `phone_1` as ship_to_phone,`email` as ship_to_email FROM `#__virtuemart_order_userinfos` WHERE address_type = 'BT' AND `virtuemart_order_id` = ".$db->quote($orderid);
			$db->setQuery($sql);
			$results = $db->loadAssoc();
			return $results;
		}
	}
	
	public function getOrder($orderid){
		$db = JFactory::getDbo();
		$sub = "concat((SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = order_currency),' ',";
		$sql = "SELECT virtuemart_order_id as order_id,`order_number`,`created_on` as order_date,order_subtotal as order_subtotal,`coupon_code` as coupon,order_discount as discount, (SELECT `order_status_name` FROM `#__virtuemart_orderstates` WHERE `order_status_code` = order_status) as order_status,order_total,order_billTaxAmount as total_tax,(SELECT  `payment_element` FROM  `#__virtuemart_paymentmethods` = virtuemart_paymentmethod_id) as payment_method,(SELECT `shipment_element` FROM `#__virtuemart_shipmentmethods` WHERE `virtuemart_shipmentmethod_id` = virtuemart_shipmentmethod_id) as ship_method,order_pass as secret_key,(SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = order_currency) as currency_code_3,(SELECT `currency_symbol` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = order_currency) as currency_symbol   FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = ".$db->quote($orderid);
		$db->setQuery($sql);
		$results = $db->loadAssoc();		
		return $results;
	}
	
	public function getProduct($orderid){
		$db = JFactory::getDbo();
		
		$sql = "SELECT oritem.order_item_name as product_name,oritem.order_item_sku as sku,oritem.product_final_price as product_price,oritem.product_quantity as qty,  
		( SELECT `product_in_stock` FROM `#__virtuemart_products` WHERE `virtuemart_product_id` = oritem.virtuemart_product_id ) as product_in_stock
		FROM #__virtuemart_order_items as oritem WHERE oritem.virtuemart_order_id = ".$db->quote($orderid);
		
		$db->setQuery($sql);
		$results = $db->loadAssocList();
		return $results;
	}
	public function getDefaultExportkey(){
		$exportkey = array();
		$exportkey['order_id']='Order Id';	 
		$exportkey['order_number']='Order No';
		$exportkey['product_name']='Product Name';
		$exportkey['sku']='Product SKU';
		$exportkey['order_date']='Order Date';
		$exportkey['product_price']='Product Price';
		$exportkey['qty']='Qty';
		$exportkey['product_in_stock']='Product status';
		$exportkey['secret_key']='Secret key';
		$exportkey['coupon']='Order Coupon';
		$exportkey['discount']='Coupon Discount';
		$exportkey['order_status']='Order Status';
		$exportkey['order_subtotal'] = 'Order Subtotal';
		$exportkey['total_tax']='Total Tax';
		$exportkey['payment_method']='Payment method';
		$exportkey['comment']='Comment';
		$exportkey['bill_to_fname'] =  'Bill First Name';	
		$exportkey['bill_to_lname'] =  'Bill Last Name';	
		$exportkey['bill_to_company'] =  'Bill Company';	
		$exportkey['bill_to_address'] =  'Bill Address';	
		$exportkey['bill_to_address2'] =  'Bill Second Address';	
		$exportkey['bill_to_city'] =  'Bill City';	
		$exportkey['bill_to_state'] =  'Bill State';	
		$exportkey['bill_to_country'] =  'Bill Country';	
		$exportkey['bill_to_postal'] =  'Bill Postal code';	
		$exportkey['bill_to_phone'] =  'Bill Phone';	
		$exportkey['bill_to_email'] =  'Bill Email';
		$exportkey['ship_to_fname'] =  'Ship First Name';	
		$exportkey['ship_to_lname'] =  'Ship Last Name';	
		$exportkey['ship_to_company'] =  'Ship Company';	
		$exportkey['ship_to_address'] =  'Ship Address';	
		$exportkey['ship_to_address2'] =  'Ship Second Address';	
		$exportkey['ship_to_city'] =  'Ship City';	
		$exportkey['ship_to_state'] =  'Ship State';	
		$exportkey['ship_to_country'] =  'Ship Country';	
		$exportkey['ship_to_postal'] =  'Ship Postal code';	
		$exportkey['ship_to_phone'] =  'Ship Phone';
		return $exportkey;
	}
}
