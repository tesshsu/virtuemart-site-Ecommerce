<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * The reCAPTCHA server URL's
 */
define("fst_RECAPTCHA_API_SERVER", "http://api.recaptcha.net");
define("fst_RECAPTCHA_API_SECURE_SERVER", "https://api-secure.recaptcha.net");
define("fst_RECAPTCHA_VERIFY_SERVER", "api-verify.recaptcha.net");

// Captcha stuff
global $fst_publickey,$fst_privatekey;
$fst_publickey = FST_Settings::get('recaptcha_public');
$fst_privatekey = FST_Settings::get('recaptcha_private');

if (!$fst_publickey) $fst_publickey = "6LcQbAcAAAAAAHuqZjftCSvv67KiptVfDztrZDIL";
if (!$fst_privatekey) $fst_privatekey = "6LcQbAcAAAAAAMBL5-rp10P3UQ31kpRYLhUFTsqK ";


if (!function_exists ("fst__recaptcha_qsencode"))
	require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'recaptcha_api.php');

?>
