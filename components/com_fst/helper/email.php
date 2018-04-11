<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'fields.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'tickethelper.php');

class FST_EMail
{
	static $debug = 0;
	static $dontsend = 0;
	static $exit_on_debug = 0;
	
	static function Send_Comment($comments)
	{	
		if ($comments->dest_email == "") 
		{
			return;
		}
		
		$mailer = JFactory::getMailer();
		$mailer->setSender(FST_EMail::Get_Sender());
		//$mailer->addRecipient(array($comments->dest_email));
		
		if (strpos($comments->dest_email, ",") !== false)
		{
			$dests = explode(",", $comments->dest_email);
			foreach($dests as $dest)
			{
				$dest = trim($dest);
				if ($dest != "")
					$mailer->addRecipient(array($dest));
			}	
		} else {
			$mailer->addRecipient(array($comments->dest_email));
		}


		$tpl = $comments->handler->EMail_GetTemplate($comments->moderate);
		$template = FST_EMail::Get_Template($tpl);
		
		$data = $comments->comment;
		$data['moderated'] = $comments->moderate;
		if ($data['moderated'] == 0)
			$data['moderated'] = "";
			
		if (!array_key_exists('customfields',$data))
			$data['customfields'] = "";
		if (!array_key_exists('email',$data))
			$data['email'] = "";
		if (!array_key_exists('website',$data))
			$data['website'] = "";
		if (!array_key_exists('linkmod',$data))
			$data['linkmod'] = "";
		if (!array_key_exists('linkart',$data))
			$data['linkart'] = "";
		
		$data['linkmod'] = $comments->GetModLink();
			
		$links = $comments->handler->EMail_AddFields($data);
		$links['linkart'] = 1;
		$links['linkmod'] = 1;
		
		if ($data['moderated'] == 0)
		{
			$data['moderated'] = "";
			$data['linkmod'] = "";
		}
		
		
		if ($template['ishtml'])
		{
			$data['article'] = "<a href='{$data['linkart']}'>{$data['article']}</a>";
			FST_EMail::ProcessLinks($data, $links);
			
			// add custom fields html style
			$customfields = "";
			foreach($comments->customfields as &$field)
				$customfields .= $field['description'] . ": " . $data['custom_' . $field['id']] . "<br />";
			$data['customfields'] = $customfields;
		} else {
			// add custom fields text style
			$customfields = "";
			foreach($comments->customfields as &$field)
				$customfields .= $field['description'] . ": " . $data['custom_' . $field['id']] . "\n";
			$data['customfields'] = $customfields;
		}

		$email = FST_EMail::ParseGeneralTemplate($template, $data);

		$mailer->isHTML($template['ishtml']);
		$mailer->setSubject($email['subject']);
		$mailer->setBody($email['body']);

		$send = FST_EMail::Send($mailer);
		
		FST_EMail::Debug('Send_Comment', 
			array(
				'Comments' => $comments,
				'Data' => $data,
				'EMail' => $email,
				'Mailer' => $mailer,
				'Result' => $send
				));
	}
		
