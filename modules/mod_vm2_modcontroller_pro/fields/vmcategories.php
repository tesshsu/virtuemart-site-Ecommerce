<?php
/**
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


jimport('joomla.html.html');
jimport('joomla.form.formfield');


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

if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . '/helpers/shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . '/tables/categories.php');

require_once (dirname(__FILE__).'/../helpers/fields.php');



if(! defined('VMLANG') || VMLANG == ''){ define('VMLANG','en_gb'); }



class JFormFieldVMCategories extends JFormField
{

	protected $type = 'vmcategories';

	protected function getInput(){		

	   // $lang = JFactory::getLanguage();
	   // $lang->load('com_virtuemart',JPATH_ADMINISTRATOR);
        $categorylist = modVMModControllerProFieldsHelper::categoryListTree();
		
		$mitems = array();
		$mitems[0] = new stdClass();
		$mitems[0]->value = 0;
		$mitems[0]->text = '--default--';


		foreach ( $categorylist as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->value, $item->text );
		}
		

		$output = '';
		if( ! $mitems || (count($mitems) <= 1))
		{
			$output = '';
		}
		else
		{
		  $output= JHTML::_('select.genericlist',  $mitems, $this->name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $this->value );
		}
		return $output;
		
		
	}
}
