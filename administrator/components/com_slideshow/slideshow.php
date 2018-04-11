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

JError::$legacy = false;
$document = JFactory::getDocument();
jimport('joomla.application.component.controller');
?>
<?php
$controller = JControllerLegacy::getInstance('Slideshow');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
