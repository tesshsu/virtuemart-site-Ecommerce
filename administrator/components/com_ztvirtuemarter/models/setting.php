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

jimport('joomla.application.component.modeladmin');

/**
 *
 */
class ZtvirtuemarterModelSetting extends JModelAdmin
{

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     * @return    JTable    A database object
     * @since    2.5
     */
    public function getTable($type = 'Setting', $prefix = 'ZtvirtuemarterTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }


    public function getItem($pk = null)
    {
        if($pk == null) {
            $pk = 1;
        }
        return parent::getItem($pk);
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_ztvirtuemarter.edit', 'setting', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     *
     * @param int $data
     * @return boolean
     */
    public function save($data)
    {
        $item = $this->getItem(1);
        if ($item->id == 1) {
            // Record exists than do UPDATE
            $data['id'] = 1;
        }
        return parent::save($data);
    }

}
