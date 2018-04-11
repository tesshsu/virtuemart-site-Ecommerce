<?php

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @copyright Copyright (C) 2012 iStraxx - All rights reserved.
 * @license license.txt Proprietary License. This code belongs to iStraxx UG
 * You are not allowed to distribute or sell this code. You bought only a license to use it for ONE virtuemart installation.
 * You are not allowed to modify this code. *
 */
if (!class_exists ('ShopFunctions')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
}

/**
 * @copyright    Copyright (C) 2009 Open Source Matters. All rights reserved.
 * @license    GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined ('JPATH_BASE') or die();

/**
 * Renders a multiple item select element
 *
 */

class JElementDeveloperInfo extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */

	var $_name = 'developerinfo';

	function fetchElement ($name, $value, &$node, $control_name) {

		$linesHeight = '';
		$width = '';
		$height = '';
		$contactlink = "http://extensions.virtuemart.net/index.php?option=com_virtuemart&view=vendor&layout=contact&virtuemart_vendor_id=1";
		$logo = "";
		$developer = 'iStraxx';
		$manlink = "http://extensions.virtuemart.net/option=com_content&view=article&id=17";
		$title = vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_MANUAL');
		$html = vmText::_ ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_DESCRIPTION');
		$html .= "<br/>";
		$html .= self::displayLinkButton (vmText::sprintf ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_CONTACT', $developer), $contactlink, $logo, $width, $height, $linesHeight);
		$html .= " -";
		$html .= self::displayLinkButton (vmText::sprintf ('VMUSERFIELD_ISTRAXX_EUVATCHECKER_MANUAL', $title), $manlink, $logo, $width, $height, $linesHeight);

		return $html;
	}


	static function displayLinkButton ($title, $link, $bgrndImage, $width, $height, $linesHeight, $additionalStyles = '') {

		//$lineHeight = ((int)$height)/$lines;
		//vmdebug('displayLinkButton '.$height.' '.$lineHeight);
		$html = '<a  title="' . $title . '" href="' . $link . '" target="_blank" >' . $title . '</a>';

		return $html;
	}

}
