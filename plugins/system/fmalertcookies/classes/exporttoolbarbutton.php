<?php

defined('_JEXEC') or die('Restricted access');

class JButtonExportToolBarButton extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ExportToolBarButton';

	public function fetchButton( $type='ExportToolBarButton', $name = '', $url = '', $text = '', $task = '')
	{
		$class	=	$this->fetchIconClass( 'export' );
		
		$html = "<a href='".$url."&fmalertcookiestask=".$task."' class='btn btn-info btn-small'> ";
		$html .= "<span class='".$class."' title='".$text."'></span>";
		$html .= $text;
		$html .= "</a>";
		return $html;
	}

	// fetchId
	public function fetchId( $type = 'ExportToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
}

// JToolbarButton
class JToolbarButtonExportToolBarButton extends JButtonExportToolBarButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ExportToolBarButton';

	public function fetchButton( $type='ExportToolBarButton', $name = '', $url = '', $text = '', $task = '')
	{
		$class	= $this->fetchIconClass($name);
		
		$html = "<a href='".$url."&fmalertcookiestask=".$task."' class='btn btn-info btn-small'>";
		$html .= "<span style='margin-right: 5px;' class='".$class."' title='".$text."'></span>";
		$html .= $text;
		$html .= "</a>";
		return $html;
	}

	// fetchId
	public function fetchId( $type = 'ExportToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
}