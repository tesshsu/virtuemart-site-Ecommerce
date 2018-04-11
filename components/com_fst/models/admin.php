<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'paginationjs.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'tickethelper.php');


class FstModelAdmin extends JModelLegacy
{
	function getTests()
	{
		$query = "SELECT t.id, t.prod_id, t.title, t.body, t.email, t.name, t.website, t.added, p.title as ptitle FROM #__fst_test as t LEFT JOIN #__fst_prod as p ON t.prod_id = p.id WHERE t.published = 0 ORDER BY added LIMIT 10";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return $rows;
	}	
	
	function getTestCount()
	{
		$query = "SELECT count(*) as cnt FROM #__fst_test WHERE published = 0";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows['cnt'];
	}
	
	function getKbcomms()
	{
		$query = "SELECT c.id, c.name, c.email, c.website, c.body, c.created, a.title FROM #__fst_kb_comment as c LEFT JOIN #__fst_kb_art as a ON c.kb_art_id = a.id WHERE c.published = 0 ORDER BY created LIMIT 10";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return $rows;
	}
	
	function getKbcommcount()
	{
		/*$query = "SELECT count(*) as cnt FROM #__fst_kb_comment WHERE published = 0";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows['cnt'];*/
		return 0;
	}
	
	function &getTickets()
	{
		FST_Ticket_Helper::getAdminPermissions();
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit_ticket', 'limit', FST_Helper::getUserSetting('per_page'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$db = JFactory::getDBO();
		if (empty($this->_tickets))
		{
			$query = "SELECT t.*, s.title as status, s.color, u.name, au.name as assigned, u.email as useremail, u.username as username, au.email as handleremail, au.username as handlerusername, ";
			$query .= " dept.title as department, cat.title as category, prod.title as product, pri.title as priority, pri.color as pricolor, ";
			$query .= " grp.groupname as groupname, grp.id as group_id ";
			$query .= " , pri.translation as ptl, dept.translation as dtr, s.translation as str, cat.translation as ctr, prod.translation as prtr";
			$query .= " FROM #__fst_ticket_ticket as t ";
			$query .= " LEFT JOIN #__fst_ticket_status as s ON t.ticket_status_id = s.id ";
			$query .= " LEFT JOIN #__users as u ON t.user_id = u.id ";
			$query .= " LEFT JOIN #__fst_user as a ON t.admin_id = a.id ";
			$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
			$query .= " LEFT JOIN #__fst_ticket_dept as dept ON t.ticket_dept_id = dept.id ";
			$query .= " LEFT JOIN #__fst_ticket_cat as cat ON t.ticket_cat_id = cat.id ";
			$query .= " LEFT JOIN #__fst_prod as prod ON t.prod_id = prod.id ";
			$query .= " LEFT JOIN #__fst_ticket_pri as pri ON t.ticket_pri_id = pri.id ";
			$query .= " LEFT JOIN (SELECT group_id, user_id FROM #__fst_ticket_group_members GROUP BY user_id) as mem ON t.user_id = mem.user_id ";
			$query .= " LEFT JOIN #__fst_ticket_group as grp ON grp.id = mem.group_id ";
			
			$def_open = FST_Ticket_Helper::GetStatusID('def_open');
			
			$tickets = JRequest::getVar('tickets',$def_open);
			/*if ($tickets == 'open')
				$query .= " WHERE (ticket_status_id = 1) ";
			elseif  ($tickets == 'follow')
				$query .= " WHERE (ticket_status_id = 2) ";
			elseif ($tickets == 'closed')
				$query .= " WHERE (ticket_status_id = 3) ";
			elseif ($tickets == 'reply')
				$query .= " WHERE (ticket_status_id = 4) ";
			else*/
			if ($tickets == "open")
			{
				$open = FST_Ticket_Helper::GetStatusIDs("def_open");
				// tickets that arent closed
				$query .= " WHERE ticket_status_id IN ( " . implode(", ", $open) . ") ";			
			} else if ($tickets == 'allopen')
			{
				$allopen = FST_Ticket_Helper::GetStatusIDs("is_closed", true);
				// tickets that arent closed
				$query .= " WHERE ticket_status_id IN ( " . implode(", ", $allopen) . ") ";
			}
			elseif ($tickets == 'closed')
			{
				$allopen = FST_Ticket_Helper::GetStatusIDs("is_closed");
				// remove the archived tickets from the list to deal with
				
				$def_archive = FST_Ticket_Helper::GetStatusID('def_archive');
				foreach ($allopen as $offset => $value)
					if ($value == $def_archive)
						unset($allopen[$offset]);

				// tickets that are closed
				$query .= " WHERE ticket_status_id IN ( " . implode(", ", $allopen) . ") ";
			}
			elseif ($tickets == 'all')
			{
				// need all tickets that arent archived
				$allopen = FST_Ticket_Helper::GetStatusIDs("def_archive", true);
				$query .= " WHERE ticket_status_id IN ( " . implode(", ", $allopen) . " ) ";
			}
			elseif ($tickets == 'archived')
			{
				// need all tickets that arent archived
				$allopen = FST_Ticket_Helper::GetStatusIDs("def_archive");
				$query .= " WHERE ticket_status_id IN ( " . implode(", ", $allopen) . " ) ";
			}
			else
			{
				$query .= " WHERE ticket_status_id = " . (int)FSTJ3Helper::getEscaped($db, $tickets);
			}
		
			//echo $query. "<br>";
			
			$query .= FST_Ticket_Helper::$_perm_where;

			$order = array();
			if (FST_Helper::getUserSetting("group_products"))
				$order[] = "prod.ordering";
				
			if (FST_Helper::getUserSetting("group_departments"))
				$order[] = "dept.title";
				
			if (FST_Helper::getUserSetting("group_cats"))
				$order[] = "cat.title";
				
			if (FST_Helper::getUserSetting("group_pri"))
				$order[] = "pri.ordering DESC";
				
			if (FST_Helper::getUserSetting("group_group"))
			{
				$order[] = "case when grp.groupname is null then 1 else 0 end";
				$order[] = "grp.groupname";
			}
				
			$order[] = "lastupdate DESC";
			$query .= " ORDER BY " . implode(", ", $order);

			$db->setQuery($query);
			$db->query();
			$this->_ticketcount = $db->getNumRows();
					
			//echo $query . "<br>";
					
			$db->setQuery($query, $limitstart, $limit);
			$this->_tickets = $db->loadAssocList('id');
			
		}

		
		$result['pagination'] = new JPaginationEx($this->_ticketcount, $limitstart, $limit );
		$result['count'] = &$this->_ticketcount;
		$result['tickets'] = &$this->_tickets;
		return $result;   		
	}
	
