<?php
// no direct access
defined('_JEXEC') or die;
/**
 * @package	Joomla.Site
 * @subpackage	mod_virtuemart_reorder
 * @copyright	Copyright (C) EasyJoomla.org. All rights reserved.
 * @author      Jan Linhart
 * @license	GNU General Public License version 2 or later
 */

// Load the language file of com_virtuemart.
JFactory::getLanguage()->load('com_virtuemart');

if (!class_exists( 'VirtueMartModelOrders' )) 
  require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'orders.php');

class mod_virtuemart_reorder
{
    private $orderNumber;

    function mod_virtuemart_reorder($orderNumber = 0)
    {
        $this->orderNumber = $orderNumber;
    }
    
    public function getOrderProducts($orderNumber = 0)
    {
        if (!$orderNumber)
        {
            $orderNumber = $this->orderNumber;
        }
        
        $orderModel = new VirtueMartModelOrders();
        $orderid = $orderModel->getOrderIdByOrderNumber($orderNumber);
        $order = $orderModel->getOrder($orderid);
        
        return $order;
    }

    public function getCustomIdByTitle($title)
    {
        if(!$title)
        {
            return null;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('virtuemart_custom_id')
            ->from('#__virtuemart_customs')
            ->where('custom_title = '.$db->quote($title));
        $db->setQuery($query);
        $result = $db->loadResult();

        if($errorMsg = $db->getErrorMsg())
        {
            JFactory::getApplication()->enqueueMessage($errorMsg, 'error');
        }

        return $result;
    }
}
