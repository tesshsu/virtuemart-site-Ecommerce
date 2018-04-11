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

class ZtvirtuemarterViewSetting extends JViewLegacy
{

    protected $form;
    protected $item;
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->_addToolbar();
        parent::display($tpl);
    }

    protected function _addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_ZTVIRTUEMARTER_ADMIN_TITLE'));
        JToolBarHelper::apply('setting.apply');
        JToolBarHelper::cancel();
//        JToolbarHelper::preferences('com_ztvirtuemarter');
    }

}
