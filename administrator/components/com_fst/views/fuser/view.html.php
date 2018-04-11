<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );



class FstsViewFuser extends JViewLegacy
{

	function display($tpl = null)
	{
		if (JRequest::getString('task') == "prods")
			return $this->displayProds();
			
		if (JRequest::getString('task') == "prodsa")
			return $this->displayProdsA();
			
// 
		
		$user 	=& $this->get('Data');
		$isNew		= ($user->id < 1);

		$db	= & JFactory::getDBO();

		$text = $isNew ? JText::_("NEW") : JText::_("EDIT");
		JToolBarHelper::title(   JText::_("USER").': <small><small>[ ' . $text.' ]</small></small>' , 'fst_users');
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		FSTAdminHelper::DoSubToolbar();
		
		if ($isNew)
		{
			$users =& $this->get("Users");
			$this->assignRef('users',JHTML::_('select.genericlist',  $users, 'user_id', 'class="inputbox" size="1" ', 'id', 'name'));
			
			$groups =& $this->get("Groups");
			$this->assignRef('groups',JHTML::_('select.genericlist',  $groups, 'group_id', 'class="inputbox" size="1" ', 'id', 'name'));

			$this->assignRef('type',  JHTML::_('select.booleanlist', 'type', 
				array('class' => "inputbox",
							'size' => "1", 
							'onclick' => "DoAllTypeChange();"),0, 'Group', 'User' ) );
				
			if (count($users) == 0)
			{
				$this->assign('showtypes',0);
				$this->assign('showusers',0);
				$this->assign('showgroups',1);	
			} else if (count($groups) == 0) {
				$this->assign('showtypes',0);
				$this->assign('showusers',1);
				$this->assign('showgroups',0);				
			} else {
				$this->assign('showtypes',1);
				$this->assign('showusers',1);
				$this->assign('showgroups',0);				
			}
				
		} else {
			$input = "<input type='hidden' name='user_id' id='user_id' value='" . $user->user_id . "' />";
			$this->assign('users',$input.$user->name);	
			
			$this->assign('showtypes',0);
			
			if ($user->user_id > 0)
			{
				$this->assign('showusers',1);
				$this->assign('showgroups',0);
			} else {
				$this->assign('showusers',0);
				$this->assign('showgroups',1);
			}
			
			//$input = "<input type='hidden' name='group_id' id='group_id' value='" . $user->group_id . "' />";
			//$this->assign('groups',$input.$user->groupname);
			$this->groups = "";	
		}


		$artperms = array();
        $artperms[] = JHTML::_('select.option', '0', JText::_("ART_NONE"), 'id', 'title');
        $artperms[] = JHTML::_('select.option', '1', JText::_("AUTHOR"), 'id', 'title');
        $artperms[] = JHTML::_('select.option', '2', JText::_("EDITOR"), 'id', 'title');
        $artperms[] = JHTML::_('select.option', '3', JText::_("PUBLISHER"), 'id', 'title');
        $this->assign('artperms',JHTML::_('select.genericlist',  $artperms, 'artperm', 'class="inputbox" size="1"', 'id', 'title', $user->artperm));

// 




		$this->assignRef('user', $user);

		parent::display($tpl);
	}
	
	function displayProds()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_prod as u LEFT JOIN #__fst_prod as p ON u.prod_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$products = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('products',$products);
		parent::display();
	}
	
	function displayProdsA()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_prod_a as u LEFT JOIN #__fst_prod as p ON u.prod_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$products = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('products',$products);
		parent::display();
	}
	
	function displayDepts()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_dept as u LEFT JOIN #__fst_ticket_dept as p ON u.ticket_dept_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$departments = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('departments',$departments);
		parent::display();
	}
		
	function displayDeptsA()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_dept_a as u LEFT JOIN #__fst_ticket_dept as p ON u.ticket_dept_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$departments = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('departments',$departments);
		parent::display();
	}
	
	function displayCats()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_cat as u LEFT JOIN #__fst_ticket_cat as p ON u.ticket_cat_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$catogries = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('catogries',$catogries);
		parent::display();
	}
	
	function displayCatsA()
	{
		$user_id = JRequest::getInt('user_id',0);
		$db	= & JFactory::getDBO();

		$query = "SELECT * FROM #__fst_user_cat_a as u LEFT JOIN #__fst_ticket_cat as p ON u.ticket_cat_id = p.id WHERE u.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$catogries = $db->loadObjectList();
		
		$query = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($query);
		$userpermissions = $db->loadObject();
		
		$jid = $userpermissions->user_id;
		
		$query = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $jid)."'";
		$db->setQuery($query);
		$joomlauser = $db->loadObject();
		
		$this->assignRef('userpermissions',$userpermissions);
		$this->assignRef('joomlauser',$joomlauser);
		$this->assignRef('catogries',$catogries);
		parent::display();
	}
}


