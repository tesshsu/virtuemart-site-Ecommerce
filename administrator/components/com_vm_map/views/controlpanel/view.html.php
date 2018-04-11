<?php
/*------------------------------------------------------------------------
 * com_vm_map - Virtuemart 2 SiteMap
 * ------------------------------------------------------------------------
 * St42 - P. Kohl
 * copyright Copyright (C) 2011 st42.fr. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.st42.fr
 */

defined('_JEXEC') or die ;
class vm_mapViewControlPanel extends JViewLegacy
{
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_( 'COM_VM_MAP'),'newsfeeds');

		$user	= JFactory::getUser();
		if ($user->authorise('core.admin', 'com_vm_map'))
		{
			JToolBarHelper::preferences('com_vm_map');
		}

		parent::display($tpl);
	}

}
?>