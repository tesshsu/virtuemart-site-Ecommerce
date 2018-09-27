<?php
/**
 * Users model for Spambotcheck
 *
 * @author       Aicha Vack

 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist');

/**
 * visforms Model
 *
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @since        Joomla 1.6 
 */
class SpambotcheckModelUsers extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'user_id', 'a.user_id',
				'ip', 'a.ip',
				'hits', 'a.hits',
				'suspicious', 'a.suspicious',
				'trust', 'a.trust',
				'note', 'a.note',
				'name', 'b.name',
				'username', 'b.username',
				'email', 'b.email',
				'registerdate', 'b.registerDate',
				'block', 'b.block',
				'activation', 'b.activation',
				'groupname', 'ug.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$trust = $this->getUserStateFromRequest($this->context.'.filter.trust', 'filter_trust', '');
		$this->setState('filter.trust', $trust);

		$suspicious = $this->getUserStateFromRequest($this->context.'.filter.suspicious', 'filter_suspicious', '');
		$this->setState('filter.suspicious', $suspicious);
		
		$block = $this->getUserStateFromRequest($this->context.'.filter.block', 'filter_block', '');
		$this->setState('filter.block', $block);
		
		$activation = $this->getUserStateFromRequest($this->context.'.filter.activation', 'filter_activation', '');
		$this->setState('filter.activation', $activation);
		
		$range = $this->getUserStateFromRequest($this->context.'.filter.range', 'filter_range');
		$this->setState('filter.range', $range);


		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.trust');
		$id	.= ':'.$this->getState('filter.suspicious');
		$id	.= ':'.$this->getState('filter.block');
		$id	.= ':'.$this->getState('filter.activation');
		$id .= ':'.$this->getState('filter.range');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*, b.id as bid, b.name as name, b.email as email, b.username as username, b.registerDate as registerdate, b.block as block, b.activation as activation, ' .
				'm.user_id as muser_id, m.group_id as mgroup_id, ug.id as ugid, ug.title as groupname'
			)
		);
		$query->from('#__user_spambotcheck AS a');
		$query->join('LEFT', '#__users AS b ON b.id=a.user_id');
		$query->join('LEFT', '#__user_usergroup_map AS m ON m.user_id=b.id');
		$query->join('LEFT', '#__usergroups AS ug ON ug.id=m.group_id');
		$query->group('a.id,a.user_id,a.ip,a.hits,a.suspicious,a.trust,a.note,b.name,b.username,b.email,b.block,b.registerDate,b.activation');

		// Filter by trust state
		$trust = $this->getState('filter.trust');
		if (is_numeric($trust)) {
			$query->where('a.trust = ' . (int) $trust);
		}
		
		// Filter by suspicious state
		$suspicious = $this->getState('filter.suspicious');
		if (is_numeric($suspicious)) {
			$query->where('a.suspicious = ' . (int) $suspicious);
		}
		
		// Filter by block state
		$block = $this->getState('filter.block');
		if (is_numeric($block)) {
			$query->where('b.block = ' . (int) $block);
		}

		// If the model is set to check the activated state, add to the query.
		$active = $this->getState('filter.activation');
		if (is_numeric($active))
		{
			if ($active == '0')
			{
				// might be '' or '0'
				$query->where($query->length('b.activation').' != 32');
			}
			elseif ($active == '1')
			{
				// GUID
				$query->where($query->length('b.activation').' = 32');
			}
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) 
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.ip LIKE '.$search.' OR b.username LIKE '.$search.' OR b.email LIKE '.$search.')');
		}

		// Apply the range filter.
		$range = $this->getState('filter.range');
		if ($range != '' && $range != '*')
		{
			jimport('joomla.utilities.date');

			// Get UTC for now.
			$dNow = new JDate;
			$dStart = clone $dNow;

			switch ($range)
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$app	= JFactory::getApplication();
					$offset	= $app->getCfg('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart	= new JDate('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$tz = new DateTimeZone('GMT');
					$dStart->setTimezone($tz);
					break;
			}

			if ($range == 'post_year')
			{
				$query->where(
					'b.registerDate < '.$db->quote($dStart->format('Y-m-d H:i:s'))
				);
			}
			else
			{
				$query->where(
					'b.registerDate >= '.$db->quote($dStart->format('Y-m-d H:i:s')).
					' AND b.registerDate <='.$db->quote($dNow->format('Y-m-d H:i:s'))
				);
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
	
	public function trust(&$pks, $value = 0) 
	{
		// Initialise variables.
        $user = JFactory::getUser();
        $table = $this->getTable();
        $pks = (array) $pks;
				
		 // Attempt to change the state of the records.
        if (!$table->trust($pks, $value))
        {
            $this->setError($table->getError());
            return false;
        }
 
        if (in_array(false, $result, true))
        {
            $this->setError($table->getError());
            return false;
        }
 
        // Clear the component's cache
        $this->cleanCache();
 
        return true;
	}
	
	public function delete(&$pks)
	{		
		// Initialise variables.
		$pks = (array) $pks;
		$table = $this->getTable();
		$usertable = $this->getTable('User', 'JTable');
		// Initialise variables.
		$user	= JFactory::getUser();

		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');


		if (in_array($user->id, $pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF'));
			return false;
		}
		

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin', 'com_users')) ? false : $allow;
			
				$userIp = plgSpambotCheckHelpers::getTableFieldValue('#__user_spambotcheck', 'ip', 'user_id', $pk);
				if ($allow)
				{
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
					break;
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
			
			//clean up user_spambotcheck fields
			plgSpambotCheckHelpers::cleanUserSpambotTable($userIp, $pk);
			
			if ($usertable->load($pk))
			{
				if (!$usertable->delete($pk))
				{
					$this->setError($usertable->getError());
					return false;
				}
			}
			else
			{
				$this->setError($usertable->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Gets the list of users and adds expensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (empty($this->cache[$store]))
		{
			$items = parent::getItems();

			// Bail out on an error or empty list.
			if (empty($items))
			{
				$this->cache[$store] = $items;

				return $items;
			}

			// Joining the groups with the main query is a performance hog.
			// Find the information only on the result set.

			// First pass: get list of the user id's and reset the counts.
			$userIds = array();
			foreach ($items as $item)
			{
				$userIds[] = (int) $item->user_id;
				$item->group_count = 0;
				$item->group_names = '';
			}

			// Get the counts from the database only for the users in the list.
			$db = $this->getDbo();
			$query = $db->getQuery(true);

			// Join over the group mapping table.
			$query->select('map.user_id, COUNT(map.group_id) AS group_count')
				->from('#__user_usergroup_map AS map')
				->where('map.user_id IN ('.implode(',', $userIds).')')
				->group('map.user_id')
				// Join over the user groups table.
				->join('LEFT', '#__usergroups AS g2 ON g2.id = map.group_id');

			$db->setQuery($query);

			// Load the counts into an array indexed on the user id field.
			$userGroups = $db->loadObjectList('user_id');

			$error = $db->getErrorMsg();
			if ($error)
			{
				$this->setError($error);

				return false;
			}

			// Second pass: collect the group counts into the master items array.
			foreach ($items as &$item)
			{
				
				if (isset($userGroups[$item->user_id]))
				{
					$item->group_count = $userGroups[$item->user_id]->group_count;
					//Group_concat in other databases is not supported
					$item->group_names = $this->_getUserDisplayedGroups($item->user_id);
				}
			}

			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}

		return $this->cache[$store];
	}
	
	public function _getUserDisplayedGroups($user_id)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT title FROM ".$db->quoteName('#__usergroups')." ug left join ".
				$db->quoteName('#__user_usergroup_map')." map on (ug.id = map.group_id)".
				" WHERE map.user_id=".$user_id;

		$db->setQuery($sql);
		$result = $db->loadColumn();
		return implode("\n", $result);
	}
}