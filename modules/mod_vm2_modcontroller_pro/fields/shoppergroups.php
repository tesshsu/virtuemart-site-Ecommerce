<?php
/*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');



// Category element
class JFormFieldShopperGroups extends JFormField {

	protected $type = 'shoppergroups';
	
	protected function getInput(){
		
	  if (!class_exists( 'VmConfig' )) {
		  require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
	  }
	  VmConfig::loadConfig(true,false);
	  $lang = JFactory::getLanguage();		
       //$lang->load('mod_vm2_modcontroller_pro',JPATH_SITE);		
	  
	  
	  if(method_exists('VmConfig','loadJLang'))
	  {
		  VmConfig::loadJLang('com_virtuemart', true);	
		  VmConfig::loadJLang('com_virtuemart_shoppers', true);		  
		  
	  }
	  else
	  {
		$lang->load('com_virtuemart');
		$lang->load('com_virtuemart_shoppers');
		
	  }
		
		$mitems = array();
		$mitems[0] = new stdClass();
		$mitems[0]->id = '-1';
		$mitems[0]->title = 'Do Not Filter By Groups';
		$mitems[1] = new stdClass();
		$mitems[1]->id = '0';
		$mitems[1]->title = 'All Shopper Groups';
		
		
		$db = JFactory::getDBO();
		$query = "SELECT virtuemart_shoppergroup_id AS id,shopper_group_name FROM #__virtuemart_shoppergroups ORDER BY shopper_group_name";
	
		
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		
		foreach($results as &$result)
		{
			$result->title = JText::_($result->shopper_group_name);
		}

        if(count($results>0))
		{
		   $shoppergroups = array_merge($mitems, $results);
			
		   return JHTML::_('select.genericlist',  $shoppergroups, $this->name, 'class="inputbox" multiple="multiple"', 'id', 'title', $this->value, $this->id );
		}
		else
		{
		  return '';	
			
		}

		
	}

}