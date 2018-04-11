<?php
/*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



jimport('joomla.html.html');
jimport('joomla.form.formfield');


// Category element
class JFormFieldManufacturers extends JFormField {

	protected $type = 'manufacturers';
	
	function getInput(){
		
	  if (!class_exists( 'VmConfig' )) {
		  require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
	  }
	  VmConfig::loadConfig(true,false);
	  if(method_exists('VmConfig','loadJLang'))
	  {
		  VmConfig::loadJLang('com_virtuemart', true);
	  }
	  else
	  {
		$lang = JFactory::getLanguage();		
		$lang->load('com_virtuemart');
	  }
		
		
		$mitems = array();
		$mitems[0] = new stdClass();		
		$mitems[0]->id = '-1';
		$mitems[0]->title = 'Do Not Filter By Manufacturers';
		$mitems[1] = new stdClass();		
		$mitems[1]->id = '0';
		$mitems[1]->title = 'All Manfacturers';
		
		
		$db = JFactory::getDBO();
		$query = "SELECT m.virtuemart_manufacturer_id AS id, l.mf_name AS title FROM #__virtuemart_manufacturers AS m ";
		$query .= " LEFT JOIN #__virtuemart_manufacturers_".$db->escape(VMLANG)." as l ON m.virtuemart_manufacturer_id=l.virtuemart_manufacturer_id ";
		$query .= " ORDER BY l.mf_name";
	
		
		$db->setQuery( $query );
		if(!$results = $db->loadObjectList())
		{
		  return '';	
			
		}

        if(count($results)>0)
		{
		   $manufacturers = array_merge($mitems, $results);
			
		   return JHTML::_('select.genericlist',  $manufacturers, $this->name.'[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $this->value );
		}
		else
		{
		  return '';	
			
		}

		
	}

}