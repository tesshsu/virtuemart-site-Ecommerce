<?php
/**
 * Logs view for Spambotcheck
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2013 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * visforms View
 *
 * @package    Joomla.Administratoar
 * @subpackage com_spambotcheck
 * @since      Joomla 1.6
 */
class SpambotcheckViewLogs extends JViewLegacy
{
	protected $form;
	protected $items;
	protected $state;
	protected $canDo;
	
	/**
	 * visforms view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		
		// Get data from the model
		$this->form	= $this->get('Form');
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->canDo = JHelperContent::getActions('com_users');
		
		//$items = $this->get( 'Data');
		$pagination = $this->get('Pagination');

		$this->assignRef('pagination', $pagination);
		
		// We don't need toolbar in the modal window.
		if (($this->getLayout() !== 'modal') && ($this->getLayout() !== 'modal_data')) {
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
		
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$doc = JFactory::getDocument();
		$css = '.icon-48-spambotcheck {background:url(../administrator/components/com_spambotcheck/images/logo-banner.png) no-repeat;}';
   		$doc->addStyleDeclaration($css);

		
		

		JToolBarHelper::title(JText::_( 'COM_SPAMBOTCHECK_LOGS' ), 'spambotcheck' );
		if ($this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('COM_SPAMBOTCHECK_DELETE', 'logs.delete');
		}
		 // Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_spambotcheck'))     
		{
			JToolBarHelper::preferences('com_spambotcheck');
		}
	}
}
