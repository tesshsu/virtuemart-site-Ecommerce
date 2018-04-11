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
defined('_JEXEC') or die;


class ModPresetOptionHelper
{
    
	public static function &getList(&$params)
	{
	   
    $numberpresets            = $params->get('presets'); 
    $app        = JFactory::getApplication();
    $template   = $app->getTemplate(true);
    $params     = $template->params;  
    $presets = array();
     
    
    for($i = 1; $i<=$numberpresets; $i++){
        
        $preset_major = 'preset'.$i.'_major';
        $presets[$i] = $params->get($preset_major); 
    }
     
    
     
    	 

		return $presets;
	}
}
