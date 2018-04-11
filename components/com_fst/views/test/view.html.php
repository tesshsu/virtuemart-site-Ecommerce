<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;


jimport( 'joomla.application.component.view');
jimport( 'joomla.mail.helper' );
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'paginationex.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'email.php');

//JHTML::_('behavior.mootools');
	
require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments.php');

class FstViewTest extends JViewLegacy
{
	var $product = null;

    function display($tpl = null)
    {
		$document = JFactory::getDocument();
		if (FST_Helper::Is16())
			JHtml::_('behavior.framework');

		$mainframe = JFactory::getApplication();
		
		JHTML::_('behavior.tooltip');
        JHTML::_('behavior.modal', 'a.fst_modal');

		$user = JFactory::getUser();
		$userid = $user->id;
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__fst_user WHERE user_id = '".FSTJ3Helper::getEscaped($db, $userid)."'";
		$db->setQuery($query);
		$this->_permissions = $db->loadAssoc();
		$this->params =& FST_Settings::GetViewSettingsObj('test');
		$this->test_show_prod_mode = $this->params->get('test_show_prod_mode','accordian');
		$this->test_always_prod_select = $this->params->get('test_always_prod_select','0');
		$layout = JRequest::getVar('layout','');
			
		$this->prodid = JRequest::getVar('prodid');
		if ($this->prodid == "")
			$this->prodid = -1;
				
		$this->products = $this->get('Products');
		//print_p($this->products);
		if (count($this->products) == 0)
			$this->prodid = 0;
		
		$this->comments = new FST_Comments("test",$this->prodid);
		if ($this->prodid == -1)
			$this->comments->opt_show_posted_message_only = 1;

		$onlyprodid = JRequest::getInt('onlyprodid');
		if ($onlyprodid > 0)
		{
			$this->comments->itemid = (int)$onlyprodid;
			$this->comments->show_item_select = false;
		}

		if ($this->params->get('hide_add',0))
		{
			$this->comments->can_add = 0;
		}
			
		if ($layout == "create")
		{
			$this->setupCommentsCreate();	
		}
		
		if ($this->prodid != -1)
		{
			if ($this->test_always_prod_select)
			{
				$this->comments->show_item_select = 1;
			} else {
				$this->comments->show_item_select = 0;
			}
		}
		
		if ($this->comments->Process())
			return;
			
		if ($layout == "create")
			return $this->displayCreate();
			
		if ($this->prodid != -1)
		{
			return $this->displaySingleProduct();	
		}

		return $this->displayAllProducts();
		
 	}
	
	function setupCommentsCreate()
	{
		$this->comments->opt_display = 0;
		$this->comments->comments_hide_add = 0;
		$this->comments->opt_show_form_after_post = 1;
		$this->comments->opt_show_posted_message_only = 1;
	}
	
	function displayCreate()
	{
		$this->tmpl = JRequest::getVar('tmpl','');
		parent::display();	
	}
	
	function displaySingleProduct()
	{
		$this->product = $this->get('Product');
		$this->products = $this->get('Products');	
		
		FST_Helper::TrSingle($this->product);
 		FST_Helper::Tr($this->products);
		
        $mainframe = JFactory::getApplication();
		$pathway =& $mainframe->getPathway();
		if (FST_Helper::NeedBaseBreadcrumb($pathway, array( 'view' => 'test' )))	
			$pathway->addItem(JText::_('TESTIMONIALS'), FSTRoute::x( 'index.php?option=com_fst&view=test' ) );
        $pathway->addItem($this->product['title']);
		
		// no product then general testimonials
		if (!$this->product && count($this->products) > 0)
		{
			$this->product = array();
			$this->product['title'] = JText::_('GENERAL_TESTIMONIALS');	
			$this->product['id'] = 0;
			$this->product['description'] = '';
			$this->product['image'] = '/components/com_fst/assets/images/generaltests.png';
		}
		
		if ($this->test_always_prod_select)
		{
			$this->comments->show_item_select = 1;
		} else {
			$this->comments->show_item_select = 0;
		}
		
		$this->comments->PerPage(FST_Settings::Get('test_comments_per_page'));
				
		parent::display("single");
	}
	
	function displayAllProducts()
	{
		$this->products = $this->get('Products');
		if (!is_array($this->products))
			$this->products = array();
 		FST_Helper::Tr($this->products);
		
		$this->showresult = 1;
		
        $mainframe = JFactory::getApplication();
        $pathway =& $mainframe->getPathway();
		if (FST_Helper::NeedBaseBreadcrumb($pathway, array( 'view' => 'test' )))	
			$pathway->addItem(JText::_('TESTIMONIALS'), FSTRoute::x( 'index.php?option=com_fst&view=test' ) );
 
		if (FST_Settings::get('test_allow_no_product'))
		{
			$noproduct = array();
			$noproduct['id'] = 0;
			$noproduct['title'] = JText::_('GENERAL_TESTIMONIALS');
			$noproduct['description'] = '';
			$noproduct['image'] = '/components/com_fst/assets/images/generaltests.png';
			$this->products = array_merge(array($noproduct), $this->products);
		}
		
		if ($this->test_show_prod_mode != "list")
		{
			$idlist = array();
			if (count($this->products) > 0)
			{
				foreach($this->products as &$prod) 
				{
					$prod['comments'] = array();
					$idlist[] = $prod['id'];	
				}
			}
			
			// not in normal list mode, get comments for each product
			
			$this->comments->itemid = $idlist;
			
			$this->comments->GetComments();
						
			foreach($this->comments->_data as &$data)
			{
				if ($data['itemid'] > 0)
					$this->products[$data['itemid']]['comments'][] =& $data;
			}
		}
		
		parent::display();
	}
}

