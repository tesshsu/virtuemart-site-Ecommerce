<?php
/**
 * Logs model for Spambotcheck
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2013 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );

/**
 * Logs Model
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @since        Joomla 1.6 
 */
class SpambotcheckModelLogs extends JModelList
{
	/**
	 * forms data array
	 *
	 * @var array
	 */
	var $_data;
	
	/**
	* Items total
	* @var integer
	*/
	var $_total = null;
	
	/**
	* Pagination object
	* @var object
	*/
	var $_pagination = null;
	
	/*
	 * Constructor
	 *
	 */
	function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
			'id', 'a.id',
			'action', 'a.action',
			'email', 'a.email',
			'ip', 'a.ip',
			'username', 'a.username',
			'engine', 'a.engine',
			'attempt_date', 'a.attempt_date',
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
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

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
				'a.*'
				
			)
		);
		$tn = "#__spambot_attempts";
		$query->from($db->quoteName($tn) . ' AS a');
		
		// Filter by search in title.
		/*$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.title LIKE '.$search.' OR a.name LIKE '.$search.')');
		}*/


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}
	
	public function delete(&$pks)
	{		
		// Initialise variables.
		$pks = (array) $pks;
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{
				if (!$table->delete($pk))
				{
					$this->setError($table->getError());
					return false;
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
	
