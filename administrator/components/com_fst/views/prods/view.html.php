<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );


class FstsViewProds extends JViewLegacy
{
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_("PRODUCTS"), 'fst_prods' );
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::custom('import','copy','copy','IMPORT_FROM_VIRTUEMART',false);
        JToolBarHelper::cancel('cancellist');
		FSTAdminHelper::DoSubToolbar();

        $this->assignRef( 'lists', $this->get('Lists') );
        $this->assignRef( 'data', $this->get('Data') );
        $this->assignRef( 'pagination', $this->get('Pagination'));

		$categories = array();
		$categories[] = JHTML::_('select.option', '-1', JText::_("IS_PUBLISHED"), 'id', 'title');
		$categories[] = JHTML::_('select.option', '1', JText::_("PUBLISHED"), 'id', 'title');
		$categories[] = JHTML::_('select.option', '0', JText::_("UNPUBLISHED"), 'id', 'title');
		$this->lists['published'] = JHTML::_('select.genericlist',  $categories, 'ispublished', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->lists['ispublished']);

		$what = JRequest::getVar('what');
		if ($what == "togglefield")
			return $this->toggleField();

        parent::display($tpl);
    }

	function toggleField()
	{
		$id = JRequest::getVar('id');
		$field = JRequest::getVar('field');			
		$val = JRequest::getVar('val');	

		if ($field == "")
			return;
		if ($id < 1)
			return;
		if ($field != "inkb" && $field != "insupport" && $field != "intest")
			return;

		$db = JFactory::getDBO();

		$qry = "UPDATE #__fst_prod SET ".FSTJ3Helper::getEscaped($db, $field)." = ".FSTJ3Helper::getEscaped($db, $val)." WHERE id = ".FSTJ3Helper::getEscaped($db, $id);
		$db->setQuery($qry);
		$db->Query();

		echo FST_GetYesNoText($val);
		exit;
	}
}



