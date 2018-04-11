<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport('joomla.utilities.date');
jimport('joomla.filesystem.file');
//JHTML::_('behavior.mootools');

// 

require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'email.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'parser.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'helper.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'tickethelper.php');


class FstViewAdmin extends JViewLegacy
{
	var $parser = null;
	var $layoutpreview = 0;

	function display($tpl = null)
	{
		JHTML::_('behavior.modal', 'a.fst_modal');

		$user = JFactory::getUser();
		$this->userid = $user->get('id');

		// remove any admin open stuff
		$_SESSION['admin_create'] = 0;
		$_SESSION['admin_create_user_id'] = 0;
		$_SESSION['ticket_email'] = "";
		$_SESSION['ticket_name'] = "";

		// set up permissions
		$mainframe = JFactory::getApplication();
		$aparams = $mainframe->getPageParameters('com_fst');
		
		$this->permission = FST_Ticket_Helper::getAdminPermissions();
		$model = $this->getModel();
		$model->_perm_where = FST_Ticket_Helper::$_perm_where;
		
		// sort layout
		$layout = JRequest::getVar('layout',  JRequest::getVar('_layout', ''));
		$this->assignRef('layout',$layout);
			
// 
			return $this->displayModerate();
			
// 
	}
	
// 

	function displayModerate()
	{
		if (!$this->permission['mod_kb'])
			return $this->NoPerm();
		
		$this->GetCounts();
		
		if ($this->comments->Process())
			return;
			
		parent::display();
	}
	
// 
	
	function GetCounts()
	{
// 
			
		$this->contentmod = 0;
		$this->comments = new FST_Comments(null,null);
		$this->moderatecount = $this->comments->GetModerateTotal();
	}
	
// 
	
	function NoPerm()
	{
		//echo "needLogin : Current Layout : " . $this->getLayout() . "<br>";
		/*if (array_key_exists('REQUEST_URI',$_SERVER))
		{
			$url = $_SERVER['REQUEST_URI'];//JURI::current() . "?" . $_SERVER['QUERY_STRING'];
		} else {
			$option = JRequest::getString('option','');
			$view = JRequest::getString('view','');
			$layout = JRequest::getString('layout','');
			$Itemid = JRequest::getInt('Itemid',0);
			$url = FSTRoute::x("index.php?option=" . $option . "&view=" . $view . "&layout=" . $layout . "&Itemid=" . $Itemid); 	
		}

		$url = str_replace("&what=find","",$url);
		$url = urlencode(base64_encode($url));*/

		$return = FST_Helper::getCurrentURLBase64();
		$this->assignRef('return',$return);

		$this->setLayout("noperm");
		parent::display();
	}
	
// 

}

