<?php
defined ('_JEXEC') or die();
/**
 * @version 2.4.3
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2016 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */
define("CMCIC_CTLHMAC", "V1.04.sha1.php--[CtlHmac%s%s]-%s");
define("CMCIC_CTLHMACSTR", "CtlHmac%s%s");
define("CMCIC_CGI2_RECEIPT", "version=2\ncdr=%s");
define("CMCIC_CGI2_MACOK", "0");
define("CMCIC_CGI2_MACNOTOK", "1\n");
define("CMCIC_CGI2_FIELDS", "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*");
define("CMCIC_CGI1_FIELDS", "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s");
class cybermut_api {

	const CYBERMUT_RETURN_CODE_ACCEPTED = 'paiement';
	const CYBERMUT_RETURN_CODE_TEST_ACCEPTED = 'payetest';
	const CYBERMUT_RETURN_CODE_ERROR = 'Annulation';

	/**
	 *  Return CyberMut protocol version
	 *
	 * @param    none
	 * @return      string Protocol version
	 */
	static function getVersion () {

		$version = '3.0';

		return $version;
	}

	public function getCybermutUrl ($method) {

		$url = '';
		switch ($method->banque) {
			default:
			case 'mutuel':
				$url = ($method->test_production == 'production') ? 'https://paiement.creditmutuel.fr/test/paiement.cgi' : 'https://paiement.creditmutuel.fr/paiement.cgi';
				break;
			case 'cic':
				$url = ($method->test_production == 'production') ? 'https://ssl.paiement.cic-banques.fr/test/paiement.cgi' : 'https://ssl.paiement.cic-banques.fr/paiement.cgi';
				break;
			case 'obc':
				$url = ($method->test_production == 'production') ? 'https://ssl.paiement.banque-obc.fr/test/paiement.cgi' : 'https://ssl.paiement.banque-obc.fr/paiement.cgi';
				break;
		}
		return $url;
	}

	static function getMAC ($fields, $key) {

		if (TRUE) {
			$data = sprintf ('%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*', $fields['TPE'], $fields['date'], $fields['montant'], $fields['reference'], $fields['texte-libre'], $fields['version'], $fields['lgue'], $fields['societe'], $fields['mail'], "", "", "", "", "", "", "", "", "");
		} else {
			$data = sprintf ('%s*%s*%s*%s*%s*%s*%s*%s*', $fields['TPE'], $fields['date'], $fields['montant'], $fields['reference'], $fields['texte-libre'], $fields['version'], $fields['lgue'], $fields['societe']);
		}

		return self::_CMCIC_hmac ($data, $key);
	}

	/**
	// RFC 2104 HMAC implementation for PHP 4 >= 4.3.0 - Creates a SHA1 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Adjusted from the md5 version by Lance Rushing .

	// Impl�mentation RFC 2104 HMAC pour PHP 4 >= 4.3.0 - Cr�ation d'un SHA1 HMAC.
	// Elimine l'installation de mhash pour le calcul d'un HMAC
	// Adapt�e de la version MD5 de Lance Rushing.
	 */
	static function _CMCIC_hmac ($data, $key) {

		return strtolower (hash_hmac ("sha1", $data, self::_getUsableKey ($key)));

		$length = 64; // block length for SHA1
		if (strlen ($key) > $length) {
			$key = pack ("H*", sha1 ($key));
		}
		$key = str_pad ($key, $length, chr (0x00));
		$ipad = str_pad ('', $length, chr (0x36));
		$opad = str_pad ('', $length, chr (0x5c));
		$k_ipad = $key ^ $ipad;
		$k_opad = $key ^ $opad;

		return sha1 ($k_opad . pack ("H*", sha1 ($k_ipad . $data)));

	}

	// ----------------------------------------------------------------------------
	//
	// Fonction / Function : _getUsableKey
	//
	// Renvoie la clé dans un format utilisable par la certification hmac
	// Return the key to be used in the hmac function
	//
	// ----------------------------------------------------------------------------

