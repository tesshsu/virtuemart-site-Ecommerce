<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;


jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.folder');



class FstsViewProd extends JViewLegacy
{

	function display($tpl = null)
	{
		$prod	=& $this->get('Data');
		$isNew		= ($prod->id < 1);

		$text = $isNew ? JText::_("NEW") : JText::_("EDIT");
		JToolBarHelper::title(   JText::_("PRODUCT").': <small><small>[ ' . $text.' ]</small></small>', 'fst_prods' );
		if (FST_Helper::Is16())
		{
			JToolBarHelper::custom('translate','translate', 'translate', 'Translate', false);
			JToolBarHelper::spacer();
		}
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		FSTAdminHelper::DoSubToolbar();

		$this->assignRef('prod',		$prod);
		
		$path = JPATH_SITE.DS.'images'.DS.'fst'.DS.'products';

		if (!file_exists($path))
			mkdir($path,0777,true);
		
		$files = JFolder::files($path,'(.png$|.jpg$|.jpeg$|.gif$)');
		
		$sections[] = JHTML::_('select.option', '', JText::_("NO_IMAGE"), 'id', 'title');
		foreach ($files as $file)
		{
			$sections[] = JHTML::_('select.option', $file, $file, 'id', 'title');
		}
				
		$lists['images'] = JHTML::_('select.genericlist',  $sections, 'image', 'class="inputbox" size="1" ', 'id', 'title', $prod->image);

		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}


