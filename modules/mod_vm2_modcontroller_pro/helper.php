<?php
/*
copyright 2009 Fiona Coulter http://www.spiralscripts.co.uk

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.module.helper');
jimport('joomla.utilities.arrayhelper');
jimport('joomla.utilities.date');


class modVMModControllerProHelper
{
	public static function loadModuleById($id)
	{
		$version = new JVersion();
		$result = null;
		$modules =& self::_load();
		$total = count($modules);

		for ($i = 0; $i < $total; $i++)
		{
			// Match the id of the module
			if ((int)$modules[$i]->id == (int)$id)
			{
					// Found it
					$result = $modules[$i];
					break; // Found it
			}
		}
		if (is_null($result))
		{
			$result            = new stdClass;
			$result->id        = 0;
			$result->title     = '';
			$result->module    = 'mod_dummy';
			$result->position  = '';
			$result->content   = '';
			$result->showtitle = 0;
			$result->control   = '';
			$result->params    = '';
			$result->user      = 0;
		}
	
		return $result;			
		
		
		
		
	}
	
	
	public static function renderModule($module, $attribs = array())
	{
	    $output = JModuleHelper::renderModule($module, $attribs);	
		echo $output;
		
	}
	
	public static function checkRender(&$params)
	{
		$behaviour = $params->get('defaultBehaviour','show');
		$renderModule = $behaviour == 'show'? true: false;
		
		
		$user		= JFactory::getUser();
		$userId		= (int) $user->get('id');	
		$category = self::getCategory(); //category is integers
		$view = self::getView();//layout
		$manufacturer = (int)JRequest::getVar('virtuemart_manufacturer_id',0);
		$option = JRequest::getVar('option','');
		
		//check categories
        $catParam = (array)$params->get('vmcat','0');
		if(count($catParam) > 0 && !in_array('0',$catParam))
		{
		   JArrayHelper::toInteger($catParam);
		   if($category>0)
		   {
			   $renderModule = false;
			   if(in_array($category, $catParam))
			   {
				  $renderModule = true;   
				  
			   }
		
		   }
			
		}
		
		//check categories to exclude
        $catExclude = (array)$params->get('vmcat_exclude','0');
		if(count($catExclude) > 0 && !in_array('0',$catExclude))
		{
		   JArrayHelper::toInteger($catExclude);
		   if($category>0)
		   {
			 $renderModule = true;   
			 if(in_array($category, $catExclude))
			 {
				$renderModule = false;   
				
			 }
			 
		   }
			
		}
		
				//check manufacturers
        $manParam = (array)$params->get('vmmanufacturer','-1');
		if(count($manParam) > 0 && !in_array('-1',$manParam))
		{
		   JArrayHelper::toInteger($manParam);
		   if($manufacturer>0)
		   {
		       $renderModule = false;   			   
			   if(in_array($manufacturer, $manParam) || in_array(0,$manParam))
			   {
				  $renderModule = true;   
			   }			 
		   }
		}
		
		
		
				//check manufacturers to exclude
        $manExclude = (array)$params->get('vmmanufacturer_exclude','-1');
		if(count($manExclude) > 0 &&  !in_array('-1',$manExclude))
		{
		   JArrayHelper::toInteger($manExclude);
		   if($manufacturer>0)
		   {
		       $renderModule = true;   			   
			   if(in_array($manufacturer, $manExclude)||in_array(0,$manExclude))
			   {
				  $renderModule = false;   
			   }			 
		   }
		}
				
		
		
		
		
		if($option == 'com_virtuemart')
		{
			$vmpage = (array)$params->get('vmpage', array());
			if(count($vmpage) > 0){
			  if(!in_array('',$vmpage) && !in_array($view,$vmpage))
			  {
				$renderModule = false; 				
			  }
			  
			}
			
			//exclude pages
			$vmpage_exclude = (array)$params->get('vmpage_exclude', array());
			if(count($vmpage_exclude) > 0){			  
			  if(!in_array('',$vmpage_exclude) && in_array($view, $vmpage_exclude))
			  {
				$renderModule = false; 				
			  }			  
			}

		}
		
		
			
		

		
		

	
		
		$groups = $params->get('vmshoppergroup',-1);
		
		//shopper groups
		if(is_array($groups))
		{			
			
		  JArrayHelper::toInteger($groups);
		  if(!in_array(-1, $groups))
		  {
		     if(!in_array(0, $groups))
		     {
		       $shopper_ids = self::get_shopper_id();				 
			   $renderModule = false;
			   foreach($shopper_ids as $shopper_id)
			   {
				 if(in_array($shopper_id, $groups))
				 {
					$renderModule = true;   
				 }
			   }
		     }
			 
		  }
			
		}
		else if ((int)$groups != -1)
		{
			
			if((int) $groups != 0)
			{
				
			  $shopper_ids = self::get_shopper_id();
			  $renderModule = false;	

			   foreach($shopper_ids as $shopper_id)
			   {
				
				  if((int) $groups == $shopper_id)
				  {
					$renderModule = true;	
				  }
			   }
			}
		}
		
		
		$usertype = $params->get('vmusertype');
		if($usertype != '')
		{
			if(($usertype == '1') && ($userId >0))
			{
				$renderModule = false;					
			}
			else if(($usertype == '2') && ($userId == 0))
			{
				$renderModule = false;					
			}
		}



        $isSSL = $params->get('sslpages','');
		if($isSSL == 'yes')
		{
			$uri =& JURI::getInstance();
			$scheme = $uri->getScheme();
			$scheme = JString::strtolower($scheme);
			if($scheme == 'http'){ $renderModule = false; }
			
		}
		else if($isSSL == 'no')
		{
			$uri =& JURI::getInstance();
			$scheme = $uri->getScheme();
			$scheme = JString::strtolower($scheme);
			if($scheme == 'https'){ $renderModule = false; }
			
		}
		
		

        $nonVM = $params->get('nonvmpages','');
		if($nonVM == 'yes')
		{
			if($option != 'com_virtuemart'){ $renderModule = false; }
			
		}
		else if($nonVM == 'no')
		{
			if($option == 'com_virtuemart'){ $renderModule = false; }
			
		}

				
		//echo 'render module=';
		//print_r($renderModule);
		
				
		return $renderModule;

		
	}
	
	public static function getView()
	{
	
      $option = JRequest::getVar('option','');	  		
	  if($option != 'com_virtuemart'){ return ''; }
		
	  $viewName = JRequest::getVar('view','virtuemart');
	  
	  return $viewName;
		
	}
	
	public static function get_shopper_id()
	{
		$user		= JFactory::getUser();
		$userId		= (int) $user->get('id');
		
		//if (!class_exists('VirtueMartModelShopperGroup'))
			//require(VMPATH_ADMIN . DS . 'models' . DS . 'shoppergroup.php');

		$shoppergroups = self::getShoppergroupById($userId);
		return $shoppergroups;
	}
	
  	public static function getShoppergroupById($id) {
    	$virtuemart_vendor_id = 1;
    	$db = JFactory::getDBO();

    	$q =  'SELECT `#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`, `#__virtuemart_shoppergroups`.`shopper_group_name`, `default` AS default_shopper_group FROM `#__virtuemart_shoppergroups`';

    	if (!empty($id)) {
      		$q .= ', `#__virtuemart_vmuser_shoppergroups`';
      		$q .= ' WHERE `#__virtuemart_vmuser_shoppergroups`.`virtuemart_user_id`="'.(int)$id.'" AND ';
      		$q .= '`#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`=`#__virtuemart_vmuser_shoppergroups`.`virtuemart_shoppergroup_id`';
    	}
    	else {
    		$q .= ' WHERE `#__virtuemart_shoppergroups`.`virtuemart_vendor_id`="'.(int)$virtuemart_vendor_id.'" AND `default`="2"';
    	}

    	$db->setQuery($q);
    	if($results = $db->loadColumn())
		{
			return $results;
			
		}
		else
		{
    	     $q =  'SELECT `#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`, `#__virtuemart_shoppergroups`.`shopper_group_name`, `default` AS default_shopper_group FROM `#__virtuemart_shoppergroups`';
    		$q .= ' WHERE `#__virtuemart_shoppergroups`.`virtuemart_vendor_id`="'.(int)$virtuemart_vendor_id.'" AND `default`="1"';
    	    $db->setQuery($q);
			if($results = $db->loadColumn())
			{
				return $results;
				
			}
			else
			{
				return array(0);
				
			}
			
			 
		}
  	}
	
	
	
	public static function old_get_shopper_id() {
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');
		
	    if($userId == 0)
	    {
		  return 0;	
	    }
		
		

		
		$q = "SELECT virtuemart_shoppergroup_id FROM #__virtuemart_vmuser_shoppergroups WHERE virtuemart_user_id='".$userId.'\'';
		
		

	    $db->setQuery($q);
		$rows = $db->loadObjectList();
		
		//echo mysql_error();
		

        if(count($rows) > 0)
		{
		   return $rows[0]->virtuemart_shoppergroup_id;
		}
		else
		{
		   return 0;	
		}


	}
	
	
	public static function getCategory()
	{
		$cat = (int)JRequest::getVar('virtuemart_category_id',0);
		return $cat;
	}
	
	protected static function &_load()
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$Itemid = JRequest::getInt('Itemid');
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$cache = JFactory::getCache('com_modules', '');
		$cacheid = md5(serialize(array($Itemid, $groups, $clientId, $lang)));

		if (!($clean = $cache->get($cacheid)))
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid');
			$query->from('#__modules AS m');
			$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
			//$query->where('m.published = 1');

			$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
			$query->where('e.enabled = 1');

			$date = JFactory::getDate();
			$dateFormat = $db->getDateFormat();
			$now = $date->format($date);
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = ' . $db->Quote($nullDate) . ' OR m.publish_up <= ' . $db->Quote($now) . ')');
			$query->where('(m.publish_down = ' . $db->Quote($nullDate) . ' OR m.publish_down >= ' . $db->Quote($now) . ')');

			$query->where('m.access IN (' . $groups . ')');
			$query->where('m.client_id = ' . $clientId);
			$query->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter())
			{
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('m.position, m.ordering');

			// Set the query
			$db->setQuery($query);
			$modules = $db->loadObjectList();
			$clean = array();

			if ($db->getErrorNum())
			{
				JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
				return $clean;
			}

			// Apply negative selections and eliminate duplicates
			$negId = $Itemid ? -(int) $Itemid : false;
			$dupes = array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition or if
				// the Itemid is missing or zero and the module is in exclude mode.
				$negHit = ($negId === (int) $module->menuid) || (!$negId && (int) $module->menuid < 0);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit)
					{
						unset($clean[$module->id]);
					}
					continue;
				}

				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					// Determine if this is a 1.0 style custom module (no mod_ prefix)
					// This should be eliminated when the class is refactored.
					// $module->user is deprecated.
					$file = $module->module;
					$custom = substr($file, 0, 4) == 'mod_' ?  0 : 1;
					$module->user = $custom;
					// 1.0 style custom module name is given by the title field, otherwise strip off "mod_"
					$module->name = $custom ? $module->module : substr($file, 4);
					$module->style = null;
					$module->position = strtolower($module->position);
					$clean[$module->id] = $module;
				}
			}

			unset($dupes);

			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->store($clean, $cacheid);
		}

		return $clean;
	}
	
	
	
	
}
