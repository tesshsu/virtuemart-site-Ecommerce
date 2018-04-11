<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );


class FstsViewEmails extends JViewLegacy
{
 
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_("EMAIL_TEMPLATE_MANAGER"), 'fst_emails' );
        //JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        //JToolBarHelper::addNew();
        JToolBarHelper::cancel('cancellist');
		FSTAdminHelper::DoSubToolbar();

        $this->assignRef( 'data', $this->get('Data') );

        parent::display($tpl);
    }
}


