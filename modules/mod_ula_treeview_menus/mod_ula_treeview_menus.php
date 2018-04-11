<?php
/*
# ------------------------------------------------------------------------
# Module: Vina Treeview for Menus
# ------------------------------------------------------------------------
# Copyright (C) 2014 www.VinaGecko.com. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: VinaGecko.com
# Websites: http://VinaGecko.com
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list		= ModUlaTreeviewMenusHelper::getList($params);
$base		= ModUlaTreeviewMenusHelper::getBase($params);
$active		= ModUlaTreeviewMenusHelper::getActive($params);
$active_id 	= $active->id;
$path		= $base->tree;

$showAll	= $params->get('showAllChildren');
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

if(count($list))
{
	ModUlaTreeviewMenusHelper::loadMediaFiles($params, $module);
	require JModuleHelper::getLayoutPath('mod_ula_treeview_menus', $params->get('layout', 'default'));
}