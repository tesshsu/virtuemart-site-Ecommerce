<?php

defined('_JEXEC') or die('Restricted access');


class JButtonImportToolBarButton extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ImportToolBarButton';

	function fetchButton( $type='ImportToolBarButton', $name = '', $url = '', $text = '', $task = '')
	{

    $class	= $this->fetchIconClass($name);
	
    $html	= "<form style='margin: 0;' id='form_fmalertcookies' name='form_fmalertcookies' action='".$url."&fmalertcookiestask=import' method='post' enctype='multipart/form-data'>";	
    $html .= "<a href='#' onclick=\"document.getElementById('file_fmalertcookies').click();\" class='btn btn-info btn-small'>";
    $html .= "<span class='".$class."' title='".$text."'>";
    $html .= "</span>".$text."</a>";
	$html .= "<input id='file_fmalertcookies' onchange=\"document.getElementById('form_fmalertcookies').submit();\" name='id_fmalertcookies' type='file' style='display: none;' />";
	$html .= "</form>";

    return $html;
	}

	// fetchId
	public function fetchId( $type = 'ImportToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}

}
	
class JToolbarButtonImportToolBarButton extends JButtonExportToolBarButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ImportToolBarButton';

	function fetchButton( $type='ImportToolBarButton', $name = '', $url = '', $text = '', $task = '')
	{

    $class	= $this->fetchIconClass($name);

    $html	= "<form style='margin: 0;' id='form_fmalertcookies' name='form_fmalertcookies' action='".$url."&fmalertcookiestask=import' method='post' enctype='multipart/form-data'>";	
    $html .= "<a href='#' onclick=\"document.getElementById('file_fmalertcookies').click();\" class='btn btn-info btn-small'>";
    $html .= "<span style='margin-right: 5px;' class='".$class."' title='".$text."'>";
    $html .= "</span>".$text."</a>";
	$html .= "<input id='file_fmalertcookies' onchange=\"document.getElementById('form_fmalertcookies').submit();\" name='id_fmalertcookies' type='file' style='display: none;' />";
	$html .= "</form>";

    return $html;
	}

	// fetchId
	public function fetchId( $type = 'ImportToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}

}	