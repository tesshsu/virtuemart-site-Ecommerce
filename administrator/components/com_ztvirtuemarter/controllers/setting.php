<?php

/**
 * Zt Virtuemarter
 *
 * @package     Joomla
 * @subpackage  Component
 * @version     1.0.0
 * @author      ZooTemplate
 * @email       support@zootemplate.com
 * @link        http://www.zootemplate.com
 * @copyright   Copyright (c) 2015 ZooTemplate
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Setting controller class
 */
class ZtvirtuemarterControllerSetting extends JControllerAdmin
{

    /**
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->input = JFactory::getApplication()->input;
    }

    /**
     * Save setting to database
     */
    public function apply()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Get data
        $settings = $this->input->post->get('jform', array(), 'array');
        // Get model
        $model = $this->getModel('setting');
        /**
         * Setup data array for saving
         * @todo It must be implemented in form
         */
        $data['setting'] = $settings;
        // Save data
        if (!$model->save($data)) {
            $this->setMessage(JText::_('COM_ZTVIRTUEMARTER_SETTING_SAVE_FAILED'), 'error');
        } else {
            $this->setMessage(JText::_('COM_ZTVIRTUEMARTER_SETTING_SAVE_SUCCESSED'));
        }
        // Redirect back
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option, false));
    }

    /**
     * Wrapped method to get model
     * @param type $name
     * @param type $prefix
     * @param type $config
     * @return type
     */
    public function getModel($name = '', $prefix = 'ZtvirtuemarterModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

}
