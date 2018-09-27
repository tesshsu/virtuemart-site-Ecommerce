<?php

/**
 * @version		$Id: script.php 22354 2011-11-07 05:01:16Z github_bot $
 * @package		com_visforms
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );


class com_spambotcheckInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
   // public function __constructor(JAdapterInstance $adapter);

	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	//public function preflight($route, JAdapterInstance $adapter);

	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
    // public function postflight($route, JAdapterInstance $adapter);

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
		//create an empty dataset in table user_spambotcheck for each user
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__users'));
		$db->setQuery($query);
		$users = $db->loadColumn();
 
        foreach ($users as $user)
        {
			 
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->clear();
			 
			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__user_spambotcheck'))
				->columns(array($db->quoteName('user_id'), $db->quoteName('note')))
				->values(implode(',', array($db->quote($user), $db->quote('User was created before component installation.'))));
			 
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$db->execute();
        }
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	//public function update(JAdapterInstance $adapter);

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	//public function uninstall(JAdapterInstance $adapter);
	
	
	
}

?>
