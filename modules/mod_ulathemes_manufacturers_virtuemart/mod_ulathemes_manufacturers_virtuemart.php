<?php
/*
# ------------------------------------------------------------------------
# Ulathemes Manufacturers Carousel for VirtueMart for Joomla 3
# ------------------------------------------------------------------------
# Copyright(C) 2014 www.Ulathemes.com. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: Ulathemes.com
# Websites: http://ulathemes.com
# ------------------------------------------------------------------------
*/

defined('_JEXEC') or die('Restricted access');

// VirtueMart config
if(!class_exists('VmConfig')) {
	require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
}

VmConfig::loadConfig();
VmConfig::loadJLang($module->module, true);

$model 			= VmModel::getModel('Manufacturer');
$manufacturers 	= $model->getManufacturers(true, true, true);
$model->addImages($manufacturers);
if(empty($manufacturers)) return false;

// get params
$showImage 		= $params->get('showImage', 1);
$showName		= $params->get('showName', 0);
$linkOnImage	= $params->get('linkOnImage', 1);
$linkOnName		= $params->get('linkOnName', 0);
$bgImage		= $params->get('bgImage', 		null);
if($bgImage != '') {
	if(strpos($bgImage, 'http://') === FALSE) {
		$bgImage = JURI::base() . $bgImage;
	}
}
$isBgColor		= $params->get('isBgColor', 	1);
$bgColor		= $params->get('bgColor', 		'#dddddd');

$rows				= $params->get('rows', '1');
$rows				= $params->get('rows', '1');
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