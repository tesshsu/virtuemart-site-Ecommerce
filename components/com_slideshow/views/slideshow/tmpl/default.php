<?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/

defined('_JEXEC') or die('Restircted access');

require_once JPATH_SITE.'/components/com_slideshow/helpers/helper.php';

$id = JRequest::getVar('slideshow',   $this -> slder_id , '', 'int');
$slideshow_class = new SlideshowsHelper;
$slideshow_class->slideshow_id = $id;
$slideshow_class->type = 'component';
$slideshow_class->class_suffix = '';
$slideshow_class->module_id =  $this -> slder_id ;
echo $slideshow_class->render_html();
