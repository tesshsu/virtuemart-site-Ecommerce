<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);

if (!JDEBUG)
{
	error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING);
}

// Require the base controller
require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'j3helper.php' );
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'settings.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'helper.php' );
require_once( JPATH_COMPONENT.DS.'adminhelper.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

if (!function_exists("print_p"))
{
	function print_p($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";	
	}
}
	
// do version check
$ver_inst = FSTAdminHelper::GetInstalledVersion();
$ver_files = FSTAdminHelper::GetVersion();
	
	
if (FST_Helper::Is16())
{
	if (!JFactory::getUser()->authorise('core.manage', 'com_fst')) 
	{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}
// if bad version display warning message
if ($ver_files != $ver_inst)
{
	$task = JRequest::getVar('task');	
	$view = JRequest::getVar('view');

	if ($task != "update" || $view != "backup")
		JError::raiseWarning( 100, JText::sprintf('INCORRECT_VERSION',FSTRoute::x('index.php?option=com_fst&view=backup&task=update')) );
	
	if ($view != "" && $view != "backup")
		JRequest::setVar('view','');
}
// if bad version and controller is not fsts dont display
	
	
// Create the controller
$controllername = $controller;
$classname    = 'FstsController'.$controller;
$controller   = new $classname( );

$css = JRoute::_( JURI::root()."index.php?option=com_fst&view=css" );
$document = JFactory::getDocument();
$document->addStyleSheet($css); 
$document->addStyleSheet(JURI::root().'administrator/components/com_fst/assets/css/main.css'); 
$document->addStyleSheet(JURI::root().'components/com_fst/assets/css/popup.css'); 
$document->addScript( JURI::root().'components/com_fst/assets/js/popup.js' );
$document->addScript( JURI::root().'administrator/components/com_fst/assets/js/translate.js' );

FST_Helper::IncludeJQuery(true);

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
