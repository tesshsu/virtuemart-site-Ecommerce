<?php

defined('_JEXEC') or die('Restricted access');

class JButtonDonToolBarButton extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'DonToolBarButton';

	public function fetchButton( $type='DonToolBarButton', $name = '', $lang = "fr_FR")
	{
		$file = "https://www.paypalobjects.com/".$lang."/i/btn/btn_donate_SM.gif";
		$file_headers = @get_headers($file);	
		
		if($file_headers[0] == 'HTTP/1.0 404 Not Found') {
			$lang = "en_GB";
		}	
			
		$html = "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_blank'>";
		$html .="<input type='hidden' name='cmd' value='_s-xclick'>";
		$html .="<input type='hidden' name='encrypted' 
		value='-----BEGIN PKCS7-----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-----END PKCS7-----'>";
		$html .= "<input type='image' src='https://www.paypalobjects.com/".$lang."/i/btn/btn_donate_SM.gif' border='0' name='submit' alt='PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !'>";
		$html .= "<img alt='' border='0' src='https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif' width='1' height='1'>";
		$html .= "</form>";				
		return $html;
	}

	// fetchId
	public function fetchId( $type = 'DonToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
}

// JToolbarButton
class JToolbarButtonDonToolBarButton extends JButtonExportToolBarButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'DonToolBarButton';

	public function fetchButton( $type='DonToolBarButton', $name = '', $lang = 'fr_FR')
	{
		$file = "https://www.paypalobjects.com/".$lang."/i/btn/btn_donate_SM.gif";
		$file_headers = @get_headers($file);	
		
		if($file_headers[0] == 'HTTP/1.0 404 Not Found') {
			$lang = "en_GB";
		}		

		$html = "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_blank'>";
		$html .="<input type='hidden' name='cmd' value='_s-xclick'>";
		$html .="<input type='hidden' name='encrypted' 
		value='-----BEGIN PKCS7-----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-----END PKCS7-----'>";
		$html .= "<input type='image' src='https://www.paypalobjects.com/".$lang."/i/btn/btn_donate_SM.gif' border='0' name='submit' alt='PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !'>";
		$html .= "<img alt='' border='0' src='https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif' width='1' height='1'>";
		$html .= "</form>";				
		return $html;
	}

	// fetchId
	public function fetchId( $type = 'DonToolBarButton', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
}