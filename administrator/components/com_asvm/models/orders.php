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
class AsvmModelOrders extends JModelList
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
		$query->from($db->quoteName('#__virtuemart_orders') . ' AS a');

		//Join over the orderstates
		$query->select('vos.order_status_name AS order_status')
		->join('LEFT', $db->quoteName('#__virtuemart_orderstates') . ' AS vos ON vos.order_status_code = a.order_status');

		// Join over the virtuemart paymentmethods .
		$query->select('vpeg.payment_name AS payment_method')
			->join('LEFT', '#__virtuemart_paymentmethods_fr_fr AS vpeg ON vpeg.virtuemart_paymentmethod_id=a.virtuemart_paymentmethod_id');
		
		// Join over the virtuemart  order  userinfos.
		$query->select("CONCAT(vou.first_name, ' ', vou.last_name) as name , vou.email")
		->join('LEFT', '#__virtuemart_order_userinfos AS vou ON vou.virtuemart_order_id = a.virtuemart_order_id');
		
		
		// Filter by order id 
		$orderId = $this->getState('filter.order_id');
		
		if (trim($orderId) != '')
		{
			$query->where("a.virtuemart_order_id = ".$db->quote($orderId));
		}	

		// Filter by order number
		$orderNumber = $this->getState('filter.order_number');

		if (!empty($orderNumber))
		{
			$query->where("a.order_number LIKE ".$db->quote("%".$orderNumber."%"));
		}			
		
		// Filter by from date
		$from = $this->getState('filter.from');

		if (!empty($from))
		{
			$query->where("a.created_on >= ".$db->quote($from));
		}	
		
		
		// Filter by to date
		$to = $this->getState('filter.to');

		if (!empty($to))
		{
			$str = date('Y-m-d',strtotime($to)+86400);
			$query->where("a.created_on <= ". $db->quote($str));
		}
				
		// Filter by  paymentmethod 
		$paymentMethod1 = $this->getState('filter.payment_method');
		$paymentMethod = array_filter((isset($paymentMethod1) && !empty($paymentMethod1)) ? $paymentMethod1 : array());		
		if (!empty($paymentMethod))
		{
			$pM = implode(',',$paymentMethod);
			
			$query->where("a.virtuemart_paymentmethod_id IN($pM) ");
		}
		
		
		// Filter by firstname
		$firstName = $this->getState('filter.first_name');

		if (!empty($firstName))
		{
			$query->where("vou.first_name LIKE ".$db->quote("%".$firstName."%"));
		}	
		
		// Filter by lastname
		$lastName = $this->getState('filter.last_name');

		if (!empty($lastName))
		{
			$query->where("vou.last_name LIKE ".$db->quote("%".$lastName."%"));
		}	
		
		// Filter by email
		$email = $this->getState('filter.email');

		if (!empty($email))
		{
			$query->where("vou.email LIKE ".$db->quote("%".$email."%"));
		}
		
		// Filter by city
		$city = $this->getState('filter.city');

		if (!empty($city))
		{
			$query->where("vou.city LIKE ".$db->quote("%".$city."%"));
		}
		
		// Filter by  state 
		$state = $this->getState('filter.state');		
		//$state = array_filter($state1);		
		if (!empty($state))
		{			
			$db->setQuery("SELECT virtuemart_state_id FROM #__virtuemart_states WHERE state_name LIKE ".$db->quote("%".$state."%"));
			$stateids = $db->loadObjectList();			
			$ids = array();
			if(!empty($stateids)){
				foreach($stateids as $k=>$v){
					$ids[] = $v->virtuemart_state_id; 
				}
				$pM = implode(',',$ids);
			}else{
				$pM = 0;
			}	
			$query->where("vou.virtuemart_state_id IN($pM) ");
		}
		
		// Filter by  country 
		$country1 = $this->getState('filter.country');
		$country = array_filter((isset($country1) && !empty($country1)) ? $country1 : array());		
		if (!empty($country))
		{
			$pM = implode(',',$country);
			$query->where("vou.virtuemart_country_id IN($pM) ");
		}
		
		
		// Filter by zipcode
		$zip = $this->getState('filter.zip');

		if (!empty($zip))
		{
			$query->where("vou.zip LIKE ".$db->quote("%".$zip."%"));
		}
		
		// Filter by address
		$address = $this->getState('filter.address');

		if (!empty($address))
		{
			$query->where("(vou.address_1 LIKE ".$db->quote("%".$address."%")." OR vou.address_2 LIKE ".$db->quote("%".$address."%").")");
		}
		
		// Filter by  order status 
		$orderStatus1 = $this->getState('filter.order_status');
		$orderStatus = array_filter((isset($orderStatus1) && !empty($orderStatus1)) ? $orderStatus1 : array());
		
		if (!empty($orderStatus))
		{
			$orstatus = '';
			foreach($orderStatus as $k=>$os){
				if($k == 0){
					$orstatus .= "a.order_status = ".$db->quote($os);
				}else{
					$orstatus .= "OR a.order_status = ".$db->quote($os);
				}
				
			}			
			$query->where($orstatus);
		}
		
		
		// Filter by  product sku 
		$productSku1 = $this->getState('filter.product_sku');		
		$productSku = array_filter((isset($productSku1) && !empty($productSku1)) ? $productSku1 : array());		
		if (!empty($productSku))
		{		
			$oderItemIds = array();
			foreach($productSku as $psk){
				$db->setQuery("SELECT DISTINCT virtuemart_order_id FROM #__virtuemart_order_items WHERE order_item_sku LIKE ".$db->quote("%".$psk."%"));
				$orderids = $db->loadObjectList();
				
				foreach($orderids as $k=>$v){
					$oderItemIds[] = $v->virtuemart_order_id; 
				}
			}
			
			if(!empty($oderItemIds)){				
				$pM = implode(',',$oderItemIds);
			}else{
				$pM = 0;
			}	
			$query->where("a.virtuemart_order_id IN($pM) "); 
		}
		
		
		// Filter by  product sku 
		$produtName1 = $this->getState('filter.produt_name');		
		$produtName = array_filter((isset($produtName1) && !empty($produtName1)) ? $produtName1 : array());	
	
		if (!empty($produtName))
		{		
			$productItemIds = array();
			$pId = implode(',',$produtName);
			$db->setQuery("SELECT DISTINCT virtuemart_order_id FROM #__virtuemart_order_items WHERE virtuemart_product_id IN($pId) ");			
			$orderids = $db->loadObjectList();			
			foreach($orderids as $k=>$v){
				$productItemIds[] = $v->virtuemart_order_id; 
			}			
			
			if(!empty($productItemIds)){				
				$pM = implode(',',$productItemIds);
			}else{
				$pM = 0;
			}	
			$query->where("a.virtuemart_order_id IN($pM) "); 
		}
		
		 
		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = explode(' ',$search);
			$firstName = (isset($search[0])) ? $search[0] : '';
			$lastName = (isset($search[1])) ? $search[1] : '';
			if(!empty($firstName) && empty($lastName)){
				$query->where("(vou.first_name LIKE ".$db->quote("%".$firstName."%")." OR vou.last_name LIKE ".$db->quote("%".$firstName."%")." )");
			}else if(!empty($firstName) && !empty($lastName)){
				$query->where("(vou.first_name LIKE ".$db->quote("%".$firstName."%")." AND vou.last_name LIKE ".$db->quote("%".$lastName."%")." )");
			}
			
			
		} 
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.virtuemart_order_id');
		
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));		
		$query->group('vou.virtuemart_order_id');		
		$session = JFactory::getSession();
		$session->set('orderquery',$query);
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

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$app = JFactory::getApplication('administrator');
		$search = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : '';
		$this->setState('filter.search', $search);

		$orderId = (isset($_REQUEST['filter']['order_id'])) ? $_REQUEST['filter']['order_id'] : '';
		$this->setState('filter.order_id', $orderId);

		$orderNumber = (isset($_REQUEST['filter']['order_number'])) ? $_REQUEST['filter']['order_number'] : '';
		$this->setState('filter.order_number', $orderNumber);

		$productSku = (isset($_REQUEST['filter']['product_sku'])) ? $_REQUEST['filter']['product_sku'] : '';
		$this->setState('filter.product_sku', $productSku);

		$produtName = (isset($_REQUEST['filter']['produt_name'])) ? $_REQUEST['filter']['produt_name'] : '';
		$this->setState('filter.produt_name', $produtName);
		
		
		$orderStatus = (isset($_REQUEST['filter']['order_status'])) ? $_REQUEST['filter']['order_status'] : '';
		$this->setState('filter.order_status','');

		$paymentMethod = (isset($_REQUEST['filter']['payment_method'])) ? $_REQUEST['filter']['payment_method'] : '';
		$this->setState('filter.payment_method', $paymentMethod);

		$email = (isset($_REQUEST['filter']['email'])) ? $_REQUEST['filter']['email'] : '';
		$this->setState('filter.email', $email);
		
		$firstName = (isset($_REQUEST['filter']['first_name'])) ? $_REQUEST['filter']['first_name'] : '';
		$this->setState('filter.first_name', $firstName);
		
		$lastName = (isset($_REQUEST['filter']['last_name'])) ? $_REQUEST['filter']['last_name'] : '';
		$this->setState('filter.last_name', $lastName);
		
		$state = (isset($_REQUEST['filter']['state'])) ? $_REQUEST['filter']['state'] : '';
		$this->setState('filter.state', $state);
		
		$city = (isset($_REQUEST['filter']['city'])) ? $_REQUEST['filter']['city'] : '';
		$this->setState('filter.city', $city);
		
		$address = (isset($_REQUEST['filter']['address'])) ? $_REQUEST['filter']['address'] : '';
		$this->setState('filter.address', $address);
		
		$country = (isset($_REQUEST['filter']['country'])) ? $_REQUEST['filter']['country'] : '';
		$this->setState('filter.country', $country);
		
		$zip = (isset($_REQUEST['zip'])) ? $_REQUEST['zipzip'] : '';
		$this->setState('filter.zip', $zip);		

		// Load the parameters.
		$params = JComponentHelper::getParams('com_asvm');
        $this->setState('params', $params);

		// List state information.
		parent::populateState('a.virtuemart_order_id', 'DESC');
	}
	
	public function getItems() {
        $items = parent::getItems();
        
        return $items;
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
		$sub = "concat((SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = order_currency),'  ',";
		$sql = "SELECT virtuemart_order_id as order_id,`order_number`,`created_on` as order_date,$sub `order_subtotal`) as order_subtotal,`coupon_code` as coupon,$sub `order_discount`) as discount, (SELECT `order_status_name` FROM `#__virtuemart_orderstates` WHERE `order_status_code` = order_status) as order_status,$sub `order_total`) as order_total,$sub order_billTaxAmount) as total_tax,(SELECT  `payment_element` FROM  `#__virtuemart_paymentmethods` = virtuemart_paymentmethod_id) as payment_method,(SELECT `shipment_element` FROM `#__virtuemart_shipmentmethods` WHERE `virtuemart_shipmentmethod_id` = virtuemart_shipmentmethod_id) as ship_method,order_pass as secret_key   FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = ".$db->quote($orderid);
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
}
