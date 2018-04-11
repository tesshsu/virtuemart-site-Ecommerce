<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
if (file_exists(JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'helper.php'))
{
	require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'j3helper.php' );
	require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'helper.php' );
	require_once( JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'comments.php' );

	$css = JRoute::_( "index.php?option=com_fst&view=css&layout=default" );
	$document = JFactory::getDocument();
	$document->addStyleSheet($css);
	
	FST_Helper::IncludeJQuery();
	$document->addScript( JURI::base().'components/com_fst/assets/js/jquery.autoscroll.js' );

	$db = JFactory::getDBO();

	JHTML::_('behavior.modal', 'a.fst_modal');
	
	//JHTML::_('behavior.mootools');

	$prodid = $params->get('prodid');
	$dispcount = $params->get('dispcount');
	$listtype = $params->get('listtype');
	$maxlength = $params->get('maxlength');
	$showmore = $params->get('show_more');
	$showadd = $params->get('show_add');
	$maxheight = $params->get('maxheight');

	$comments = new FST_Comments("test",$prodid);
	$comments->template = "comments_testmod";
	if (FST_Settings::get('comments_testmod_use_custom'))
		$comments->template_type = 2;
	
	if ($listtype == 0)
		$comments->opt_order = 2;

	$comments->opt_no_mod = 1;
	$comments->opt_no_edit = 1;
	$comments->opt_show_add = 0;
	$comments->opt_max_length = $maxlength;
	$comments->opt_disable_pages = 1;
	
	require( JModuleHelper::getLayoutPath( 'mod_fst_test' ) );
}