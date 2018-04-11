<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class VMMigrateHelperFilesystem {
	
	public $mode;
	protected $source_ftp;
	public $source_path;
	protected $ftp_root;
	static $_instance;
	protected $_now;

	function __construct() {
		$params = JComponentHelper::getParams('com_vmmigrate');
		$ftp_active = $params->get('ftp_enable', 0);
		$jnow = JFactory::getDate();
		$this->_now = $jnow->toSql();
		
		if ($ftp_active) {
			$ftp_root = $params->get('ftp_root');
			$this->ftp_root = $ftp_root;
			$this->mode = 'ftp';
			$this->source_ftp = self::getSourceFtp();
		} else {
			$source_path = $params->get('source_path','foo');
			$source_path = trim($source_path);
			$this->mode = 'path';
			$this->source_path = rtrim( $source_path, "/" );
		}
	}

    public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new VMMigrateHelperFilesystem();
		} else {
			//We store in UTC and use here of course also UTC
			$jnow = JFactory::getDate();
			self::$_instance->_now = $jnow->toSql();
		}
		return self::$_instance;
	}
	
	public static function getSourceFtp() {

		$params = JComponentHelper::getParams('com_vmmigrate');

        $ftp_active = $params->get('ftp_enable', 0);
        $ftp_host = trim($params->get('ftp_host', ''));
        $ftp_port = trim($params->get('ftp_port', ''));
        $ftp_user = trim($params->get('ftp_user', ''));
        $ftp_pass = trim($params->get('ftp_pass', ''));
        $ftp_root = trim($params->get('ftp_root', ''));
		$ftp_root = rtrim( trim($ftp_root), "/" );
		$ftp_root = '/'.ltrim( $ftp_root, "/" );
		//$ftp_options = array('type'=>'FTP_AUTOASCII'); //FTP_AUTOASCII|FTP_ASCII|FTP_BINARY
		$ftp_options = array();
		
		//type=>[FTP_AUTOASCII|FTP_ASCII|FTP_BINARY]
		error_reporting(0);

		jimport('joomla.client.ftp');
		if (class_exists('JClientFtp')) {
			$source_ftp = JClientFtp::getInstance($ftp_host, $ftp_port,$ftp_options,$ftp_user,$ftp_pass);
		} else {
			$source_ftp = JFTP::getInstance($ftp_host, $ftp_port,array(),$ftp_user,$ftp_pass);
		}
		return $source_ftp;
	}

	public static function isValidConnection() {
        $helper = self::getInstance();

		if ($helper->mode == 'ftp') {
			if (!$helper->source_ftp->isConnected()) {
				return false;
			}
		} else {
			if (!JFolder::exists($helper->source_path)) {
				return false;
			}
		}

		if ($helper->FileExists('/configuration.php')) {
			return true;
		} else {
			return false;
		}
	}
	
	public function FileExists($path) {
		if ($this->mode == 'ftp') {
			$filename = basename($path);
			$folder = str_replace($filename,'',$path);
			$files = $this->FilesNames($folder);
			return in_array(basename($path),$files);
		} else {
			return JFile::exists($this->source_path.$path);
		}
	}
	
	public function FilePathExists($path) {
		if ($this->mode == 'ftp') {
			$filename = basename($path);
			$folder = str_replace($filename,'',$path);
			$files = $this->source_ftp->listNames($path);
			return in_array(basename($path),$files);
		} else {
			return JFile::exists($path);
		}
	}
	
	public function FolderExists($path) {
		if ($this->mode == 'ftp') {
			$basePath = dirname($path);
			$folder = basename($path);
			$folders = $this->Folders($basePath);
			return in_array($folder,$folders);
		} else {
			return JFolder::exists($this->source_path.$path);
		}
	}
	
	public function FolderPathExists($path) {
		if ($this->mode == 'ftp') {
			$basePath = dirname($path);
			$folder = basename($path);
			$folders = $this->source_ftp->listNames($path,'folders');
			return in_array($folder,$folders);
		} else {
			return JFolder::exists($path);
		}
	}
	
	public function FilesNames($path) {
		if ($this->mode == 'ftp') {
			if ($files = $this->source_ftp->listNames($this->ftp_root.$path)) {
				return $files;
			}
		} else {
			if ($files = JFolder::Files($this->source_path.$path)) {
				return $files;
			}
		}
		return array();
	}
	
	public function Files($path) {
		if ($this->mode == 'ftp') {
			return $this->source_ftp->listDetails($this->ftp_root.$path,'files');
		} else {
			return JFolder::Files($this->source_path.$path);
		}
	}
	
	public function Folders($path) {
		if ($this->mode == 'ftp') {
			if ($folders = $this->source_ftp->listNames($this->ftp_root.$path,'folders')) {
				return $folders;
			}
		} else {
			if ($folders =  JFolder::Folders($this->source_path.$path)) {
				return $folders;
			}
		}
		return array();
	}
	
	public function IncludeFile($path) {
		
		//Legacy compatibility
		if (!defined('_VALID_MOS')) {define ('_VALID_MOS',1);}
		
		if ($this->mode == 'ftp') {
			$filename = basename($path);
			$tempPath = JFactory::getConfig()->get('tmp_path');
			if (!JFolder::exists($tempPath.'/migrator')) {
				JFolder::create($tempPath.'/migrator');
			}
			$localFile = $tempPath.'/migrator/'.$filename;
			//Check if the file was recently downloaded
			if (JFile::exists($localFile)) {
				$time = @filemtime($localFile);
				if (($time + 900) < time()) { //File is outdated, delete and get a fresh copy
					@unlink($localFile);
					$this->source_ftp->get($tempPath.'/migrator/'.$filename,$this->ftp_root.$path);
				}
			} else {
				$this->source_ftp->get($tempPath.'/migrator/'.$filename,$this->ftp_root.$path);
			}
			if (JFile::exists($localFile)) {
				include_once($localFile);
				return true;
			} else {
				return false;
			}
		} else {
			$localFile = $this->source_path.$path;
			if (JFile::exists($localFile)) {
				include_once($localFile);
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function ReadFile($path) {
		if ($this->mode == 'ftp') {
			$buffer = '';
			$this->source_ftp->read($this->ftp_root.$path,$buffer);
			return $buffer;
		} else {
			if (JFile::exists($this->source_path.$path)) {
				//return $this->source_path.$path;
				$buffer = file_get_contents($this->source_path.$path);
				$buffer = str_replace('<?php','',$buffer);
				$buffer = str_replace('?>','',$buffer);
				return $buffer.'';
			}
			return '';
			//return JFile::read($this->source_path.$path);
		}
	}
	
	public function ReadXmlFile($path) {
		if ($this->mode == 'ftp') {
			$buffer = '';
			$this->source_ftp->read($this->ftp_root.$path,$buffer);
			return simplexml_load_string($buffer);
		} else {
			if (JFile::exists($this->source_path.$path)) {
				return simplexml_load_file($this->source_path.$path);
			}
			return '';
		}
	}
	
	public function CopyFile($src,$dest) {
		if (!JFolder::exists(dirname($dest))) {
			JFolder::create(dirname($dest));
		}
		if ($this->mode == 'ftp') {
			return $this->source_ftp->get($dest,$this->ftp_root.$src);
		} else {
			return JFile::copy($this->source_path.$src,$dest);
		}
	}
	
	public function CopyPathFile($src,$dest) {
		if (!JFolder::exists(dirname($dest))) {
			JFolder::create(dirname($dest));
		}
		if ($this->mode == 'ftp') {
			return $this->source_ftp->get($dest,$src);
		} else {
			return JFile::copy($src,$dest);
		}
	}
	
	public function CopyFolder($src,$dest) {
		if ($this->mode == 'ftp') {
			$files = $this->Files($src);
			foreach ($files as $file) {
				$this->source_ftp->get($dest,$this->ftp_root.$src);
			}
			return $this->source_ftp->get($dest,$this->ftp_root.$src);
		} else {
			return JFolder::copy($this->source_path.$src,$dest,'',true);
		}
	}
	

}

?>