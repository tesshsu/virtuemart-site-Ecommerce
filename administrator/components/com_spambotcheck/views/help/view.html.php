<?php
/**
 * Help view for Spambotcheck
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2013 vi-solutions
 * @since        Joomla 1.6 
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
jimport( 'joomla.application.component.view' );

/**
 * vishelp view
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @since        Joomla 1.6 
 */
class SpambotcheckViewHelp extends JViewLegacy
{
  /**
   * HTML view display method
   *
   * @access  public
   * @param   string  $tpl  The name of the template file to parse
   * @return  void
   * @since   1.5.5
   */
  function display($tpl = null)
  {
    $document = JFactory::getDocument();
	$css = '.icon-48-visform {background:url(../administrator/components/com_spambotcheck/images/logo-banner.png) no-repeat;}';
	$document->addStyleDeclaration($css);
	JToolBarHelper::title(JText::_( 'COM_SPAMBOTCHECK_HELP' ), 'visform' );
	
	// We don't need toolbar in the modal window.
		if (($this->getLayout() !== 'modal') && ($this->getLayout() !== 'modal_data')) {
			$this->sidebar = JHtmlSidebar::render();
		}
	
	parent::display($tpl);
  }
}