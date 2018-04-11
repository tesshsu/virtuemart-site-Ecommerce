<?php
/*------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('JPATH_BASE') or die();

class JFormFieldTestdatabase extends JFormField {

	public function getInput()	{
		
		JHTML::_('behavior.modal');
		
		$versioncat  = $this->value;

		$url = JRoute::_('index.php?option=com_vmmigrate&view=test&mode=db&tmpl=component');

		$html = '<div style="display:inline-block; margin-top:5px;">';
		$html .= '<a class="modal btn btn-danger" rel="{handler: \'iframe\', size: {x: 400, y: 200}, onClose: function() {}}" href="'.$url.'" >'.JText::_('VMMIGRATE_TEST_DB_SETTINGS').'</a>';
		$html .= '</div>';
			
		return $html;

	}	
	
}

