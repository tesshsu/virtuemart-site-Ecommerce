<?php

/**
 * Module PresetOption
 * just for legacy, will be removed
 * @package UlaThemes
 * @copyright (C) 2015 The UlaThemes Team
 * @Email: ulathemes@gmail.com
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * www.ulathemes.com
 */
 
 
require_once __DIR__ . '/helper.php';
 
$presets            = $params->get('presets'); 
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx'));
$presets               = ModPresetOptionHelper::getList($params); 
require JModuleHelper::getLayoutPath('mod_preset_options', $params->get('layout', 'default'));



$document 			= JFactory::getDocument();
$document->addStylesheet(JURI::base(true) . '/modules/'.basename(dirname(__FILE__)).'/assets/css/helix3-options.css');

$document->addScript(JURI::base(true) . '/modules/'.basename(dirname(__FILE__)).'/assets/js/helix3-options.js');
$document->addScript(JURI::base(true) . '/modules/'.basename(dirname(__FILE__)).'/assets/js/jquery.cookie.js');

?>
