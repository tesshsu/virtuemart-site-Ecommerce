<?php
/**
* @version		v1.7
* @package	Joomla!®
* @copyright	Copyright © 2011 Natural Selection Web Design LLC.
* @support      http://nsel.co
* @license		GNU/GPL, see license.txt
*/

// no direct access
defined('_JEXEC') or die;

class JFormFieldSpacera extends JFormField
{
	public $type = 'Spacera';

	protected function getInput()
	{
		if ($this->value) {
			return JText::_($this->value);
		} else {
			return '<div style="width:350px;font-family:arial;font-size:13px;">Having Trouble? <a style="font-family:arial;color:#828627;" target="blank" href="https://nswd.co/help-desk">Support Here</a><br /><br /><a style="font-family:arial;color:#828627;" target="blank" href="https://nswd.co/extensions/paid/ns-fontawesome-pro">Get the Pro Version!</a></div>';
		}
	}
}
