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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General controller class
 */
class ZtvirtuemarterController extends JControllerLegacy
{

    protected $default_view = 'setting';

    /**
     * display task
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        // set default view if not set
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'setting'));

        // call parent behavior
        parent::display($cachable);
    }

}
