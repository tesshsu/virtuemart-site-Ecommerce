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
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class VMMigrateModelLog extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JController
     * @since	1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'extension',
                'task',
                'state',
				'source_id'
            );
        }

        parent::__construct($config);
    }

    protected $basename;

    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

        $extension = $this->getUserStateFromRequest($this->context . '.filter.extension', 'filter_extension');
        $this->setState('filter.extension', $extension);

        $task = $this->getUserStateFromRequest($this->context . '.filter.task', 'filter_task');
        $this->setState('filter.task', $task);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $state);

        $order = $this->getUserStateFromRequest($this->context . '.filter.order', 'filter_order');
        $this->setState('filter.order', $order);

        $order_dir = $this->getUserStateFromRequest($this->context . '.filter.order_dir', 'filter_order_Dir');
        $this->setState('filter.order_dir', $order_dir);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_vmmigrate');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('created', 'desc');
    }

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.task');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.order');
		$id .= ':'.$this->getState('filter.order_dir');

		return parent::getStoreId($id);
	}

    protected function getListQuery() {
        // Get the application object
        $app = JFactory::getApplication();
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__vmmigrate_log');

		$search = $this->getState('filter.search','');
		if ($search) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('note LIKE '.$search);
		}

		$filter_extension = $this->getState('filter.extension','');
		if ( $filter_extension ) {
			$query->where('extension = ' . $db->q($filter_extension));
		}

		$filter_task = $this->getState('filter.task','');
		if ( $filter_task ) {
			$query->where('task = ' . $db->q($filter_task));
		}

		$filter_state = $this->getState('filter.state','');
		if ( $filter_state ) {
			$query->where('state = ' . $db->q($filter_state));
		}

		$filter_order = $this->getState('filter.order','created');
		$filter_order_Dir = $this->getState('filter.order_dir','desc');
		$query->order($db->q($filter_order).' '.$filter_order_Dir);

        return $query;
    }


}
