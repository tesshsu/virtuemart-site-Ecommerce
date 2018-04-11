<?php
/*
* @license		GNU/GPL, see LICENSE.txt

*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

 
jimport('joomla.html.html');
jimport('joomla.form.formfield');


// Category element
class JFormFieldUserType extends JFormField {

	protected $type = 'usertype';
	
	protected function getInput(){
		$mitems = array();
		$mitems[0] = new stdClass();
		$mitems[0]->id = '';
		$mitems[0]->title = 'Do Not Filter By User Type';
		$mitems[1] = new stdClass();
		$mitems[1]->id = '1';
		$mitems[1]->title = 'Visitors Only';
		$mitems[2] = new stdClass();
		$mitems[2]->id = '2';
		$mitems[2]->title = 'Registered Users Only';
		
		

			
		return JHTML::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" multiple="multiple"', 'id', 'title', $this->value, $this->id );

		
	}

}