	static function Admin_Reply(&$ticket, $subject, $body)
	{
		// ticket replyd to
		if (FST_Settings::get('support_email_on_reply') == 1)
		{		
			$db = JFactory::getDBO();

			$custid = $ticket['user_id'];
			$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
			$db->setQuery($qry);
			$custrec = $db->loadAssoc();
		
			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
		
			FST_EMail::AddTicketRecpts($mailer, $ticket, $custrec);
		
			$template = FST_EMail::Get_Template('email_on_reply');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);
	
			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('Admin_Reply', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}
	
	static function Admin_Close(&$ticket, $subject, $body)
	{
		// ticket replyd to
		if (FST_Settings::get('support_email_on_close') == 1)
		{		
			$db = JFactory::getDBO();

			$custid = $ticket['user_id'];
			$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
			$db->setQuery($qry);
			$custrec = $db->loadAssoc();
		
			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
		
			FST_EMail::AddTicketRecpts($mailer, $ticket, $custrec);
		
			$template = FST_EMail::Get_Template('email_on_close');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);
		
			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('Admin_Close', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}
	
	static function Admin_AutoClose(&$ticket)
	{
		// ticket replyd to
		$db = JFactory::getDBO();

		$custid = $ticket['user_id'];
		$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
		$db->setQuery($qry);
		$custrec = $db->loadAssoc();
		
		$mailer = JFactory::getMailer();
		$mailer->setSender(FST_EMail::Get_Sender());
		
		FST_EMail::AddTicketRecpts($mailer, $ticket, $custrec);
		
		$template = FST_EMail::Get_Template('email_on_autoclose');
		$email = FST_EMail::ParseTemplate($template,$ticket,$template['subject'],"",$template['ishtml']);
		
		$mailer->isHTML($template['ishtml']);
		$mailer->setSubject($email['subject']);
		$mailer->setBody($email['body']);

		$send = FST_EMail::Send($mailer);
		
		FST_EMail::Debug('Admin_AutoClose', 
			array(
				'Ticket' => $ticket,
				'Mailer' => $mailer,
				'Result' => $send
				));
	}

	static function Admin_Forward(&$ticket, $subject, $body)
	{
		// ticket replyd to
		if (FST_Settings::get('support_email_handler_on_forward') == 1)
		{		
			$db = JFactory::getDBO();

			$admin_id = $ticket['admin_id'];
			$query = " SELECT a.*, au.name, au.username, au.email FROM #__fst_user as a ";
			$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
			$query .= " WHERE a.id = '".FSTJ3Helper::getEscaped($db, $admin_id)."'";
			$db->setQuery($query);
			$admin_rec = $db->loadAssoc();
		
			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
		
			$mailer->addRecipient(array($admin_rec['email']));
		
			$template = FST_EMail::Get_Template('email_handler_on_forward');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);

			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('Admin_Forward', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}

	static function User_Create(&$ticket, $subject, $body)
	{
		// ticket replyd to
		if (FST_Settings::get('support_email_on_create') == 1)
		{		
			$db = JFactory::getDBO();

			$custid = $ticket['user_id'];
			$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
			$db->setQuery($qry);
			$custrec = $db->loadAssoc();
		
			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
		
			$mailer->addRecipient(array($custrec['email']));
		
			$template = FST_EMail::Get_Template('email_on_create');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);

			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('User_Create', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}

	static function User_Create_Unreg(&$ticket, $subject, $body)
	{
		// ticket replyd to
		if (FST_Settings::get('support_email_on_create') == 1)
		{		
			$db = JFactory::getDBO();

			$custid = $ticket['user_id'];
			$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
			$db->setQuery($qry);
			$custrec = $db->loadAssoc();
		
			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
			$mailer->addRecipient(array($ticket['email']));
		
			$template = FST_EMail::Get_Template('email_on_create_unreg');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);

			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('User_Create_Unreg', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}

	static function Admin_Create(&$ticket, $subject, $body)
	{
		// User has created a ticket, EMail to handler
		
		// ticket replyd to
		if (FST_Settings::get('support_email_handler_on_create') == 1)
		{		
			$db = JFactory::getDBO();
		
			$custid = $ticket['user_id'];
			$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $custid)."'";
			$db->setQuery($qry);
			$custrec = $db->loadAssoc();

			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
	
			if (FST_EMail::AdminTo($mailer, $ticket) == 0)
				return;
		
			$template = FST_EMail::Get_Template('email_handler_on_create');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);

			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('Admin_Create', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}

	static function User_Reply(&$ticket, $subject, $body)
	{
		// User replied to a ticket, email admin
		if (FST_Settings::get('support_email_handler_on_reply') == 1)
		{		
			$db = JFactory::getDBO();

			$mailer = JFactory::getMailer();
			$mailer->setSender(FST_EMail::Get_Sender());
			
			if (FST_EMail::AdminTo($mailer, $ticket) == 0)
				return;

			$template = FST_EMail::Get_Template('email_handler_on_reply');
			$email = FST_EMail::ParseTemplate($template,$ticket,$subject,$body,$template['ishtml']);

			$mailer->isHTML($template['ishtml']);
			$mailer->setSubject($email['subject']);
			$mailer->setBody($email['body']);

			$send = FST_EMail::Send($mailer);
		
			FST_EMail::Debug('User_Reply', 
				array(
					'Ticket' => $ticket,
					'Mailer' => $mailer,
					'Result' => $send
					));
		}
	}
	
	/*******************
	 * Helper Functions
	 *******************/	
	
	static function ProcessLinks(&$data, $links)
	{
		foreach ($links as $link => $temp)
		{
			if ($data[$link] == "")
				continue;
			$data[$link] = "<a href='{$data[$link]}'>here</a>";	
		}
	}
	
	static function ParseGeneralTemplate($template, $data)
	{
		if ($template['ishtml'])
		{
			$data['body'] = str_replace("\n","<br>\r\n",$data['body']);	
		}
	
		foreach($data as $var => $value)
			$vars[] = FST_EMail::BuildVar($var,$value);

		$email['subject'] = FST_EMail::ParseText($template['subject'],$vars);
		$email['body'] = FST_EMail::ParseText($template['body'],$vars);
	
		if ($template['ishtml'])
			$email['body'] = FST_EMail::MaxLineLength($email['body']);
		
		return $email;			
	}
	
	static function AddTicketRecpts(&$mailer, &$ticket, &$custrec)
	{
		// add ticket user as recipient
		if ($ticket['user_id'] == 0)
		{
			$recipient = array($ticket['email']/*, $ticket['unregname']*/);
		} else {
			$recipient = array($custrec['email']/*, $custrec['name']*/);
		}
		
		$mailer->addRecipient($recipient);
		
		// check for any ticket cc users
		FST_EMail::GetTicketCC($ticket);
		
		if (count($ticket['cc'] > 0))
		{
			foreach ($ticket['cc'] as $cc)
			{
				$mailer->addCC(array($cc['email']/*, $cc['name']*/));	
			}
		}		
		
		// if user_id on ticket is set, then check for any group recipients
		if ($ticket['user_id'] > 0)
		{
			$db = JFactory::getDBO();
			
			// get groups that the user belongs to
			$qry = "SELECT * FROM #__fst_ticket_group WHERE id IN (SELECT group_id FROM #__fst_ticket_group_members WHERE user_id = '".FSTJ3Helper::getEscaped($db, $ticket['user_id'])."')";
			$db->setQuery($qry);
			//echo $qry."<br>";
			$groups = $db->loadObjectList('id');
			
			if (count($groups) > 0)
			{
				//print_p($groups);
			
				$gids = array();
			
				foreach ($groups as $id => &$group)
				{
					$gids[$id] = $id;	
				}
			
				// get list of users in the groups
				$qry = "SELECT m.*, u.email, u.name FROM #__fst_ticket_group_members as m LEFT JOIN #__users as u ON m.user_id = u.id WHERE group_id IN (" . implode(", ",$gids) . ")";
				$db->setQuery($qry);
				//echo $qry."<br>";
				$users = $db->loadObjectList();
				//print_p($users);
				
				$toemail = array();
				
				// for all users, if group has cc or user has cc then add to cc list			
				foreach($users as &$user)
				{
					if ($user->allemail || $groups[$user->group_id]->allemail)
					{
						$toemail[$user->email] = $user->name;

					}
				}	
				
				foreach ($toemail as $email => $name)
					$mailer->addCC(array($email/*, $name*/));	
			}
		}
	}

	static function Debug($type, $vars)
	{
		if (!FST_EMail::$debug)
			return;
		
		echo "<h2>$type</h2>";
		foreach ($vars as $name => $data)
		{
			echo "<h4>$name</h4>";
			print_p($data);
		}
		
		if (FST_EMail::$exit_on_debug)
			exit;
	}
	
	static function Send($mailer)
	{
		if (FST_EMail::$dontsend)
		{
			return "NOT SENT";
		} else {
			return $mailer->Send();	
		}
	}
	
	static function Get_Sender()
	{
		$config = JFactory::getConfig();
		
		if (FSTJ3Helper::IsJ3())
		{		
			$address = 	$config->get( 'config.mailfrom' );
			$name = $config->get( 'config.fromname' );		

			if (!$address || !$name)
			{
				$address = 	$config->get( 'mailfrom' );
				$name = $config->get( 'fromname' );		
			}
		} else {		
			$address = 	$config->getValue( 'config.mailfrom' );
			$name = $config->getValue( 'config.fromname' );		
		}

		/*if (FST_Settings::get('support_email_from_name') != "" && FST_Settings::get('support_email_from_name') != "0")
			$name = FST_Settings::get('support_email_from_name');

		if (FST_Settings::get('support_email_from_address') != "" && FST_Settings::get('support_email_from_address') != "0")
			$address = FST_Settings::get('support_email_from_address');*/

		return array( $address, $name );
	}
	
	static function GetTicketCC(&$ticket)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT u.name, u.id, u.email FROM #__fst_ticket_cc as c LEFT JOIN #__users as u ON c.user_id = u.id WHERE c.ticket_id = {$ticket['id']} ORDER BY name";
		$db->setQuery($qry);
		$ticket['cc'] = $db->loadAssocList();
	}

	static function AdminTo($mailer, &$ticket)
	{
		$rcpt = 0; // keep track of how many recipients
		
		// if email all admins
		if (FST_Settings::get('support_email_all_admins'))
		{
			if (!FST_Settings::get('support_email_all_admins_only_unassigned') || $ticket['admin_id'] > 0)
			{
				// Build a list of all available ticket handlers
				$handlers = FST_Ticket_Helper::ListHandlers(
									$ticket['prod_id'], $ticket['ticket_dept_id'], $ticket['ticket_cat_id'], 
									FST_Settings::get('support_email_all_admins_ignore_auto'), 
									FST_Settings::get('support_email_all_admins_can_view'));
			
				// add handlers to the email to list
				$rcpt += FST_EMail::AddAdminAddress($mailer, $handlers);
			}
		}
		
		if ($ticket['admin_id'] < 1)
		{
			$rcpt += FST_EMail::AddMultiAddress($mailer, FST_Settings::get('support_email_unassigned'));
		} else {
			$rcpt += FST_EMail::AddAdminAddress($mailer, $ticket['admin_id']);
		} 	
		
		// any cc emails need adding	
		if (FST_Settings::get('support_email_admincc'))
			$rcpt += FST_EMail::AddMultiAddress($mailer, FST_Settings::get('support_email_admincc'));
		
		return $rcpt;
	}
	
	static function AddAdminAddress($mailer, $ids)
	{
		if (!is_array($ids))
			$ids = array($ids);		
				
		if (count($ids) < 1)
			$ids[] = "0";
				
		// load all handlers and add them to the email to addresses
		$query = " SELECT a.*, au.name, au.username, au.email FROM #__fst_user as a ";
		$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
		$query .= " WHERE a.id IN (".implode(", ", $ids) . ")";
				
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$admins = $db->loadObjectList();
				
		if (!is_array($admins))
			return 0;
		
		$count = 0;
		foreach ($admins as $admin)
		{
			$mailer->AddAddress($admin->email, $admin->name);
			$count++;
		}
		
		return $count;
	}
	
	static function AddMultiAddress($mailer, $address)
	{
		$address = trim($address);
		if ($address == "")
			return 0;
		$count = 0;
		if (strpos($address, ","))
		{
			$addresss = explode(",", $address);
			foreach ($addresss as $address)
			{
				if (trim($address))
				{
					$mailer->AddAddress(trim($address));	
					$count++;
				}
			}
		} else {
			if (trim($address))
			{
				$mailer->AddAddress(trim($address));	
				$count++;
			}
		}
		
		return $count;
	}

	static function &GetHandler($admin_id)
	{
		if ($admin_id == 0)
		{
			$res = array("name" => JText::_("UNASSIGNED"),"username" => JText::_("UNASSIGNED"),"email" => "");
			return $res;	
		}
		$db = JFactory::getDBO();
		$query = " SELECT a.*, au.name, au.username, au.email FROM #__fst_user as a ";
		$query .= " LEFT JOIN #__users as au ON a.user_id = au.id ";
		$query .= " WHERE a.id = '".FSTJ3Helper::getEscaped($db, $admin_id)."'";
		$db->setQuery($query);
		$handler = $db->loadAssoc();
		return $handler;
	} 

	static function &GetUser($user_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__users WHERE id = '".FSTJ3Helper::getEscaped($db, $user_id)."'";
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row;
	}

	static function GetStatus($status_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_ticket_status WHERE id = '".FSTJ3Helper::getEscaped($db, $status_id)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}

	static function GetArticle($artid)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_kb_art WHERE id = '".FSTJ3Helper::getEscaped($db, $artid)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}

	static function GetPriority($pri_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_ticket_pri WHERE id = '".FSTJ3Helper::getEscaped($db, $pri_id)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}

	static function GetCategory($cat_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_ticket_cat WHERE id = '".FSTJ3Helper::getEscaped($db, $cat_id)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}

	static function GetDepartment($dept_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_ticket_dept WHERE id = '".FSTJ3Helper::getEscaped($db, $dept_id)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}

	static function GetProduct($prod_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT title FROM #__fst_prod WHERE id = '".FSTJ3Helper::getEscaped($db, $prod_id)."'";	
		$db->setQuery($qry);
		$row = $db->loadAssoc();
		return $row['title'];
	}
	
	static function GetMessageHist($ticket_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT m.*, u.name, u.username, u.email FROM #__fst_ticket_messages as m";
		$qry .= " LEFT JOIN #__users as u ON m.user_id = u.id";
		$qry .= " WHERE ticket_ticket_id = '".FSTJ3Helper::getEscaped($db, $ticket_id)."'";	
		$qry .= " AND admin IN (0, 1) ORDER BY posted DESC";
		
		//echo $qry."<br>";
		$db->setQuery($qry);
		$rows = $db->loadAssocList();

		return $rows;
	}
	
	static function MaxLineLength($in)
	{
		$lines = explode("\n", $in);
		
		$maxlen = 250;
		
		$out = array();
		
		foreach	($lines as $line)
		{
			while (strlen($line) > $maxlen)
			{
				$sublen = strrpos(substr($line, 0, $maxlen), " ");
				if ($sublen > 0)
				{
					$out[] = substr($line, 0, $sublen);
					$line = substr($line, $sublen+1);	
				} else {
					$out[] = $line;
					$line = "";	
				}
			}		
			
			$out[] = $line;
		}

		return implode("\r\n", $out);
	}

	static function &ParseTemplate($template,&$ticket,$subject,$body,$ishtml)
	{
		$handler = FST_EMail::GetHandler($ticket['admin_id']);
		$custrec = FST_EMail::GetUser($ticket['user_id']);
	
		$subject = trim(str_ireplace("re:","",$subject));
		$vars[] = FST_EMail::BuildVar('subject',$subject);
		/*if ($ishtml)
		{
			$body = str_replace("\n","<br />\n",$body);	
		}*/
		$body = FST_Helper::ParseBBCode($body);
		$vars[] = FST_EMail::BuildVar('body',$body);
		$vars[] = FST_EMail::BuildVar('reference',$ticket['reference']);
		$vars[] = FST_EMail::BuildVar('password',$ticket['password']);
		
		if ($ticket['user_id'] == 0)
		{
			$vars[] = FST_EMail::BuildVar('user_name',$ticket['unregname']);
			$vars[] = FST_EMail::BuildVar('user_username',JText::_("UNREGISTERED"));
			$vars[] = FST_EMail::BuildVar('user_email',$ticket['email']);
		} else {
			$vars[] = FST_EMail::BuildVar('user_name',$custrec['name']);
			$vars[] = FST_EMail::BuildVar('user_username',$custrec['username']);
			$vars[] = FST_EMail::BuildVar('user_email',$custrec['email']);
		}
		$vars[] = FST_EMail::BuildVar('handler_name',$handler['name']);
		$vars[] = FST_EMail::BuildVar('handler_username',$handler['username']);
		$vars[] = FST_EMail::BuildVar('handler_email',$handler['email']);
		
		$vars[] = FST_EMail::BuildVar('ticket_id',$ticket['id']);
		$vars[] = FST_EMail::BuildVar('status',FST_EMail::GetStatus($ticket['ticket_status_id']));
		$vars[] = FST_EMail::BuildVar('priority',FST_EMail::GetPriority($ticket['ticket_pri_id']));
		$vars[] = FST_EMail::BuildVar('category',FST_EMail::GetCategory($ticket['ticket_cat_id']));
		$vars[] = FST_EMail::BuildVar('department',FST_EMail::GetDepartment($ticket['ticket_dept_id']));
		$vars[] = FST_EMail::BuildVar('product',FST_EMail::GetProduct($ticket['prod_id']));
		
		if (strpos($template['body'],"{messagehistory}") > 0)
		{
			//echo "Get message history<br>";	
			$messages = FST_EMail::GetMessageHist($ticket['id']);
			
			// need to load in the messagerow template and parse it
			$text = FST_EMail::ParseMessageRows($messages, $ishtml);
			
			$vars[] = FST_EMail::BuildVar('messagehistory',$text);
			//print_p($messages);
		}
		
		$uri = JURI::getInstance();
		$baseUrl = $uri->toString( array('scheme', 'host', 'port'));
		
		$vars[] = FST_EMail::BuildVar('ticket_link',$baseUrl . FSTRoute::_('index.php?option=com_fst&view=ticket&ticketid=' . $ticket['id'], false));
		$vars[] = FST_EMail::BuildVar('admin_link',$baseUrl . FSTRoute::_('index.php?option=com_fst&view=admin&layout=support&ticketid=' . $ticket['id'], false));

		$config = JFactory::getConfig();
		if (FSTJ3Helper::IsJ3())
		{
			$sitename = $config->get('sitename');
		} else {
			$sitename = $config->getValue('sitename');	
		}
		
		if (FST_Settings::get('support_email_site_name') != "")
			$sitename = FST_Settings::get('support_email_site_name');

		$vars[] = FST_EMail::BuildVar('websitetitle',$sitename);
	
		// need to add the tickets custom fields to the output here
		
		$fields = FSTCF::GetAllCustomFields(true);
		$values = FSTCF::GetTicketValues($ticket['id'],$ticket);
		
		foreach ($fields as $fid => &$field)
		{
			$name = "custom_" . $fid;
			$value = "";
			if (array_key_exists($fid, $values))
				$value = $values[$fid]['value'];
			//echo "$name -> $value<br>";
			
			$fieldvalues = array();
			$fieldvalues[0]['field_id'] = $fid;
			$fieldvalues[0]['value'] = $value;
			
			// only do area output processing if we are in html mode
			if ($field['type'] != "area" || $ishtml)
				$value = FSTCF::FieldOutput($field, $fieldvalues, '');
			
			$vars[] = FST_EMail::BuildVar($name, $value);
		}
	
		$email['subject'] = FST_EMail::ParseText($template['subject'],$vars);
		$email['body'] = FST_EMail::ParseText($template['body'],$vars);
	
		//print_p($vars);
	
		//print_p($email);

		if ($template['ishtml'])
		{
			//$email['subject'] = str_replace("\n","<br />\n",$email['subject']);
			$email['body'] = FST_EMail::MaxLineLength($email['body']);
		} else {	
			// strip bbcode out of subject or parse it to html depending on template type
			$email['body'] = str_replace("<br />","\n",$email['body']);
			$email['body'] = html_entity_decode($email['body']);
			$email['body'] = preg_replace_callback("/(&#[0-9]+;)/", array($this, "email_decode_utf8"), $email['body']); 
			$email['body'] = strip_tags($email['body']);
		}

		return $email;	
	}
	
	static function email_decode_utf8($m) { 
		return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); 
	}
	
	static function ParseMessageRows(&$messages, $ishtml)
	{
		$template = FST_EMail::Get_Template('messagerow');
		$result = "";
		
		foreach ($messages as &$message)
		{
			$vars = array();
			//print_p($message);
			if ($message['name'])
			{
				$vars[] = FST_EMail::BuildVar('name',$message['name']);
				$vars[] = FST_EMail::BuildVar('email',$message['email']);
				$vars[] = FST_EMail::BuildVar('username',$message['username']);
			} else {
				$vars[] = FST_EMail::BuildVar('name','Unknown');
				$vars[] = FST_EMail::BuildVar('email','Unknown');
				$vars[] = FST_EMail::BuildVar('username','Unknown');
			}
			$vars[] = FST_EMail::BuildVar('subject',$message['subject']);
			$vars[] = FST_EMail::BuildVar('posted',FST_Helper::Date($message['posted']));
			
			$message['body'] = FST_Helper::ParseBBCode($message['body']);

			if ($ishtml)
			{
				$message['body'] = str_replace("\n","<br>\n",$message['body']);	
				$vars[] = FST_EMail::BuildVar('body',$message['body'] . "<br />");	
			} else {
				$vars[] = FST_EMail::BuildVar('body',$message['body'] . "\n");	
			}
			
			$result .= FST_EMail::ParseText($template['body'],$vars);
		}
		
		return $result;
	}

	static function BuildVar($name,$value)
	{
		$data['name'] = $name;
		$data['value'] = $value;
		return $data;
	}

	static function ParseText($text,&$vars)
	{
		foreach ($vars as $var)
		{
			//echo "Proc : {$var['name']}<br>";
			$value = $var['value'];
			$block = "{".$var['name']."}";
			$start = "{".$var['name']."_start}";
			$end = "{".$var['name']."_end}";
		
			if ($value != "")
			{
				$text = str_replace($block, $value, $text);	
				$text = str_replace($start, "", $text);	
				$text = str_replace($end, "", $text);	
			} else {
				$text = str_replace($block, "", $text);	
				$pos_end = strpos($text, $end);
				$pos_beg = strpos($text, $start);
				//echo "$start = $pos_beg, $end = $pos_end<br>";
				if ($pos_end && $pos_beg){
					$text = substr_replace($text, '', $pos_beg, ($pos_end - $pos_beg) + strlen($end));
				}
			}
		}
		return $text;
	}

	static function Get_Template($tmpl)
	{
		$db = JFactory::getDBO();
		$qry = 	"SELECT body, subject, ishtml FROM #__fst_emails WHERE tmpl = '".FSTJ3Helper::getEscaped($db, $tmpl)."'";
		$db->setQuery($qry);
		return $db->loadAssoc();
	}
}
