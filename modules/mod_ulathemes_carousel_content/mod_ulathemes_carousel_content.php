<?php
/*
# ------------------------------------------------------------------------
# Ulathemes Article Carousel
# ------------------------------------------------------------------------
# Copyright(C) 2014 www.ulathemes.com. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: ulathemes.com
# Websites: http://ulathemes.com
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once dirname(__FILE__) . '/helper.php';

$input 		= JFactory::getApplication()->input;
$idbase 	= $params->get('catid');
$cacheid 	= md5(serialize(array ($idbase, $module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class 	= 'ModUlathemesArticlesCarouselHelper';
$cacheparams->method 	= 'getList';
$cacheparams->methodparams 	= $params;
$cacheparams->modeparams 	= $cacheid;

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if(empty($list))
{
	echo 'No item found! Please check your config!';
	return;
}

// get params
$classSuffix 	= $params->get('moduleclass_sfx', '');
$bgImage		= $params->get('bgImage', 		null);
if($bgImage != '') {
	if(strpos($bgImage, 'http://') === FALSE) {
		$bgImage = JURI::base() . $bgImage;
	}
}
$isBgColor		= $params->get('isBgColor', 0);
$bgColor		= $params->get('bgColor', '#dddddd');

// display params
$showImage			= $params->get('showImage', 1);
$resizeImage		= $params->get('resizeImage', 1);
$imagegWidth		= $params->get('imagegWidth', 100);
$imagegHeight		= $params->get('imagegHeight', 100);
$showTitle			= $params->get('showTitle', 1);
$showCreatedDate	= $params->get('show_date', 0);
$showCategory		= $params->get('show_category', 0);
$showHits			= $params->get('show_hits', 0);
$introText			= $params->get('show_introtext', 1);
$readmore			= $params->get('show_readmore', 1);

// Carousel Params
//$rows				= $params->get('rows', '1');
$cols				= $params->get('cols', '4');
$colsOnDesktop		= $params->get('colsOnDesktop', '4');
$colsOnDesktopSmall	= $params->get('colsOnDesktopSmall', '3');
$colsOnTablet		= $params->get('colsOnTablet', '2');
$colsOnMobile		= $params->get('colsOnMobile', '1');
$itemsSpace			= $params->get('itemsSpace', '30');
$scrollItems		= $params->get('scrollItems', '1');
$autoPlay			= $params->get('autoPlay', 1);
$autoPlayTimeout	= $params->get('autoPlayTimeout', '5000');
$pauseOnHover		= $params->get('pauseOnHover', 0);
$loop				= $params->get('loop', 1);
$mouseDrag			= $params->get('mouseDrag', 1);
$touchDrag			= $params->get('touchDrag', 0);
$pagination			= $params->get('pagination', 0);
$navigation			= $params->get('navigation', 1);
$nextNav			= $params->get('nextNav', 'Next');
$prevNav			= $params->get('prevNav', 'Prev');

require(JModuleHelper::getLayoutPath($module->module, 'default'));