	function &getTicketSearch()
	{
		FST_Ticket_Helper::getAdminPermissions();
		
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit_ticket', 'limit', FST_Helper::getUserSetting('per_page'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$db = JFactory::getDBO();
		
		if (empty($this->_tickets))
		{
			$query = "SELECT t.*, s.title as status, s.color, u.name, au.name as assigned, u.email as useremail, u.username as username, au.email as handleremail, au.username as handlerusername, ";
			$query .= " dept.title as department, cat.title as category, prod.title as product, pri.title as priority, pri.color as pricolor, ";
			$query .= " grp.groupname as groupname, grp.id as group_id ";
			$query .= " , pri.translation as ptl, dept.translation as dtr, s.translation as str, cat.translation as ctr, prod.translation as prtr";
			$query .= " FROM #__fst_ticket_ticket as t ";
			$query .= " LEFT JOIN #__fst_ticket_status as s ON t.ticket_status_id = s.id ";
			$query .= " LEFT JOIN #__users as u ON t.user_id = u.id ";
			$query .= " LEFT JOIN #__fst_user as a ON t.admin_id = a.id ";
			$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
			$query .= " LEFT JOIN #__fst_ticket_dept as dept ON t.ticket_dept_id = dept.id ";
			$query .= " LEFT JOIN #__fst_ticket_cat as cat ON t.ticket_cat_id = cat.id ";
			$query .= " LEFT JOIN #__fst_prod as prod ON t.prod_id = prod.id ";
			$query .= " LEFT JOIN #__fst_ticket_pri as pri ON t.ticket_pri_id = pri.id ";
			$query .= " LEFT JOIN (SELECT group_id, user_id FROM #__fst_ticket_group_members GROUP BY user_id) as mem ON t.user_id = mem.user_id ";
			$query .= " LEFT JOIN #__fst_ticket_group as grp ON grp.id = mem.group_id ";

			$searchtype = JRequest::getVar('searchtype','basic');
			$ticketids = array();
			$ticketids[0] = 0;
			$ticketid_matchall = 0;

			$tags = JRequest::getVar('tags','');
			$tags = trim($tags,';');
			if ($tags)
			{
				$tags_ = explode(";",$tags);
				$tags = array();
				foreach($tags_ as $tag)
				{
					if ($tag)
						$tags[$tag] = $tag;
				}

				if (count($tags) > 0)
				{
					foreach($tags as $tag)
					{
						$ticketid_matchall++;
						$qry = "SELECT ticket_id FROM #__fst_ticket_tags WHERE tag = '".FSTJ3Helper::getEscaped($db, $tag)."'";
						$db->setQuery($qry);
						//echo $qry."<br>";
						$rows = $db->loadAssocList("ticket_id");
						foreach($rows as $row)
						{
							$ticketid =	$row['ticket_id'];
							if (array_key_exists($ticketid,$ticketids))
							{
								$ticketids[$ticketid]++;
							} else {
								$ticketids[$ticketid] = 1;
							}
						}
						
					}	
				}
			}

			if ($searchtype == "basic")
			{
				$search = JRequest::getVar('search','');
				$wherebits = array();
				
				// store tag match ids in separate array, as we want to AND them, not OR
				$tagids = $ticketids;
				$ticketids = array();
				$ticketids[0] = 0;

				if ($search != "")
				{
					$wherebits[] = " t.title LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
					$wherebits[] = " t.reference = '".FSTJ3Helper::getEscaped($db, $search)."' ";
			
					// search custom fields that are set to be searched
					$fields = FSTCF::GetAllCustomFields(true);
					/*echo "<pre>";
					print_r($fields);
					echo "</pre>";*/
					foreach ($fields as $field)
					{
						if (!$field['basicsearch']) continue;

						$ticketid_matchall++;
						
						$fieldid = $field['id'];
						$qry = "SELECT ticket_id FROM #__fst_ticket_field WHERE field_id = '" . FSTJ3Helper::getEscaped($db, $fieldid) . "' AND value LIKE '%" . FSTJ3Helper::getEscaped($db, $search) . "%'";
						$db->setQuery($qry);	
						//echo $qry."<br>";
						$moreids = $db->loadAssoclist();
						//print_r($moreids);
						foreach($moreids as $row)
						{
							if (array_key_exists($row['ticket_id'],$ticketids))
							{
								$ticketids[$row['ticket_id']]++;
							} else {
								$ticketids[$row['ticket_id']] = 1;
							}
						}				
					}

					// basic search optional fields
					if (FST_Settings::get('support_basic_name'))
					{
						$wherebits[] = " u.name LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
						$wherebits[] = " unregname LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
					}

					if (FST_Settings::get('support_basic_username'))
					{
						$wherebits[] = " u.username LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
					}

					if (FST_Settings::get('support_basic_email'))
					{
						$wherebits[] = " u.email LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
						$wherebits[] = " t.email LIKE '%".FSTJ3Helper::getEscaped($db, $search)."%' ";
					}

					if (FST_Settings::get('support_basic_messages'))
					{
						$ticketid_matchall++;
						
						$fieldid = $field['id'];
						$qry = "SELECT ticket_ticket_id as ticket_id FROM #__fst_ticket_messages WHERE subject LIKE '%" . FSTJ3Helper::getEscaped($db, $search) . "%' OR body LIKE '%" . FSTJ3Helper::getEscaped($db, $search) . "%'";
						$db->setQuery($qry);	
						//echo $qry."<br>";
						$moreids = $db->loadAssoclist();
						//print_r($moreids);
						foreach($moreids as $row)
						{
							if (array_key_exists($row['ticket_id'],$ticketids))
							{
								$ticketids[$row['ticket_id']]++;
							} else {
								$ticketids[$row['ticket_id']] = 1;
							}
						}				
					}
				}


				if (count($ticketids) > 1)
				{
					$tids = array();
					foreach($ticketids as $id => $rec)
					{
						$tids[] = $id;
					}	
					$ticketids = $tids;
					unset($tids);
				}

				if (count($ticketids) > 1)
					$wherebits[] = "t.id IN (".implode(",",$ticketids).")";
				
				if (count($wherebits) == 0)
					$wherebits[] = "1";

				$query .= " WHERE (" . implode(" OR ", $wherebits) . ")";

				// add ticket tag ids
				if (count($tagids) > 1)
				{
					$tids = array();
					foreach($tagids as $id => $rec)
					{
						$tids[] = $id;
					}	
					$tagids = $tids;
					unset($tids);
					$query .= " AND t.id IN (".implode(",",$tagids).")";
				}

				//echo $query . "<br>";
			} else if ($searchtype == "advanced")
			{
				$search = JRequest::getVar('search','');
				$wherebits = array();
			
				$subject = JRequest::getVar('subject','');
				if ($subject)
					$wherebits[] = " t.title LIKE '%".FSTJ3Helper::getEscaped($db, $subject)."%' ";
			
				$reference = JRequest::getVar('reference','');
				if ($reference)
					$wherebits[] = " t.reference = '".FSTJ3Helper::getEscaped($db, $reference)."' ";
			
				$username = JRequest::getVar('username','');
				if ($username)
					$wherebits[] = " u.username LIKE '%".FSTJ3Helper::getEscaped($db, $username)."%' ";
			
				$useremail = JRequest::getVar('useremail','');
				if ($useremail)
					$wherebits[] = " ( u.email LIKE '%".FSTJ3Helper::getEscaped($db, $useremail)."%' OR t.email LIKE '%".FSTJ3Helper::getEscaped($db, $useremail)."%' ) ";
			
				$userfullname = JRequest::getVar('userfullname','');
				if ($userfullname)
					$wherebits[] = " ( u.name LIKE '%".FSTJ3Helper::getEscaped($db, $userfullname)."%' OR unregname LIKE '%".FSTJ3Helper::getEscaped($db, $userfullname)."%' ) ";
			
				$content = JRequest::getVar('content','');
				if ($content)
				{
					$q = " t.id IN ";
					$q .= "( SELECT ticket_ticket_id FROM #__fst_ticket_messages WHERE body LIKE '%".FSTJ3Helper::getEscaped($db, $content)."%' )";
					$wherebits[] = $q;
				}
			
				$handler = JRequest::getVar('handler','');
				if ($handler)
				{
					if ($handler == -1 || $handler == -2) // my tickets
					{						
						// need to find my handler id
						$user = JFactory::getUser();
						$qry = "SELECT * FROM #__fst_user WHERE user_id = '" . FSTJ3Helper::getEscaped($db, $user->id)."'";
						$db->setQuery($qry);
						$fstuser = $db->loadObject();
						
						if ($handler == -1)
						{
							$wherebits[] = " t.admin_id = '".FSTJ3Helper::getEscaped($db, $fstuser->id)."' ";
						} else {
							$wherebits[] = " t.admin_id != '".FSTJ3Helper::getEscaped($db, $fstuser->id)."' ";
							$wherebits[] = " t.admin_id != 0 ";
						}
					} else if ($handler == -3) // unassigned
					{
						$wherebits[] = " t.admin_id = 0" ;
					} else // handler
					{
						$wherebits[] = " t.admin_id = '".FSTJ3Helper::getEscaped($db, $handler)."' ";
					} 
				}
			
				$status = JRequest::getVar('status','');
				if ($status)
					$wherebits[] = " t.ticket_status_id = '".FSTJ3Helper::getEscaped($db, $status)."' ";
			
				$product = JRequest::getVar('product','');
				if ($product)
					$wherebits[] = " t.prod_id = '".FSTJ3Helper::getEscaped($db, $product)."' ";
			
				$department = JRequest::getVar('department','');
				if ($department)
					$wherebits[] = " t.ticket_dept_id = '".FSTJ3Helper::getEscaped($db, $department)."' ";
			
				$cat = JRequest::getVar('cat','');
				if ($cat)
					$wherebits[] = " t.ticket_cat_id = '".FSTJ3Helper::getEscaped($db, $cat)."' ";
			
				$pri = JRequest::getVar('priority','');
				if ($pri)
					$wherebits[] = " t.ticket_pri_id = '".FSTJ3Helper::getEscaped($db, $pri)."' ";
				
				$group = JRequest::getVar('group','');
				if ($group > 0)
				{
					$wherebits[] = " t.user_id IN (SELECT user_id FROM #__fst_ticket_group_members WHERE group_id = '".FSTJ3Helper::getEscaped($db, $group)."' GROUP BY user_id)";
				}
		
				$date_from = $this->DateValidate(JRequest::getVar('date_from',''));
				$date_to = $this->DateValidate(JRequest::getVar('date_to',''));
				
				/*if ($date_from && $date_to)
				{
					// got both date, need a ticket with 
				} else*/if ($date_from)
				{
					$wherebits[] = " t.lastupdate > DATE_SUB('".FSTJ3Helper::getEscaped($db, $date_from)."',INTERVAL 1 DAY) ";
				} /*else*/if ($date_to)
				{
					$wherebits[] = " t.opened < DATE_ADD('".FSTJ3Helper::getEscaped($db, $date_to)."',INTERVAL 1 DAY) ";
				}

				// search custom fields that are set to be searched
				$fields = FSTCF::GetAllCustomFields(true);
				/*echo "<pre>";
				print_r($fields);
				echo "</pre>";*/
				foreach ($fields as $field)
				{
					if (!$field['advancedsearch']) continue;

					$search = JRequest::getVar('custom_' . $field['id'],"");
					//echo "Field : {$field['id']} = $search<br>";
					if ($search != "")
					{
						$ticketid_matchall++;
						
						$fieldid = $field['id'];
						if ($field['type'] == "checkbox")
						{
							if ($search == "1")
								$qry = "SELECT ticket_id FROM #__fst_ticket_field WHERE field_id = '" . FSTJ3Helper::getEscaped($db, $fieldid) . "' AND value = 'on'";
							else
								$qry = "SELECT ticket_id FROM #__fst_ticket_field WHERE field_id = '" . FSTJ3Helper::getEscaped($db, $fieldid) . "' AND value = ''";
						} elseif ($field['type'] == "radio" || $field['type'] == "combo")
						{
							$qry = "SELECT ticket_id FROM #__fst_ticket_field WHERE field_id = '" . FSTJ3Helper::getEscaped($db, $fieldid) . "' AND value = '" . FSTJ3Helper::getEscaped($db, $search) . "'";
						} else {
							$qry = "SELECT ticket_id FROM #__fst_ticket_field WHERE field_id = '" . FSTJ3Helper::getEscaped($db, $fieldid) . "' AND value LIKE '%" . FSTJ3Helper::getEscaped($db, $search) . "%'";
						}
						$db->setQuery($qry);	
						//echo $qry."<br>";
						$moreids = $db->loadAssoclist();
						//print_r($moreids);
						foreach($moreids as $row)
						{
							if (array_key_exists($row['ticket_id'],$ticketids))
							{
								$ticketids[$row['ticket_id']]++;
							} else {
								$ticketids[$row['ticket_id']] = 1;
							}
						}	
					}			
				}	
				
				if ($ticketid_matchall > 0)
				{
					unset($ticketids[0]);
					$tids = array();
					if (count($ticketids) > 0)
					{
						foreach($ticketids as $id => $rec)
						{
							if ($id == 0)
								continue;
							if ($rec == $ticketid_matchall)
								$tids[] = $id;
						}	
						$ticketids = $tids;
						unset($tids);
					}

					if (count($ticketids) > 0)
						$wherebits[] = "t.id IN (".implode(",",$ticketids).")";
					else
						$wherebits[] = "0";
				}


				if (count($wherebits) == 0)
					$wherebits[] = "1";
			
				$query .= " WHERE " . implode(" AND ", $wherebits);
			} else {
				$query .= " WHERE 1 ";
			}
			
			$query .= FST_Ticket_Helper::$_perm_where;


			$order = array();
			if (FST_Helper::getUserSetting("group_products"))
				$order[] = "prod.ordering";
				
			if (FST_Helper::getUserSetting("group_departments"))
				$order[] = "dept.title";
				
			if (FST_Helper::getUserSetting("group_cats"))
				$order[] = "cat.title";
				
			if (FST_Helper::getUserSetting("group_pri"))
				$order[] = "pri.ordering DESC";
				
			if (FST_Helper::getUserSetting("group_group"))
			{
				$order[] = "case when grp.groupname is null then 1 else 0 end";
				$order[] = "grp.groupname";
			}
				
			$order[] = "lastupdate DESC";
			$query .= " ORDER BY " . implode(", ", $order);
		
			//echo "<br>$query<br>";
			$db->setQuery($query);
			$db->query();
			$this->_ticketcount = $db->getNumRows();
					
			$db->setQuery($query, $limitstart, $limit);
			$this->_tickets = $db->loadAssocList('id');
		}
		/*echo "<pre>";
		print_r($result['tickets']);
		echo "</pre>";*/

		$result['pagination'] = new JPaginationJs($this->_ticketcount, $limitstart, $limit );
		$result['count'] = &$this->_ticketcount;
		$result['tickets'] = &$this->_tickets;
		return $result;   		
	}
	
