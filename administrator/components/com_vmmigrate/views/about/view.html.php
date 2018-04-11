<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class VMMigrateViewAbout extends JViewLegacy {

    protected $canDo;

	function display($tpl = null)
	{

		$this->canDo = VMMigrateHelperVMMigrate::getActions();
		$this->config = new VMMigrateHelperConfig();
		$this->addToolbar();
		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title(JText::_('COM_VMMIGRATE'), 'vmmigrate' );
		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_vmmigrate');
		}
		$bar->appendButton( 'Popup', 'help', JText::_('help'), 'https://www.daycounts.com/help/vm-migrator/?tmpl=component', 670, 500 );
	}

}