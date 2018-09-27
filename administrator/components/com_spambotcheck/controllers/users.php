<?php
/**
 * Users controller for Spambotckeck
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

jimport( 'joomla.application.component.controlleradmin' );

/**
 * Users Controller
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @since        Joomla 1.6 
 */
class SpambotcheckControllerUsers extends JControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return void
	 * @since Joomla 1.6
	 */
	function __construct($config = array())
	{
		parent::__construct($config = array());
		$this->registerTask('distrust', 'trust');
		$this->registerTask('block',		'changeBlock');
		$this->registerTask('unblock',		'changeBlock');

	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Users', $prefix = 'SpambotcheckModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	public function trust()
	{
		// Check for request forgeries
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
		
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$data = array('distrust' => 0, 'trust' => 1);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
		
		if (empty($cid))
        {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        }
        else
        {
			// Get the model.
            $model = $this->getModel();
 
            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);
 
            // Toogle Suspicion state of items.
            if (!$model->trust($cid, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_SET_TO_DISTRUST';
                }
                elseif ($value == 1)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_SET_TO_TRUST';
                }
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
	/**
	 * Method to change the block status on a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function changeBlock()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('block' => 1, 'unblock' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the Joomla User model.
			$model = $this->getModel($name = 'User', $prefix = 'UsersModel', $config = array('ignore_request' => true));

			// Change the state of the records.
			if (!$model->block($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_BLOCKED', count($ids)));
				}
				elseif ($value == 0)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_UNBLOCKED', count($ids)));
				}
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
	/**
	 * Method to activate a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the Joomla User model.
			$model = $this->getModel($name = 'User', $prefix = 'UsersModel', $config = array('ignore_request' => true));

			// Change the state of the records.
			if (!$model->activate($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(JText::plural('COM_USERS_N_USERS_ACTIVATED', count($ids)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
}
?>
