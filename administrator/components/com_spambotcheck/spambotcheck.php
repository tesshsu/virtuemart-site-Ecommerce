<?php
/**
 * Form component for Joomla
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
defined('_JEXEC') or die('Restricted access');

// Include dependancies
jimport('joomla.application.component.controller');

// Register helper class
JLoader::register('SpambotcheckHelper', dirname(__FILE__) . '/helpers/spambotcheck.php');
JLoader::register('JHTMLSpambotcheck', dirname(__FILE__) . '/helpers/html/spambotcheck.php');
JLoader::register('plgSpambotCheckHelpers', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'spambotcheck'.DIRECTORY_SEPARATOR.'SpambotCheck'.DIRECTORY_SEPARATOR.'SpambotCheckHelpers.php');
JLoader::register('UsersHelper', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_users'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'users.php');
JLoader::register('UsersModelUser', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_users'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'user.php');
JLoader::register('JHtmlUsers', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_users'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'users.php');

$lang = JFactory::getLanguage();
$extension = 'com_users';
$base_dir = JPATH_ADMINISTRATOR;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, null, $language_tag, $reload);

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_spambotcheck')) 
{
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Create the controller
$controller = JControllerLegacy::getInstance('Spambotcheck',  array('default_view' =>'Users'));

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

?>