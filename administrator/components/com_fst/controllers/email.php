<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FstsControllerEmail extends FstsController
{

	function __construct()
	{
		parent::__construct();
	}

	function cancellist()
	{
		$link = 'index.php?option=com_fst&view=fsts';
		$this->setRedirect($link, $msg);
	}

	function edit()
	{
		JRequest::setVar( 'view', 'email' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model =& $this->getModel('email');

        $post = JRequest::get('post');
        
		if ($post['ishtml'])
		{
			$post['body'] = JRequest::getVar('body_html', '', 'post', 'string', JREQUEST_ALLOWRAW);	
			unset($post['body_html']);	
		}
		
		if ($model->store($post)) {
			$msg = JText::_("EMAIL_TEMPLATE_SAVED");
		} else {
			$msg = JText::_("ERROR_SAVING_EMAIL_TEMPLATE");
		}

		$link = 'index.php?option=com_fst&view=emails';
		$this->setRedirect($link, $msg);
	}

	function cancel()
	{
		$msg = JText::_("OPERATION_CANCELLED");
		$this->setRedirect( 'index.php?option=com_fst&view=emails', $msg );
	}

}



