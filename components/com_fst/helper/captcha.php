<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FST_Captcha
{
	function GetCaptcha()
	{
		$usecaptcha = FST_Settings::get( 'captcha_type' );
		
		if ($usecaptcha == "")
			return "";
		if ($usecaptcha == "fsj")
			return "<img src='" . FSTRoute::x("index.php?option=com_fst&task=captcha_image&random=" . rand(0,65535)) . "' /><input id='security_code' name='security_code' type='text' style='position: relative; left: 3px;'/>";
		if ($usecaptcha == "recaptcha")
		{
			require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'recaptcha.php');
			$error = "";
			global $fst_publickey,$fst_privatekey;
			return fst_recaptcha_get_html($fst_publickey, $error);		
		}
		return "";
	}
	
	function ValidateCaptcha()
	{
		$usecaptcha = FST_Settings::get( 'captcha_type' );
		if ($usecaptcha == "")
			return true;

		if ($usecaptcha == "fsj")
		{
			if(($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code'])) ) { 
				//unset($_SESSION['security_code']);
				return true;
			}
			return false;
		}
		if ($usecaptcha == "recaptcha")
		{
			require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'recaptcha.php');
			global $fst_publickey,$fst_privatekey;
			if (array_key_exists("recaptcha_challenge_field",$_POST))
			{
				$resp = fst_recaptcha_check_answer ($fst_privatekey,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]);
			} else {
				$resp = null;	
			}
			if ($resp && $resp->is_valid)
			{
				return true;	
			} else {
				return false;	
			}
		}
		return true;
	}
	
	function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}
	
	function GetImage($width='150',$height='40',$characters='6') {
		$this->font = JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'assets'.DS.'fonts'.DS.'captcha.ttf';
		
		$code = $this->generateCode($characters);
		$_SESSION['security_code'] = $code;
		$code2 = "";
		for ($i = 0; $i < strlen($code); $i++)
			$code2 .= substr($code,$i,1) . " ";
		$code = $code2;
		/* font size will be 75% of the image height */
		$font_size = $height * 0.60;
		$image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 10, 20, 50);
		$noise_color = imagecolorallocate($image, 150, 160, 100);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/300; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
	}
}