	function DateValidate($in_date)
	{
		//echo "Checking $in_date<br>";
		$time = strtotime($in_date);
		//echo "Time : $time<br>";
		
		if ($time > 0)
		{
			return date("Y-m-d",$time);	
		}
		return "";	
	}
	
	function &getTicket($ticketid)
	{
		$db = JFactory::getDBO();
		
		
		$query = "SELECT t.*, u.name, u.username, p.title as product, d.title as dept, c.title as cat, s.title as status, ";
		$query .= "s.color as scolor, s.id as sid, pr.title as pri, pr.color as pcolor, pr.id as pid";
		$query .= " , pr.translation as ptl, d.translation as dtr, s.translation as str, c.translation as ctr, p.translation as prtr";
		$query .= " FROM #__fst_ticket_ticket as t ";
		$query .= " LEFT JOIN #__users as u ON t.user_id = u.id ";
		$query .= " LEFT JOIN #__fst_prod as p ON t.prod_id = p.id ";
		$query .= " LEFT JOIN #__fst_ticket_dept as d ON t.ticket_dept_id = d.id ";
		$query .= " LEFT JOIN #__fst_ticket_cat as c ON t.ticket_cat_id = c.id ";
		$query .= " LEFT JOIN #__fst_ticket_status as s ON t.ticket_status_id = s.id ";
		$query .= " LEFT JOIN #__fst_ticket_pri as pr ON t.ticket_pri_id = pr.id ";
		$query .= " WHERE t.id = '".FSTJ3Helper::getEscaped($db, $ticketid)."' ";

		FST_Ticket_Helper::getAdminPermissions();
		$query .= FST_Ticket_Helper::$_perm_where;

		//echo $query . "<br>";
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		
		return $rows;   		
	}
	
