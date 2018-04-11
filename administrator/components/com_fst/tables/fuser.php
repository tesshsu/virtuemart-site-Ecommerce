<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class TableFuser extends JTable
{
	
	var $id = null;

	var $mod_kb = 0;
	var $mod_test = 0;
	var $support = 0;
	var $user_id = 0;
	var $group_id = 0;
	var $seeownonly = 0;
	var $autoassignexc = 0;
	var $allprods = 0;
	var $allcats = 0;
	var $alldepts = 0;
	var $artperm = 0;
	var $groups = 0;
	var $allprods_a = 0;
	var $allcats_a = 0;
	var $alldepts_a = 0;
	var $assignperms = 0;
	var $reports = 0;
	
	function TableFuser(& $db) {
		parent::__construct('#__fst_user', 'id', $db);
	}
}


