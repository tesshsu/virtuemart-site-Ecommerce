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
class VMMigrateViewExtensions extends JViewLegacy
{
	protected $items;
	protected $canDo;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->canDo 		= VMMigrateHelperVMMigrate::getActions();

		$this->languages	= $this->get('Languages');
		$this->packages		= $this->get('Packages');
		$this->components	= $this->get('Components');
		$this->modules		= $this->get('Modules');
		$this->templates	= $this->get('Templates');
		$this->plugins		= $this->get('Plugins');

		$this->addToolbar();
		$lang = JFactory::getLanguage();
		$lang->load('com_installer',JPATH_ADMINISTRATOR);		
		
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

		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_vmmigrate');
		}
		JToolBarHelper::divider();
		$bar->appendButton( 'Popup', 'help', JText::_('help'), 'https://www.daycounts.com/help/vm-migrator/?tmpl=component', 670, 500 );
	}

}
