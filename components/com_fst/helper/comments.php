<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'j3helper.php' );

jimport('joomla.utilities.date');
jimport('joomla.mail.helper');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'captcha.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'email.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments'.DS.'_handler.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'fields.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'parser.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'tickethelper.php');

class FST_Comments
{
	var $table = "#__fst_comments";
	var $customfields;
	var $tmplpath;
	var $_data;
	var $_counts;
	
	var $hideadd = false;
	var $ident = '';
	var $identifier;
	var $itemid;
	var $captcha = '';
	var $moderate = 0;
	var $handler = null;
	var $handlers = array();
	
	var $use_comments = 0;
	var $use_email = 1;
	var $use_website = 1;
	var $loggedin = 0;
	var $showheader = 1;
	var $show_item_select = 0;
	
	var $parser = null;

	// comments options
	var $opt_getnew = 1;
	var $opt_display = 1;
	var $opt_show_form_after_post = 1;
	var $opt_order = 0;
	var $opt_form_clear_comment = 1;
	var $opt_show_posted_message_only = 0;
	var $can_add = 1;
	var $opt_no_mod = 0;
	var $opt_show_add = 1;
	var $opt_max_length = 0;
	var $opt_no_edit = 0;
	var $opt_disable_pages = 0;
	
	var $comments_hide_add = 1;
	// add comment form vars
	var $post;
	var $errors;
	var $perpage = 99999;
	
	function __construct($identifier, $itemid = -1, &$itemlist = null)
	{
		$this->uid = mt_rand(1000,9999);
		
		if (JRequest::getVar('uid') > 0)
			$this->uid = JRequest::getVar('uid');
		/*$this->use_comments = FST_Settings::get('announce_comments_allow');
		if (!$this->use_comments)
			return;*/
		
		$this->identifier = $identifier;
		
		$this->_permissions = FST_Ticket_Helper::getAdminPermissions();

		$this->use_email = FST_Settings::get('commnents_use_email');//FSJ_Settings::GetComponentSetting( fsj_get_com(), 'comments_email', 1 );
		$this->use_website = FST_Settings::get('commnents_use_website'); //FSJ_Settings::GetComponentSetting( fsj_get_com(), 'comments_website', 1 );
		
		$this->tmplpath = JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'tmpl'.DS.'comments';	
		$this->dest_email = FST_Settings::get( 'email_on_comment' );
		
		$this->itemid = $itemid;
		$this->itemlist = $itemlist;
		$this->post = array();

		$this->post['name'] = $this->GetName();
		$this->post['email'] = '';
		$this->post['website'] = '';
		$this->post['body'] = '';
		$this->post['created'] = 'now';
		$this->post['ident'] = $identifier;
		$this->post['itemid'] = $itemid;
		
		$this->errors = array();
		$this->errors['name'] = '';
		$this->errors['email'] = '';
		$this->errors['website'] = '';
		$this->errors['body'] = '';
		$this->errors['captcha'] = '';
		$this->errors['itemid'] = '';
		
		// text templates
		$this->add_a_comment = JText::_("ADD_A_COMMENT");
		$this->post_comment = JText::_("POST_COMMENT");

		$this->comments_hide_add = FST_Settings::get('comments_hide_add');
		
		if (FST_Settings::get('comments_who_can_add') == "registered")
		{
			if ($this->_permissions['userid'] == 0)
			{
				$this->can_add = 0;	
			}
		}
		
		$captcha = new FST_Captcha();
		$this->captcha = $captcha->GetCaptcha();

		// set up moderation
		$commod = FST_Settings::get('comments_moderate');
		
		$this->moderate = 0;
		
		if ($commod == "all")
		{
			$this->moderate = 1;
		} elseif ($commod == "guests")
		{
			if ($this->_permissions['userid'] == 0)
				$this->moderate = 1;
		} elseif ($commod == "registered")
		{
			if ($this->_permissions['mod_kb'] == 0)
				$this->moderate = 1;
		}
		
		// determine template and if its custom or not
		$this->IncludeTemplates();
		
		$this->template = "comments_general";
		$this->template_type = 3;
		if (FST_Settings::get('comments_general_use_custom'))
			$this->template_type = 2;
		
		if ($this->handler)
		{
			$this->template = "comments_" . $this->handler->GetName();		
			$this->template_type = 3;
			if (FST_Settings::get('comments_' . $this->handler->GetName() . '_use_custom'))
				$this->template_type = 2;
		}
		
		//print_p($this);
		//exit;
	}
	
