<?php
/**
 * Default controller for Spambotcheck
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */
 
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Default controller class for Spambotcheck
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 *
 * @since        Joomla 1.6 
 */

class SpambotcheckController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'users';	
	
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController          This object to support chaining.
	 *
	 * @since	1.6
	 */
	public function display($cachable = false, $urlparams = false)
	{	
		//Load the submenu
		SpambotcheckHelper::addSubmenu(JRequest::getCmd('view', 'users'));
		parent::display($cachable, $urlparams);
		return $this;
	}
}

?>