	private static function _getUsableKey ($key) {

		$hexStrKey = substr ($key, 0, 38);
		$hexFinal = "" . substr ($key, 38, 2) . "00";

		$cca0 = ord ($hexFinal);

		if ($cca0 > 70 && $cca0 < 97) {
			$hexStrKey .= chr ($cca0 - 23) . substr ($hexFinal, 1, 1);
		} else {
			if (substr ($hexFinal, 1, 1) == "M") {
				$hexStrKey .= substr ($hexFinal, 0, 1) . "0";
			} else {
				$hexStrKey .= substr ($hexFinal, 0, 2);
			}
		}

		return pack ("H*", $hexStrKey);
	}

	static function handleResponseMAC ($data, $key) {

		//if (((int) $this->getVersion()) >= 3) {
		if (!array_key_exists ('numauto', $data)) {
			$data['numauto'] = "";
		}
		if (!array_key_exists ('motifrefus', $data)) {
			$data['motifrefus'] = "";
		}
		if (!array_key_exists ('originecb', $data)) {
			$data['originecb'] = "";
		}
		if (!array_key_exists ('bincb', $data)) {
			$data['bincb'] = "";
		}
		if (!array_key_exists ('hpancb', $data)) {
			$data['hpancb'] = "";
		}
		if (!array_key_exists ('ipclient', $data)) {
			$data['ipclient'] = "";
		}
		if (!array_key_exists ('originetr', $data)) {
			$data['originetr'] = "";
		}
		if (!array_key_exists ('veres', $data)) {
			$data['veres'] = "";
		}
		if (!array_key_exists ('pares', $data)) {
			$data['pares'] = "";
		}
		//$string = sprintf ('%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*', $data['TPE'], $data['date'], $data['montant'], $data['reference'], $data['texte-libre'], '3.0', $data['code-retour'], $data['cvx'], $data['vld'], $data['brand'], $data['status3ds'], $data['numauto'], $data['motifrefus'], $data['originecb'], $data['bincb'], $data['hpancb'], $data['ipclient'], $data['originetr'], $data['veres'], $data['pares']);
		$cgi2_fields = sprintf (CMCIC_CGI2_FIELDS, $data['TPE'],
			$data["date"],
			$data['montant'],
			$data['reference'],
			$data['texte-libre'],
			'3.0',
			$data['code-retour'],
			$data['cvx'],
			$data['vld'],
			$data['brand'],
			$data['status3ds'],
			$data['numauto'],
			$data['motifrefus'],
			$data['originecb'],
			$data['bincb'],
			$data['hpancb'],
			$data['ipclient'],
			$data['originetr'],
			$data['veres'],
			$data['pares']
		);

		/*
	  } else {
		  $string = sprintf('%s%s+%s+%s+%s+%s+%s+%s+', $data['retourPLUS'], $data['TPE'], $data['date'], $data['montant'], $data['reference'], $data['texte-libre'], $this->getVersion(), $data['code-retour']);
	  }
  */
		return strtoupper (self::_CMCIC_hmac ($cgi2_fields, $key));
	}

	public static function getAckResponse () {

		$receipt = "version=2\ncdr=0";
		return $receipt;
	}

	public static function getNackResponse () {

		$receipt = "version=2\ncdr=1";
		return $receipt;
	}


	function getCMCICurl ($method) {

		$url = '';
		switch ($method->banque) {
			default:
			case 'CM':
				$url = $method->test_production == 'test' ? 'https://paiement.creditmutuel.fr/test/paiement.cgi' : 'https://paiement.creditmutuel.fr/paiement.cgi';
				break;
			case 'CIC':
				$url = $method->test_production == 'test' ? 'https://ssl.paiement.cic-banques.fr/test/paiement.cgi' : 'https://ssl.paiement.cic-banques.fr/paiement.cgi';
				break;
			case 'OBC':
				$url = $method->test_production == 'test' ? 'https://ssl.paiement.banque-obc.fr/test/paiement.cgi' : 'https://ssl.paiement.banque-obc.fr/paiement.cgi';
				break;
		}
		return $url;
	}

}

?>