	function IncludeTemplates()
	{
		if ($this->identifier && $this->identifier != "")
		{
			require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments'.DS. $this->identifier.'.php');
			$classname = "FST_Comments_Handler_" . $this->identifier;
			$this->handler = new $classname($this);
			$this->ident = $this->handler->ident;
			$this->handlers[$this->handler->ident] = $this->handler;
			//echo "Getitng custom fields 1 : {$this->ident}<br>";
			$this->customfields = FSTCF::Comm_GetCustomFields($this->ident);
			//exit;
		} else {
			$dir = JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments';
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if ($file == "." || $file == ".." || $file == "_handler.php") continue;
				if (strpos($file,".php") < 1) continue;
				
				require_once($dir . DS . $file);
				$classname = "FST_Comments_Handler_" . str_ireplace(".php","",$file);
				if (class_exists($classname))
				{
					$handler = new $classname($this);
					$this->handlers[$handler->ident] = $handler;
				}
			}
			//echo "Getitng custom fields 2 : Fixed -1<br>";
			$this->customfields = FSTCF::Comm_GetCustomFields(-1);
			//print_p($this);
			//exit;
		}
	}
	
	function GetName()
	{
		$user = JFactory::getUser();

		if ($user->name)
		{
			$this->loggedin = 1;
			return $user->name;
		}
			
		return JText::_("Guest");
	}
	
	function GetComment($commentid)
	{
		if (!$this->use_comments)
			return;

		$db = JFactory::getDBO();

		$qry = "SELECT * FROM " . $this->table . " WHERE ";
		
		$where = array();
		//$order = array();
		
		$where[] = "id = ".FSTJ3Helper::getEscaped($db, $commentid);
		$qry .= implode(" AND ",$where);

		$db->setQuery($qry);
		$comment = &$db->loadAssoc();
		
		$this->_data = array();
		$this->_data[] =& $comment;
		$this->PopulateCustomFields();

		return $comment;
	}

	// itentifier - defined comment set id
	// itemid - id of the item to get comments for
	// count - how many to get, -1 for all
	function GetComments($since = 0, $count = -1, $offset = 0)
	{
		if (!$this->use_comments)
			return;
		
		if (!empty($this->_data))
			return;
				
		$db = JFactory::getDBO();
		
		$qry = "SELECT c.* ";
		
		if ($this->identifier == "test")
			$qry .= ", p.title as product ";
		
		$qry .= " FROM " . $this->table . " as c";
		
		if ($this->identifier == "test")
			$qry .= " LEFT JOIN #__fst_prod AS p ON c.itemid = p.id ";
	
		$qry .= " WHERE ";
		
		$where = array();
		//$order = array();
		
		$where[] = "ident=". $this->ident;
		if (is_array($this->itemid))
		{
			if (count($this->itemid) > 0)
			{
				$items = array();
				foreach ($this->itemid as $item)
					$items[] = FSTJ3Helper::getEscaped($db, $item);
				$where[] = "c.itemid IN (" . implode(", ",$this->itemid) . ")";
			} else {
				$where[] = "c.itemid = 0";	
			}
		} else {	
			if ($this->itemid > -1)
				$where[] = "c.itemid='".FSTJ3Helper::getEscaped($db, $this->itemid)."'";
		}
		
		if (!$this->_permissions['mod_kb'])
		{
			$where[] = "c.published=1";
		}
		else	
			$order[] = "c.published";
		
		if ($this->opt_no_mod)
			$where[] = "c.published=1";
			
		$qry .= implode(" AND ",$where);
		
		if ($this->opt_order == 0)
		{
			$order[] = "c.created DESC";
		} else if ($this->opt_order == 2)
		{
			$order = array();
			$order[] = "RAND()";
		} else
		{
			$order[] = "c.created ASC";
		}
		$qry .= " ORDER BY " . implode(" , ",$order);
		
		if ($count != -1)
			$qry .= " LIMIT $offset, $count";
		
		$db->setQuery($qry);
		
		$this->_data = $db->loadAssocList();
		
		$this->PopulateCustomFields();
	}
	
	function GetModerateCounts()
	{
		if (!$this->use_comments)
			return;
		
		if (empty($this->_moderatecounts))
		{
			$qry = "SELECT count(*) as count, ident FROM " . $this->table . " WHERE ";
			
			$where[] = "published=0";
			
			$qry .= implode(" AND ",$where);
			
			$qry .= " GROUP BY ident ";
			
			$db = JFactory::getDBO();
			$db->setQuery($qry);	
			$this->_moderatecounts = $db->loadAssocList('ident');
			
			if (!$this->_moderatecounts)
				$this->_moderatecounts = array();
			
			$total = 0;
			foreach ($this->_moderatecounts as $count)
				$total += $count['count'];
			
			$this->_moderatetotal = $total;
		}		
		
		return $this->_moderatecounts;
	}
	
	function GetModerateTotal()
	{
		if (empty($this->_moderatecounts))	
			$this->GetModerateCounts();
			
		return $this->_moderatetotal;
	}
	
	function DisplayModStatus($template = 'modstatus.php')
	{
		if (empty($this->_moderatecounts))	
			$this->GetModerateCounts();
		
		include $this->tmplpath . DS . $template;
	}
	
	function DisplayAdd()
	{
		include $this->tmplpath . DS .'addcomment.php';
	}
	
	function GetCommentCounts()
	{
		if (!$this->use_comments)
			return;
		
		$db = JFactory::getDBO();
		if (empty($this->_counts))
		{
			$ids = array();
			foreach ($this->itemlist as $item)
			{
				if (is_array($item))
				{
					$ids[] = FSTJ3Helper::getEscaped($db, $item['id']);	
				} else {
					$ids[] = FSTJ3Helper::getEscaped($db, $item->id);	
				}
			}	
			if (count($ids) == 0)
			{
				return array();	
			}
			
			$qry = "SELECT count(*) as count, itemid FROM " . $this->table . " WHERE ";
			
			$where[] = "ident=".$this->ident;
			$where[] = "itemid IN (".implode(",",$ids) .")";
			$where[] = "published=1";
			
			$qry .= implode(" AND ",$where);
			
			$qry .= " GROUP BY itemid ";
			
			$db->setQuery($qry);	
			$this->_counts = &$db->loadAssocList('itemid');
			
			if (!$this->_counts)
				$this->_counts = array();
		}
	}
	
	function DisplayComments($count = -1, $order = 1, $maxlength = -1)
	{
		if (!$this->use_comments)
			return;
		
		if (empty($this->_data))
		{
			$this->GetComments(0, $count, 0, $order);
		}
				
		include $this->tmplpath . DS .'list.php';
	
		return count($this->_data);
	}
	
	function GetCountOnly($itemid)
	{
		if (empty($this->_data))
			$this->GetComments();
		
		$count = 0;
		foreach($this->_data as &$data)
		{
			if (array_key_exists('itemid',$data) && $data['itemid'] == $itemid)	
				$count++;
		}
		return $count;
	}
	
	function DisplayCommentsOnly($itemid)
	{
		if (!$this->use_comments)
			return;
		
		if (empty($this->_data))
			$this->GetComments();
		
		$captcha = new FST_Captcha();
		$this->captcha = $captcha->GetCaptcha();
		$this->onlyitem = $itemid;
		
		include $this->tmplpath . DS .'listonly.php';
		
		return count($this->_data);
	}
	
	function Process()
	{
		if (!$this->use_comments)
			return false;
		
		ob_clean();

		$task = JRequest::getVar('task');
		if ($task == "commentpost")
			return $this->DoPost();
	
		if ($task == "removecomment")
			return $this->DoPublishComment(2);

		if ($task == "approvecomment")
			return $this->DoPublishComment(1);
		
		if ($task == "editcomment")
			return $this->DoEditComment();

		if ($task == "savecomment")
			return $this->DoSaveComment();
		
		if ($task == "showcomment")
			return $this->DoShowComment();
		
		if ($task == "deletecomment")
			return $this->DoDeleteComment();
		
		if ($task == "modinner")
			return $this->DisplayModerateInner();
		
		return false;
	}
	
	function DoPublishComment($published)
	{
		$commentid = JRequest::getVar('commentid',0,'','int');   
		
		if (!$this->_permissions['mod_kb'])
			return;
			
		if (!$commentid)
			return;
			
		$db = JFactory::getDBO();
		$qry = "UPDATE #__fst_comments SET published = $published WHERE id = '".FSTJ3Helper::getEscaped($db, $commentid)."'";
		$db->SetQuery($qry);
		$db->Query();
			
		exit;
		return true;	
	}

	function DoShowComment()
	{
		$commentid = JRequest::getVar('commentid',0,'','int');   
		
		if (!$this->_permissions['mod_kb'])
			return;
		
		if (!$commentid)
			return;

		$this->comment = $this->GetComment($commentid);

		include $this->tmplpath . DS .'comment.php';
		exit;
	}
	
	function DoEditComment()
	{
		$commentid = JRequest::getVar('commentid',0,'','int');   
		
		if (!$this->_permissions['mod_kb'])
			return;
		
		if (!$commentid)
			return;

		$this->comment = $this->GetComment($commentid);

		foreach ($this->comment as $field => $value)
		{
			if (substr($field,0,7) == "custom_")
			{
				JRequest::setVar($field,$value);
			}	
		}
		$this->commentid = $commentid;
		$this->can_add = true;
		
		$ident = $this->comment['ident'];
		$this->customfields = FSTCF::Comm_GetCustomFields($ident);

		// need to return the edit form with this comment filled in
		include $this->tmplpath . DS .'editcomment.php';

		exit;
	}
	function DoDeleteComment()
	{
		$commentid = JRequest::getVar('commentid',0,'','int');   
		
		if (!$this->_permissions['mod_kb'])
			return;
		
		if (!$commentid)
			return;
		
		$db = JFactory::getDBO();
		$qry = "DELETE FROM #__fst_comments WHERE id = '".FSTJ3Helper::getEscaped($db, $commentid)."'";
		$db->SetQuery($qry);
		$db->Query();
		
		exit;
		return true;	
	}

	function DoPost()
	{	
		$this->post['name'] = JRequest::getVar('name','','','string');   
		$this->post['email'] = JRequest::getVar('email','','','string');   
		$this->post['website'] = JRequest::getVar('website','','','string');   
		$this->post['body'] = JRequest::getVar('body','','','string');   
		$this->post['itemid'] = JRequest::getVar('itemid',0,'','int');   
		$this->post['ident'] = JRequest::getVar('ident',0,'','int');   

		$published = 1;	
		
		if ($this->moderate)
			$published = 0;
		
		$captcha = new FST_Captcha();
		
		$this->valid = 1;
		
		if ($this->post['name'] == "")
		{
			$this->errors['name'] = JText::_("YOU_MUST_ENTER_A_NAME");
			$this->valid = 0;	
		}
		
		if (JFactory::getUser()->id < 1)
		{
			// EMail validation is only applicable to non-logged in users
			if ($this->use_email && $this->post['email'] == "")
			{
				$this->errors['email'] = JText::_("YOU_MUST_ENTER_AN_EMAIL_ADDRESS");
				$this->valid = 0;	
			}
			
			if ($this->use_email && $this->post['email'] != "" && !JMailHelper::isEmailAddress($this->post['email']))
			{
				$this->errors['email'] = JText::_("INVALID_EMAIL_ADDRESS_ENTERED");
				$this->valid = 0;	
			}
		}
		
		if ($this->use_website && $this->post['website'] != "" && 0)
		{
			$this->errors['website'] = JText::_("INVALID_WEBSITE_ADDRESS_ENTERED");
			$this->valid = 0;	
		}
		
		if ($this->post['body'] == "")
		{
			$this->errors['body'] = JText::_("YOU_MUST_ENTER_A_COMMENT_TO_POST");
			$this->valid = 0;	
		}

		if ($this->handler->item_select_must_have)
		{
			if ($this->post['itemid'] == 0)
			{
				$this->errors['itemid'] = JText::_("YOU_MUST_SELECT_A") . $this->handler->email_article_type;
				$this->valid = 0;	
			}
		}

		if (!$captcha->ValidateCaptcha())
		{
			$this->errors['captcha'] = JText::_("INVALID_SECURITY_CODE");
			$this->valid = 0;	
		}

		if (!FSTCF::ValidateFields($this->customfields, $this->errors))
		{
			$this->valid = 0;	
		}

		$output['valid'] = $this->valid;

		if ($this->valid == 1)
		{			
			$db = JFactory::getDBO();
			$user = JFactory::getUser();
			$userid = $user->id;
			
			$custom = FSTCF::Comm_StoreFields($this->customfields);
			$custom = serialize($custom);
			$now = FST_Helper::CurDate();
			
			$qry = "INSERT INTO " . $this->table . " (ident, itemid, name, email, website, body, published, created, userid, custom) VALUES (";
			$qry .= $this->post['ident'];
			$qry .= " , " . $this->post['itemid'];
			$qry .= " , '" . FSTJ3Helper::getEscaped($db, $this->post['name']);
			$qry .= "' , '" . FSTJ3Helper::getEscaped($db, $this->post['email']);
			$qry .= "' , '" . FSTJ3Helper::getEscaped($db, $this->post['website']);
			$qry .= "' , '" . FSTJ3Helper::getEscaped($db, $this->post['body']);
			$qry .= "' , $published, '{$now}', '".FSTJ3Helper::getEscaped($db, $userid)."', '".FSTJ3Helper::getEscaped($db, $custom)."' )";
			
			$db->SetQuery($qry);
			$db->Query();

			$this->comment = $this->post;
			$this->comment['id'] = $db->insertid();
			$this->comment['ident'] = $this->post['ident'];
			
			foreach ($this->customfields as $id => $field)
			{
				$this->comment["custom_$id"] = JRequest::getVar("custom_$id","");
				JRequest::setVar("custom_$id",'');
			}
			
			FST_EMail::Send_Comment($this);

			ob_clean();			
			if ($this->moderate)
			{
				$this->comment['published'] = 0;				
				include $this->tmplpath . DS . 'moderate.php';	
			} else {
				if ($this->opt_show_posted_message_only)
				{
					$this->comment['published'] = 0;
					include $this->tmplpath . DS . 'thanks.php';
				} else {
					$this->comment['published'] = 1;
					include $this->tmplpath . DS . 'thanks.php';
					include $this->tmplpath . DS . 'comment.php';					
				}
			}
			$output['comment'] = ob_get_contents();
			
			if ($this->opt_display)
			{
				if ($this->opt_order == 0)
				{
					$output['display'] = 'before';
				} else {
					$output['display'] = 'after';
				}
			} else {
				$output['display'] = 'none';	
			}
			
			if ($this->opt_show_posted_message_only)
				$output['display'] = "replace";
			
			$this->post['name'] = $this->GetName();
			$this->post['email'] = '';
			$this->post['website'] = '';
			$this->post['body'] = '';
			$this->post['created'] = 'now';
			
			if ($this->opt_show_form_after_post)
			{
				ob_clean();
				$this->captcha = $captcha->GetCaptcha();
				include $this->tmplpath . DS .'addcomment.php';
				$output['form'] = ob_get_contents();
				$output['form_display'] = "replace";
			} else {
				$output['form'] = '';	
				$output['form_display'] = "";
				if ($this->opt_form_clear_comment)
				{
					$output['form_display'] = "clear_comment";
				}
			}
			ob_clean();
			
			echo json_encode($output);
			exit;
		}
		else
		{		
			$output['display'] = 'none';
			$output['form_display'] = "replace";

			ob_clean();
			$this->comment = $this->post;
			include $this->tmplpath . DS .'comment.php';
			$output['comment'] = ob_get_contents();

			ob_clean();
			$this->captcha = $captcha->GetCaptcha();
			include $this->tmplpath . DS .'addcomment.php';
			$output['form'] = ob_get_contents();

			ob_clean();
			echo json_encode($output);
			exit;
		}
		
		return true;		
	}
	
	function DoSaveComment()
	{	
		$this->post['name'] = JRequest::getVar('name','','','string');   
		$this->post['commentid'] = JRequest::getVar('commentid','','','string');   
		$this->post['email'] = JRequest::getVar('email','','','string');   
		$this->post['website'] = JRequest::getVar('website','','','string');   
		$this->post['body'] = JRequest::getVar('body','','','string');   
		
		$db = JFactory::getDBO();
			
		$custom = FSTCF::Comm_StoreFields($this->customfields);
		$custom = serialize($custom);

		$qry = "UPDATE {$this->table} SET name = '".FSTJ3Helper::getEscaped($db, $this->post['name'])."', email = '".FSTJ3Helper::getEscaped($db, $this->post['email'])."', ";
		$qry .= "website = '".FSTJ3Helper::getEscaped($db, $this->post['website'])."', body = '".FSTJ3Helper::getEscaped($db, $this->post['body'])."', ";
		$qry .= "custom = '".FSTJ3Helper::getEscaped($db, $custom)."' WHERE id = ".FSTJ3Helper::getEscaped($db, $this->post['commentid']);
		
		$db->SetQuery($qry);
		$db->Query();

		$this->DoShowComment();

		exit;
	}

	function GetItemSelect()
	{
		if (!$this->handler)
			return "";
			
		// need to get a list of products
		$this->handler->GetItemData();	
		
		$item_select[] = JHTML::_('select.option', '0', $this->handler->item_select_default, 'id', 'title');
		$item_select = array_merge($item_select, $this->handler->itemdata);
		
		$itemid = 0;
		if (!is_array($this->itemid))
			$itemid = $this->itemid;
			
		return JHTML::_('select.genericlist',  $item_select, 'itemid', 'class="inputbox" size="1"', 'id', 'title', $itemid);
	}
	
	function IncludeJS()
	{
		global $fst_comments_js;
		if (empty($fst_comments_js))
		{
			//echo "<script>\n";
			//include JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'assets'.DS.'js'.DS.'comments.js';	
			//echo "</script>\n";
			
			$document = JFactory::getDocument();
			$document->addScript( JURI::root().'components/com_fst/assets/js/comments.js' );
			
			$fst_comments_js = 1;
			
			echo "<div id='comments_urls' ";
			echo "url='" . htmlentities(FSTRoute::x('&task=XXTASKXX&commentid=XXCIDXX&uid=XXUIDXX',false)) . "' ";
			echo "refresh='" . htmlentities(FSTRoute::x('&task=modinner&ident=XXXIDXX&published=XXPXX',false)) . "' ";
			echo "wait='" . htmlentities(JText::_('PLEASE_WAIT')) . "' ";
			echo "ident='" . JRequest::getVar("ident",0) . "' ";
			echo "published='" . JRequest::getVar("published",0) . "' ";
			echo "deleted='" . JText::_("COMMENT_DELETED") . "' ";
			echo "></div>";
		}
	}
	
	function GetCount($id,$raw = true)
	{
		if (!$this->use_comments)
			return "";
		
		if (empty($this->_counts))
			$this->GetCommentCounts();
			
		if ($raw)
		{
			if (array_key_exists($id,$this->_counts))
				return $this->_counts[$id]['count'];
			return "";
		}

		if (empty($this->_counts[$id]))
			return JText::_("NO_COMMENTS");
			
		$count =  $this->_counts[$id]['count'];
		if ($count == 1)
			return "1 " . JText::_("COMMENT");
			
		return $count . " " . JText::_("COMMENTS");
	}
	
	function GetModLink()
	{
		return JRoute::_('index.php?option=com_fst&view=admin&layout=moderate');	
	}
	
	function GetModerateComments()
	{
		if (!$this->use_comments)
			return;
		
		$db = JFactory::getDBO();
		
		$qry = "SELECT * FROM " . $this->table . " WHERE ";
		
		$where = array();
		$order = array();
		
		$this->ident = JRequest::getVar('ident',0);
		$this->published = JRequest::getVar('published',0);
		
		if ($this->ident > 0)
			$where[] = "ident='".FSTJ3Helper::getEscaped($db, $this->ident)."'";	
		
		$where[] = "published='".FSTJ3Helper::getEscaped($db, $this->published)."'";
		$qry .= implode(" AND ",$where);
		$qry .= " ORDER BY ident, itemid, created DESC";
		$db->setQuery($qry);
		$this->_data = &$db->loadAssocList();
		$this->PopulateCustomFields();
		
		// sort data into a tree
		
		$newdata = array();
		
		$itemids = array();
		
		foreach($this->_data as $item)
		{
			$newdata[$item['ident']][$item['itemid']][] = $item;
			$itemids[$item['ident']][$item['itemid']] = $item['itemid'];
		}
		
		foreach($itemids as $ident => $items)
		{
			$this->handlers[$ident]->GetItemData($items);
		}
		
		$this->_data = $newdata;
		
		//print_p($this->customfields);
	}
	
	function PopulateCustomFields()
	{
		foreach($this->_data as &$item)
		{
			foreach($this->customfields as $id => &$field)
				$item['custom_'.$id] = "";
			
			if (array_key_exists('custom',$item))
			{
				if (!$item['custom'])
				{
					unset($item['custom']);
					continue;
				}
				
				$custom = unserialize($item['custom']);	
				
				foreach ($custom as $id => $value)
				{
					$item['custom_'.$id] = $value;
				}
				
				unset($item['custom']);
			}
		}	
	}

	function DisplayModerateInner()
	{
		$this->GetModerateComments();		
		include $this->tmplpath . DS .'modadmin_inner.php';
		exit;		
	}
	
	function DisplayModerate()
	{
		$this->GetModerateComments();		
		
		$idents = array();
		$idents[] = JHTML::_('select.option', '-1', JText::_("ALL"), 'id', 'title');
		$db	= & JFactory::getDBO();
		$db->SetQuery("SELECT ident FROM #__fst_comments WHERE published = 0 GROUP BY ident");
		$identlist = $db->loadAssocList('ident');
		foreach($identlist as $ident => $temp)
			$idents[] = JHTML::_('select.option', $ident, $this->handlers[$ident]->GetDesc(), 'id', 'title');
				
		$this->identselect = JHTML::_('select.genericlist',  $idents, 'ident', ' class="inputbox" size="1" onchange="fst_moderate_refresh()"', 'id', 'title', $this->ident);
		
		$whatcomm = array();
		$whatcomm[] = JHTML::_('select.option', '0', JText::_("FOR_MODERATION"), 'id', 'title');
		$whatcomm[] = JHTML::_('select.option', '2', JText::_("DECLINED"), 'id', 'title');
		$this->whatcomm = JHTML::_('select.genericlist',  $whatcomm, 'published', ' class="inputbox" size="1" onchange="fst_moderate_refresh()"', 'id', 'title', $this->published);

		include $this->tmplpath . DS .'modadmin.php';
	}
	
	function PerPage($count)
	{
		$this->perpage = 999999;
		if ($count > 1)
			$this->perpage = $count;	
	}
}