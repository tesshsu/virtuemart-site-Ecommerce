<?php
/**
 * Logs controller for Spambotckeck
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

jimport( 'joomla.application.component.controlleradmin' );

/**
 * Logs Controller
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @since        Joomla 1.6 
 */
class SpambotcheckControllerLogs extends JControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return void
	 * @since Joomla 1.6
	 */
	function __construct($config = array())
	{
		parent::__construct($config = array());

	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Logs', $prefix = 'SpambotcheckModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
?>
