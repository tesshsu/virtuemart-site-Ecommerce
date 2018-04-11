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

/**
 * Asvm helper.
 */
class AsvmHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
			JText::_('COM_ASVM_TITLE_ORDERS'),
			'index.php?option=com_asvm&view=orders',
			$vName == 'orders'
		);
		JHtmlSidebar::addEntry(
			JText::_('Export'),
			'index.php?option=com_asvm&view=exports',
			$vName == 'exports'
		);		
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_asvm';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
	
	
	// get product sku
	public static function getProductSkuOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('product_sku As value, product_sku As text')
			->from('#__virtuemart_products AS a')
			->where('a.published = 1')
			->where("a.product_sku != ''")
			->order('a.virtuemart_product_id');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		// $options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option','0',JText::_('COM_ASVM_ORDER_SELECT_PRODUCT_SKU')));		
		return $options;	
	}
	
	
	// get product name 
	public static function getProductNameOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('virtuemart_product_id As value, product_name As text')
			->from('#__virtuemart_products_en_gb AS a')			
			->order('a.product_name');
		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		
		array_unshift($options, JHtml::_('select.option','0',JText::_('COM_ASVM_ORDER_SELECT_PRODUCT_NAME')));		
		return $options;
	}
	
	
	// get Order Status Options
	public static function getOrderStatusOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('order_status_code As value, order_status_name As text')
			->from('#__virtuemart_orderstates AS a')
			->where('a.published = 1')
			->order('a.order_status_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options  = array();
			$options1 = $db->loadObjectList();
			if(!empty($options1)){
				foreach($options1 as $k=>$v){
					$text 		  =  str_replace('COM_VIRTUEMART','COM_ASVM',$v->text);
					$v->text 	  = JText::_($text);	
					$options[$k]  = $v;
				}
			}
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		// $options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option','0',JText::_('COM_ASVM_ORDER_SELECT_ORDER_STATUS')));

		return $options;
	}
	
	// get Payment Method Options
	public static function getPaymentMethodOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.virtuemart_paymentmethod_id As value, b.payment_name As text')
			->from('#__virtuemart_paymentmethods AS a')
			->join('left', $db->quoteName('#__virtuemart_paymentmethods_en_gb', 'b') . ' ON (' . $db->quoteName('a.virtuemart_paymentmethod_id') . ' = ' . $db->quoteName('b.virtuemart_paymentmethod_id') . ')')
			->where('a.published = 1')
			->order('a.payment_element');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		// $options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option','0',JText::_('COM_ASVM_ORDER_SELECT_PAYMENT_METHOD')));

		return $options;
	}
	// get  Country Options
	public static function getVmCountryOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('virtuemart_country_id As value, country_name As text')
			->from('#__virtuemart_countries AS a')
			->where('a.published = 1')
			->order('a.country_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Merge any additional options in the XML definition.
		// $options = array_merge(parent::getOptions(), $options);

		array_unshift($options, JHtml::_('select.option','0',JText::_('COM_ASVM_ORDER_SELECT_COUNTRY')));

		return $options;
	}


}
