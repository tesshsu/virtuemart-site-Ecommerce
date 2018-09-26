<?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
?>
<?php defined('_JEXEC') or die('Restricted access'); 
JLog::addLogger(
	array('text_file' => 'com_slider.php'),
	JLog::ALL,
	array('com_slider')
);
JError::$legacy = false;
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Slideshow');
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task', 'display'));
$controller->redirect();
