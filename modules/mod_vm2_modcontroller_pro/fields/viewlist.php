<?php
/**
* @license		GNU/GPL, see LICENSE.txt
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a list of layouts for an extension
 */
 
 
jimport('joomla.html.html');
jimport('joomla.form.formfield');



class JFormFieldViewlist extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'viewlist';

	function getInput()
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		// path to extension directory
		$path		= JPATH_ROOT.'/components/'.$this->element['extension']. '/views/';
		$filter		= $this->element['filter'];
		$exclude	= $this->element['exclude'];
		$multiple   = $this->element['multiple']? true: false;
		

        $folders	= JFolder::folders($path, $filter);

		$options = array ();
		foreach ($folders as $folder)
		{
			if ($exclude)
			{
				if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $folder )) {
					continue;
				}
			}
			else
			{
				$options[] = JHTML::_('select.option', $folder, $folder);
						
		
			}
			
			//$options[] = JHTML::_('select.option', $folder, $folder);
		}
		
		if (!$this->element['hide_none']) {
			array_unshift($options, JHTML::_('select.option', '-1', '- '.JText::_('Do not use').' -'));
		}

		if (!$this->element['hide_default']) {
			array_unshift($options, JHTML::_('select.option', '', '- '.JText::_('Use default').' -'));
		}

		return JHTML::_('select.genericlist',  $options, $this->name.'[]', 'class="inputbox"'.$multiple?' multiple="multiple" size="10"':'', 'value', 'text', $this->value);
		
		
		
	}
	
	
	
	
}