<?php
/*------------------------------------------------------------------------
# Daycounts Version download custom param/field
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.cache.cache');
jimport('joomla.application.helper');
jimport('joomla.filesystem.file');
jimport('joomla.html.parameter.element');

class JFormFieldVersionDownload extends JFormField {

	public function getInput()	{

		$updateplugin = JPluginHelper::getPlugin('installer','daycounts');
		$msg = '';
	
		$msg .= '<input type="text" id="'.$this->id.'" style="width:250px;" name="'.$this->name.'" value="'.$this->value.'">';

		if (!JPluginHelper::isEnabled('installer','daycounts')) {
			$msg .= '<span color="red">Please install and publish the <a href="https://www.daycounts.com/component/digitolldownloads/download/request/PLG_UPDATE">daycounts updater plugin</a></span>';
		}
		return $msg;

	}	
	

}
