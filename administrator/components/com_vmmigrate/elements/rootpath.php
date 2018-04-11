<?php
/*------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldRootpath extends JFormField {

	public function getInput()	{

		$new_path = JPATH_ROOT;
		
		$toremove = '';
		if ($test = stristr($new_path,'public_html')) {
			$toremove = str_ireplace('public_html','',$test);
		} else if ($test = stristr($new_path,'httpdocs')) {
			$toremove = str_ireplace('httpdocs','',$test);
		} else if ($test = stristr($new_path,'www')) {
			$toremove = str_ireplace('www','',$test);
		} else if ($test = stristr($new_path,'vhosts')) {
			$toremove = str_ireplace('vhosts','',$test);
		} else if ($test = stristr($new_path,'sites')) {
			$toremove = str_ireplace('sites','',$test);
		}

		$old_path = '';
		if ($toremove) {
			$old_path = str_replace($toremove,'',$new_path);
		}
		
		$html = '<div style="clear:none;float:left;margin:3px 0 0 2px;" class="path">';
		$html .= JText::sprintf('This Joomla instance runs in the path <u>%s</u>',$new_path);
		if ($old_path) {
			$html .= JText::sprintf('<br/>If the new website is installed in a subfolder of the old website, the correct path would then be <u>%s</u>',$old_path);
			$html .= '&nbsp;<button class="btn btn-success btn-small" data-value="'.$old_path.'" id="usepath">Use this</button>';
		}
		$html .= '</div>';
		return $html;
	}	
	

}