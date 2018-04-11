<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

class FstModelTest extends JModelLegacy
{
	function &getProduct()
	{
		$db = JFactory::getDBO();
		$prodid = JRequest::getVar('prodid', 0, '', 'int');
		$query = "SELECT * FROM #__fst_prod WHERE id = '".FSTJ3Helper::getEscaped($db, $prodid)."'";

		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows;        
	} 
	
	function &getProducts()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__fst_prod";
		
		$where = array();
		$where[] = "published = 1";
		$where[] = "intest = 1";
		
		if (FST_Helper::Is16())
		{
			$user = JFactory::getUser();
			$where[] = 'access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';				
		}	
		
		if (count($where) > 0)
			$query .= " WHERE " . implode(" AND ",$where);

		$db->setQuery($query);
		$rows = $db->loadAssocList('id');
		if (!is_array($rows))
			return array();
		return $rows;        
	} 
}