	function &getMessages($ticketid)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT m.*, u.name FROM #__fst_ticket_messages as m LEFT JOIN #__users as u ON m.user_id = u.id WHERE ticket_ticket_id = '".FSTJ3Helper::getEscaped($db, $ticketid)."'";
		
		if (FST_Helper::getUserSetting("reverse_order"))
		{
			$query .= " ORDER BY posted ASC";
		} else {
			$query .= " ORDER BY posted DESC";
		}

		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return $rows;   		
	}
	
	function &getMessage($messageid)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT m.* FROM #__fst_ticket_messages as m WHERE m.id = '".FSTJ3Helper::getEscaped($db, $messageid)."' ORDER BY posted DESC";

		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows;   		
	}
	
	function &getAttach($ticketid)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT a.*, u.name FROM #__fst_ticket_attach as a LEFT JOIN #__users as u ON a.user_id = u.id WHERE ticket_ticket_id = '".FSTJ3Helper::getEscaped($db, $ticketid)."' ORDER BY added DESC";

		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return $rows;   		
	}
	
	function &getUsersGroups($user_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT g.* FROM #__fst_ticket_group_members as m LEFT JOIN #__fst_ticket_group as g ON m.group_id = g.id WHERE m.user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";

		$db->setQuery($query);
		$rows = $db->loadAssocList();
		return $rows;   		
	}
	
	/*function &getPriority($priid)
	{
	       $db = JFactory::getDBO();
	       
	       $query = "SELECT * FROM #__fst_ticket_pri WHERE id = '$priid'";

	       $db->setQuery($query);
	       $rows = $db->loadAssoc();
	       return $rows;   		
		
	}*/
	
	/*function &getCategory($catid)
	{
	       $db = JFactory::getDBO();
	       
	       $query = "SELECT * FROM #__fst_ticket_cat WHERE id = '$catid'";

	       $db->setQuery($query);
	       $rows = $db->loadAssoc();
	       return $rows;   		
		
	}*/
	
	/*function &getProduct()
	{
	    $db = JFactory::getDBO();
	    $prodid = JRequest::getVar('prodid','');
	    $query = "SELECT * FROM #__fst_prod WHERE id = '$prodid'";

	    $db->setQuery($query);
	    $rows = $db->loadAssoc();
	    return $rows;        
	} */
	
	function &getPriorities()
	{
		if (empty($this->_priorities))
		{
			$db = JFactory::getDBO();
		
			$query = "SELECT * FROM #__fst_ticket_pri ORDER BY id ASC";

			$db->setQuery($query);
			$this->_priorities = $db->loadAssocList('id');
		}
		return $this->_priorities;   			
	}
	
	
	function &getPriority($priid)
	{
		if (empty($this->_priorities))
		{
			$this->getPriorities();
		}

		return $this->_priorities[$priid];
	}
	
	function &getStatuss()
	{
		if (empty($this->_statuss))
		{
			$db = JFactory::getDBO();
		
			$query = "SELECT * FROM #__fst_ticket_status ORDER BY ordering";

			$db->setQuery($query);
			$this->_statuss = $db->loadAssocList('id');
		}
		return $this->_statuss;   		
	}	
	
	function &getStatus($statusid)
	{
		if (empty($this->_statuss))
		{
			$this->getStatuss();
		}

		return $this->_statuss[$statusid];
	}
	
	function &getTicketCount()
	{
		return FST_Ticket_Helper::getTicketCount();
	}
	
	/*function &getDepartment()
	{
	    $db = JFactory::getDBO();
	    $deptid = JRequest::getVar('deptid','');
	    $query = "SELECT * FROM #__fst_ticket_dept WHERE id = '$deptid'";

	    $db->setQuery($query);
	    $rows = $db->loadAssoc();
	    return $rows;        
	}*/ 
	
	function &getAdminUser($adminid)
	{
		$db = JFactory::getDBO();
		
		$query = " SELECT a.*, au.name, au.username FROM #__fst_user as a ";
		$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
		$query .= " WHERE a.id = '".FSTJ3Helper::getEscaped($db, $adminid)."'";
		
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows;   		
	}
	
	function getUser($user_id)
	{
		$db = JFactory::getDBO();

		$query = " SELECT * FROM #__users ";
		$query .= " WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		return $rows;   		
	}

	function getAdminUsers()
	{
		if (empty( $this->_adminusers )) 
		{
			$query = "SELECT u.id as admin_id, m.id, CONCAT(m.username,' (',m.name,')') as name, m.email FROM #__users as m ";
			$query .= " LEFT JOIN #__fst_user as u ON m.id = u.user_id ";
			$query .= " WHERE u.id > 0 AND u.support = 1 ";
			$query .= " ORDER BY m.username";
			
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_adminusers = $db->loadAssocList();
		}
		return $this->_adminusers;
	}
	
	function getDepartments()
	{
		if (empty( $this->_depts )) 
		{
			$query = "SELECT * FROM #__fst_ticket_dept ORDER BY title";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_depts = $db->loadAssocList();
		}
		return $this->_depts;
	}
		
	function GetDepartment($dept_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fst_ticket_dept WHERE id = '".FSTJ3Helper::getEscaped($db, $dept_id)."'";
        $db->setQuery($qry);
		$rec = $db->loadObject();
		return $rec->title;	
	}
		
	function GetProduct($prod_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fst_prod WHERE id = '".FSTJ3Helper::getEscaped($db, $prod_id)."'";
        $db->setQuery($qry);
		$rec = $db->loadObject();
		return $rec->title;	
	}

	function getProducts()
	{
		if (empty( $this->_prods )) 
		{
			$query = "SELECT * FROM #__fst_prod ORDER BY title";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_prods = $db->loadAssocList();
		}
		return $this->_prods;
	}

	function getGroups()
	{
		if (empty( $this->_groups )) 
		{
			$query = "SELECT * FROM #__fst_ticket_group ORDER BY groupname";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_groups = $db->loadAssocList();
		}
		return $this->_groups;
	}
	
	function getCats()
	{
		if (empty( $this->_cats )) 
		{
			$query = "SELECT * FROM #__fst_ticket_cat ORDER BY title";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_cats = $db->loadAssocList();
		}
		return $this->_cats;
	}
	
	function getHandlers()
	{
		if (empty( $this->_handlers )) 
		{
			$query = "SELECT au.id, u.name, u.id as user_id, u.username, u.email FROM #__fst_user as au LEFT JOIN #__users as u ON au.user_id = u.id WHERE support = 1";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$this->_handlers = $db->loadAssocList();
		}
		return $this->_handlers;
	}

	function getTags($ticketid)
	{
		if (empty( $this->_tags )) 
		{
			$db = JFactory::getDBO();
			$query = "SELECT tag FROM #__fst_ticket_tags WHERE ticket_id = '".FSTJ3Helper::getEscaped($db, $ticketid)."'";
			$db->setQuery($query);
			$this->_tags = $db->loadAssocList();
		}
		return $this->_tags;	
	}

	function getAllTags()
	{
		if (empty( $this->_alltags )) 
		{ 
			$db = JFactory::getDBO();
			$query = "SELECT count(*) as cnt, tag FROM #__fst_ticket_tags GROUP BY tag ORDER BY cnt DESC LIMIT 10";
			$db->setQuery($query);
			$this->_alltags = $db->loadAssocList();
		}
		return $this->_alltags;	
	}

	function getTagsPerTicket()
	{
		$ticketids = array();

		if (empty($this->_tickets) || count($this->_tickets) == 0)
			return;

		foreach($this->_tickets as &$ticket)
		{
			$ticketids[] = $ticket['id'];
		}

		$qry = "SELECT * FROM #__fst_ticket_tags WHERE ticket_id IN (" . implode(",",$ticketids) . ")";
		$db = JFactory::getDBO();
		$db->setQuery($qry);
		$tags = $db->loadAssocList();

		if (count($tags) > 0)
		{
			foreach($tags as $tag)
			{
				$this->_tickets[$tag['ticket_id']]['tags'][] = $tag;	
			}
		}
	}

	function getMessageCounts()
	{
		$ticketids = array();

		if (empty($this->_tickets) || count($this->_tickets) == 0)
			return;

		$db = JFactory::getDBO();
		
		foreach($this->_tickets as &$ticket)
		{
			$ticketids[] = FSTJ3Helper::getEscaped($db, $ticket['id']);
			$ticket['msgcount'] = array();
			$ticket['msgcount'][0] = 0;
			$ticket['msgcount'][1] = 0;
			$ticket['msgcount']['total'] = 0;
			
		}

		$qry = "SELECT ticket_ticket_id, admin, count(*) as msgcnt FROM #__fst_ticket_messages WHERE ticket_ticket_id IN (" . implode(",",$ticketids) . ") GROUP BY ticket_ticket_id, admin";
		$db->setQuery($qry);
		//echo $qry."<br>";
		$tags = $db->loadAssocList();
		if (count($tags) > 0)
		{
			foreach($tags as $tag)
			{
				$this->_tickets[$tag['ticket_ticket_id']]['msgcount'][$tag['admin']] = $tag['msgcnt'];
				if ($tag['admin'] < 2)
				{
					if (array_key_exists('total',$this->_tickets[$tag['ticket_ticket_id']]['msgcount']))
					{
						$this->_tickets[$tag['ticket_ticket_id']]['msgcount']['total'] += $tag['msgcnt'];	
					} else {
						$this->_tickets[$tag['ticket_ticket_id']]['msgcount']['total'] = 1;	
					}
				}
			}	
		}	
	}

	function getAttachPerTicket()
	{
		$ticketids = array();
		
		if (empty($this->_tickets) || count($this->_tickets) == 0)
			return;

		$db = JFactory::getDBO();
		
		foreach($this->_tickets as &$ticket)
		{
			$ticketids[] = FSTJ3Helper::getEscaped($db, $ticket['id']);
		}

		$qry = "SELECT * FROM #__fst_ticket_attach WHERE ticket_ticket_id IN (" . implode(",",$ticketids) . ")";
		$db->setQuery($qry);
		$tags = $db->loadAssocList();
		if (count($tags) > 0)
		{
			foreach($tags as $tag)
			{
				$this->_tickets[$tag['ticket_ticket_id']]['attach'][] = $tag;	
			}	
		}	
	}

	function getGroupsPerTicket()
	{
		$user_ids = array();
		
		if (empty($this->_tickets) || count($this->_tickets) == 0)
			return;

		$db = JFactory::getDBO();
		
		foreach($this->_tickets as &$ticket)
		{
			$ticket['groups'] = array();
			if ($ticket['user_id'] > 0)
				$user_ids[] = FSTJ3Helper::getEscaped($db, $ticket['user_id']);
		}

		if (count($user_ids) == 0)
			return;

		$qry = "SELECT m.user_id, g.groupname FROM #__fst_ticket_group_members as m LEFT JOIN #__fst_ticket_group as g ON m.group_id = g.id WHERE m.user_id IN (" . implode(",",$user_ids) . ")";
		$db->setQuery($qry);
		$tags = $db->loadAssocList();
		
		foreach($this->_tickets as &$ticket)
		{
			$user_id = $ticket['user_id'];
			if ($user_id == 0) continue;
			
			foreach ($tags as $tag)
			{
				if ($tag['user_id'] == $user_id)
					$ticket['groups'][] = $tag['groupname'];		
			}	
		}
	}

	function GetUserNameFromFSTUID($user_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fst_user WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
        $db->setQuery($qry);
		$rec = $db->loadObject();
		if (!$rec)
		{
			$qry = "SELECT * FROM #__fst_user WHERE user_id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
			$db->setQuery($qry);
			$rec = $db->loadObject();
		}
		$user = JFactory::getUser($rec->user_id);
		return $user->name . " (" . $user->username . ")";
	}

	function getAnnouncements()
	{
		// get a list of announcements, including pagination and filter
			
		$db = JFactory::getDBO();
		$qry = "SELECT a.id, a.title, a.subtitle, a.published, a.added, u.name, u.username FROM #__fst_announce as a LEFT JOIN #__users as u ON a.author = u.id ";
		
		$qry .= " ORDER BY added DESC";
		$db->setQuery($qry);
		return $db->loadObjectList();
	}

	function getAnnouncement()
	{
		// get a list of announcements, including pagination and filter
		$id = JRequest::getVar('id',0);
		
		$db = JFactory::getDBO();
		$qry = "SELECT a.*, u.name, u.username FROM #__fst_announce as a LEFT JOIN #__users as u ON a.author = u.id ";
		
		$qry .= "WHERE a.id = '".FSTJ3Helper::getEscaped($db, $id)."'";
		
		$db->setQuery($qry);
		return $db->loadObject();
	}
	
	function getArticleCounts()
	{
		if (empty($this->artcounts))
		{
			$this->artcounts = array();
		
			$types = array();
			$types[] = "announce";
			$types[] = "faqs";
			$types[] = "kb";
		
			foreach($types as $type)
			{
				require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'content'.DS.$type.'.php');
				$class = "FST_ContentEdit_$type";
				$content = new $class();
				$this->artcounts[$type] = array();	
				$this->artcounts[$type]['desc'] = $content->descs;
				$this->artcounts[$type]['id'] = $content->id;
				$this->artcounts[$type]['counts'] = $content->GetCounts();
			}
		}
		
		return $this->artcounts;
	}
}

