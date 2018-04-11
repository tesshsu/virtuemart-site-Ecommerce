<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

class VMMigrateController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false) {
	
		$app = JFactory::getApplication();
		// Load the submenu.
		VMMigrateHelperVMMigrate::addSubmenu($app->input->getCmd('view', 'rules'));

		// set default view if not set
		$app->input->set('view', $app->input->getCmd('view', 'upgrade'));

		// call parent behavior
		parent::display($cachable);
	}
	
	function upgrade() {
		
		ob_start();
		if(function_exists('error_reporting')) {
			//Store the current error reporting level
			$oldLevel = error_reporting();
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
			if (JDEBUG) {
				error_reporting(E_ALL);
			}
		}
	
		$app = JFactory::getApplication();
		$extension = $app->input->getString('ext','virtuemart');
		$current_step = $app->input->getString('step','start');
		$steps = $app->input->post->get('steps_'.$extension,array(),'array');

		if (!count($steps)) {
			$step = array();
			$step['next'] = '';
			$step['log'] = 'You need to select at least one action';
			$step['log_type'] = 'error';
			echo json_encode($step);
			jexit();
		}
		
		jimport('joomla.application.component.model');
		JLoader::import( 'base',  JPATH_ADMINISTRATOR.'/components/com_vmmigrate/models' );
		JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migrators');
		//JLoader::discover('VMMigrateModel', JPATH_COMPONENT_ADMINISTRATOR . '/migratorsdemo');
		//JLoader::import( $extension, JPATH_ADMINISTRATOR.'/components/com_vmmigrate/migrators' );
	
		$model = JModelLegacy::getInstance( $extension,'VMMigrateModel' );
		$first_step = $steps[0];
		$count_steps = count($steps);
		$last_step = $steps[$count_steps-1];

		$model->setExtension($extension);
		if ($current_step=='start') {
			$result = $model->execute_step($first_step,$steps);
		} else {
			$result = $model->execute_step($current_step,$steps);
		}
		
		if ($current_step==$last_step && $result['percentage']==100) {
			$result['allcompleted'] = true;
		}
				
		//Let's trap the errors that may have been sent to the buffer
		$buf = ob_get_clean();
		if ($buf) {
			$result['systemerror'] = $buf;
		}
				
		echo json_encode($result);
		
		if(function_exists('error_reporting')) {
			error_reporting($oldLevel);
		}
		jexit();
	}

}
