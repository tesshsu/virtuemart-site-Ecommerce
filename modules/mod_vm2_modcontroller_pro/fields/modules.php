<?php
/*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.html.html');
jimport('joomla.form.formfield');


// Category element
class JFormFieldModules extends JFormField {

	protected  $type = 'modules';
	
	protected function getInput(){
		$db = JFactory::getDBO();
		$query = "SELECT id, CONCAT(title,' (',id,')') AS title FROM #__modules AS m WHERE m.client_id = 0 AND module NOT LIKE 'mod_vm2_modcontroller_pro' ORDER BY title";
		
		
		$db->setQuery( $query );
		$modules = $db->loadObjectList();

        if(count($modules>0))
		{
		   return JHTML::_('select.genericlist',  $modules, $this->name, 'class="inputbox" ', 'id', 'title', $this->value, $this->id );
		}
		else
		{
		  return '';	
			
		}

		
	}

}