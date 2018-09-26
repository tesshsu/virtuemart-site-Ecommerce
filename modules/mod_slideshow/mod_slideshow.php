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
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.application.component.helper');
?>
<?php
require_once JPATH_SITE.'/components/com_slideshow/helpers/helper.php';

$id = $module -> id;
$cis_class = new SlideshowsHelper();
$cis_class->slideshow_id = $params->get('choose_slideshow');
$class_suffix = $params->get('class_suffix',$id);
$cis_class->type = 'module';
$cis_class->class_suffix = $class_suffix;
$cis_class->module_id = $id;
echo $cis_class->render_html();
?>



