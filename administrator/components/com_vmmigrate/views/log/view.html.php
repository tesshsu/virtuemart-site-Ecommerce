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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

/**
 * View class for a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class VMMigrateViewLog extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $canDo;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->canDo 		= VMMigrateHelperVMMigrate::getActions();

		$this->addToolbar();
		
		VMMigrateHelperVMMigrate::setJoomlaVersionLayout($this);
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

		if ($this->canDo->get('core.delete')) {
			$bar->appendButton('Confirm','VMMIGRATE_CONFIRM_DELETE', 'delete', 'VMMIGRATE_DELETE_SELECTED', 'log.deletebyid',true);
			$bar->appendButton('Confirm','VMMIGRATE_CONFIRM_DELETE_ALL', 'delete', 'VMMIGRATE_DELETE_ALL', 'log.delete',false);
		}
		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_vmmigrate');
		}
		JToolBarHelper::divider();
		$bar->appendButton( 'Popup', 'help', JText::_('help'), 'https://www.daycounts.com/help/vm-migrator/?tmpl=component', 670, 500 );
	}

}
