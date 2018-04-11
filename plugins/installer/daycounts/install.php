<?php
/*------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgInstallerDaycountsInstallerScript {

	/**
	* Called on installation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function install($adapter) {
	}

	/**
	* Called on uninstallation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*/
	function uninstall($adapter) {
		//echo '<p>'. JText::_('1.6 Custom uninstall script') .'</p>';
	}

	/**
	* Called on update
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function update($adapter) {
		//echo '<p>'. JText::_('1.6 Custom update script') .'</p>';
	}

	/**
	* Called before any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function preflight($route, $adapter) {
        $jversion = new JVersion();

        // Installing component manifest file version
        $component_version = $adapter->get("manifest")->version;
        $joomla_version = $jversion->getShortVersion();
		
        $minimum_version = $adapter->get("manifest")->attributes()->minimum_version;
        $maximum_version = $adapter->get("manifest")->attributes()->maximum_version;

        //abort if version less than minimun
        if ($minimum_version && version_compare($joomla_version, $minimum_version, 'lt')) {
            Jerror::raiseWarning(null, 'Cannot install in a Joomla release prior to ' . $minimum_version);
            return false;
        }
        // abort if the current Joomla release is older
        if ($maximum_version && version_compare($joomla_version, $maximum_version.'.9999', 'gt')) {
            Jerror::raiseWarning(null, 'Cannot install in a Joomla release greater than ' . $maximum_version);
            return false;
        }
    
	}

	/**
	* Called after any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function postflight($route, $adapter) {
		if ($route=='install' || $route=='update') {
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__update_sites');
			$query->set($db->qn('location').' = REPLACE('.$db->qn('location').','.$db->q('https://www.daycounts.com').','.$db->q('http://www.daycounts.com').')');
			$query->where('location like '.$db->q('https://www.daycounts.com%'));
			$db->setQuery($query);
			$db->execute();
			
			$url = 'index.php?option=com_plugins&filter_search=daycounts&filter_folder=installer';
			echo '<br/><p><a href="'.$url.'">Configure</a></p>';
		}
	}
}