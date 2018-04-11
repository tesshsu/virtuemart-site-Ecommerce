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
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Tracks list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class VMMigrateControllerLog extends JControllerLegacy {

    /**
     * @var		string	The context for persistent state.
     * @since	1.6
     */
    protected $context = 'com_vmmigrate.log';

    /**
     * Proxy for getModel.
     *
     * @param	string	$name	The name of the model.
     * @param	string	$prefix	The prefix for the model class name.
     *
     * @return	JModel
     * @since	1.6
     */
    public function &getModel($name = 'Log', $prefix = 'VMMigrateModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    /**
     * Method to remove a record.
     *
     * @return	void
     * @since	1.6
     */
    public function delete() {
        // Check for request forgeries.
		$app = JFactory::getApplication();
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$option = $app->input->getCmd('option');

        $ids = $app->input->get('cid', array(), 'array');

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->delete('#__vmmigrate_log');
		$db->setQuery($query);
		if ($db->query()) {
			$app = & JFactory::getApplication();
			$app->setUserState($option.".filter_extension",'');
			$app->setUserState($option.".filter_task",'');
			$app->setUserState($option.".filter_state",'');
			$app->setUserState($option.".search",'');
			$app->setUserState($option.".limitstart",0);
            $this->setMessage(JText::_('HISTORY_RECORD_DELETED'));
		} else {
			$app->enqueueMessage($model->getError(),'error');
		}

        $this->setRedirect('index.php?option='.$option.'&view=log');

    }

    public function deletebyid() {
        
		$app = JFactory::getApplication();
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$option = $app->input->getCmd('option');

        $ids = $app->input->get('cid', array(), 'array');

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->delete('#__vmmigrate_log')
			->where("id in ('".implode("','",$ids)."')");
		$db->setQuery($query);
		if ($db->query()) {
            $this->setMessage(JText::_('HISTORY_RECORD_DELETED'));
		} else {
			$app->enqueueMessage($model->getError(),'error');
		}

        $this->setRedirect('index.php?option='.$option.'&view=log');
    }

}
