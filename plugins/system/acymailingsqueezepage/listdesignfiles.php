<?php
if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo 'This module can not work without the AcyMailing Component';
}

// List php files of the media/com_acymailing/plugins/squeezepage folder
// Only took files with no _ in their name (used for language and mobile versions)
if(!ACYMAILING_J16){
	class JElementListdesignfiles extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name) {
			jimport('joomla.filesystem.folder');
			$files = JFolder::files(rtrim(ACYMAILING_MEDIA,DS).DS.'plugins'.DS.'squeezepage', '.php');

			$dropdown = array();
			$dropdown[] = JHTML::_('select.option', '0', 'component.php file from your template');
			foreach($files as $oneFile){
				if(strpos($oneFile, '_') !== false) continue;
				$designFile = substr($oneFile,0,strlen($oneFile)-4);
				$dropdown[] = JHTML::_('select.option', $designFile, $designFile);
			}
			return JHTML::_('select.genericlist', $dropdown, $control_name.'['.$name.']' , 'size="1"', 'value', 'text', $value);
		}
	}
}else{
	class JFormFieldListdesignfiles extends JFormField
	{
		var $type = 'listdesignfiles';

		function getInput() {
			jimport('joomla.filesystem.folder');
			$files = JFolder::files(rtrim(ACYMAILING_MEDIA,DS).DS.'plugins'.DS.'squeezepage', '.php');

			$dropdown = array();
			$dropdown[] = JHTML::_('select.option', '0', 'component.php file from your template');
			foreach($files as $oneFile){
				if(strpos($oneFile, '_') !== false) continue;
				$designFile = substr($oneFile,0,strlen($oneFile)-4);
				$dropdown[] = JHTML::_('select.option', $designFile, $designFile);
			}
			return JHTML::_('select.genericlist', $dropdown, $this->name , 'size="1"', 'value', 'text', $this->value);
		}
	}
}
