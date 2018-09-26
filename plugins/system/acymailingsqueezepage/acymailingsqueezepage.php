<?php
/**
 * @copyright    Copyright (C) 2009-2016 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

// If we are on the admin side, we stop the process (we don't have squeezebox in back)
if(!JFactory::getApplication()->isSite()) return;

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

try{
	if(!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
		echo 'This plugin can not work without the AcyMailing Component';
		return;
	}
}catch(Exception $e){
	return;
}


class plgSystemAcymailingsqueezepage extends JPlugin{

	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$db = JFactory::getDBO();
			if(!ACYMAILING_J16){
				$db->setQuery("SELECT `params` FROM `#__plugins` WHERE `element` = 'acymailingsqueezepage' AND `folder`= 'system' LIMIT 1");
			}else{
				$db->setQuery("SELECT `params` FROM `#__extensions` WHERE `element` = 'acymailingsqueezepage' AND `type` = 'plugin' AND `folder`= 'system' LIMIT 1");
			}
			$params = $db->loadResult();
			$this->params = new acyParameter($params);
		}
	}

	function onAfterDispatch(){
		$app = JFactory::getApplication();

		// Only display the modal in the site section (nothing for admin)
		// If a cookie exists, we leave without displaying the modal
		if(!$app->isSite() || JRequest::getString('tmpl') == 'component' || JRequest::getVar('acymailingSubscriptionState', '', 'cookie', 'string')){
			return;
		}

		$option = JRequest::getCmd('option');
		//we don't display the popup if the user tries to modify his subscription or tries to unsubscribe... it does not make any sense
		if($option == 'acymailing' && JRequest::getCmd('ctrl') == 'user'){
			return;
		}

		if(!$this->params->get('dispmobile', 1) && $this->isMobile()){
			//Not for mobile
			return;
		}

		global $Itemid;
		$currentItemid = $Itemid;
		if(empty($currentItemid)){
			$jsite = JFactory::getApplication('site');
			$menus = $jsite->getMenu();
			$activeMenu = $menus->getActive();
			if(!empty($activeMenu->id)) $currentItemid = $activeMenu->id;
		}

		//Do we have other restrictions?
		//incpages is a list of Itemids or options (com_hikashop for example)
		$incpages = $this->params->get('incpages');
		if(!empty($incpages)){
			$allPages = explode(',', $incpages);
			$inc = false;
			foreach($allPages as $onePage){
				$onePage = strtolower(trim($onePage));
				if(is_numeric($onePage) && $onePage == $currentItemid){
					$inc = true;
				}elseif($onePage == $option) $inc = true;
			}
			if(!$inc) return;
		}

		$expages = $this->params->get('expages');
		if(!empty($expages)){
			$allPages = explode(',', $expages);
			$inc = true;
			foreach($allPages as $onePage){
				$onePage = strtolower(trim($onePage));
				if(is_numeric($onePage) && $onePage == $currentItemid){
					$inc = false;
				}elseif($onePage == $option) $inc = false;
			}
			if(!$inc) return;
		}

		// Clean parameters
		$moduleId = abs(intval($this->params->get('moduleid', 1)));
		$timer = abs(intval($this->params->get('timer', 0))) * 1000;
		$interval = intval($this->params->get('interval', 7)) * 3600 * 24;
		//$on = 'open'; // or 'close';
		//$message = 'Would you like to subscribe to our newsletter before leaving? If so, please stay on this page.';

		// Get logged in user
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		if(!empty($user->id)){
			$subscriberClass = acymailing_get('class.subscriber');
			$subid = intval($subscriberClass->subid($user->id));

			// Retrieve the required lists
			$lists = $this->params->get('lists', '');
			if(!empty($lists) && strtolower(trim($lists)) != 'all'){
				$ll = array();
				$lists = explode(',', $lists);
				foreach($lists as $list){
					$list = max(intval(trim($list)), 0);
					if(!empty($list)){
						$ll[] = $list;
					}
				}
				$lists = implode(',', $ll);
				unset($ll);
			}else{
				$lists = '';
			}

			// Retrieve the lists to which the user is registered
			$wherelists = ($lists == '') ? '' : "AND `listid` IN ($lists)";
			$query = "SELECT `listid`
					FROM `#__acymailing_listsub`
					WHERE `subid` = $subid
					$wherelists";
			$db->setQuery($query);
			$lists = $db->loadRowList();

			// If the user is registered to one or more of the required lists, we leave without displaying the modal
			if(!empty($lists)){
				return;
			}
		}

		// Load modal script
		JHTML::_('behavior.modal');

		// Get Itemid
		$config = acymailing_config();
		$itemId = $config->get('itemid', 0);
		//We avoid an error if the user didn't update to 4.0.0
		if($config->get('version') < '4.0.0') return;

		// Get modal's dimensions from acymailing module
		$db->setQuery("SELECT `params` FROM `#__modules` WHERE `id` = $moduleId LIMIT 1");
		$modParams = $db->loadResult();
		$modParams = new acyParameter($modParams);
		$width = abs(intval($modParams->get('boxwidth', 250)));
		$height = abs(intval($modParams->get('boxheight', 200)));

		//Do we have a special design?
		$design = $this->params->get('design');
		if(!empty($design)){
			//Do we have a special language design?
			$currentLanguage = JFactory::getLanguage();
			$currentLang = str_replace('-', '_', strtolower($currentLanguage->getTag()));
			if(!empty($currentLang) && file_exists(ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'_'.$currentLang.'.php')){
				$design = $design.'_'.$currentLang;
			}

			if(!empty($currentItemid) && file_exists(ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'_'.$currentItemid.'.php')){
				$design = $design.'_'.$currentItemid;
			}
			//Check if we have a mobile version... and if so, we use it if we are on a mobile devise
			$mobilefile = ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'_mobile.php';

			if(file_exists($mobilefile) && $this->isMobile()){
				//We include the file only so it can define another width/height
				$design .= '_mobile';
			}

			if(!empty($design) && file_exists(ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'.php')){
				//We include the $design to be able to set parameters like the width/height
				ob_start();
				require(ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'.php');
				$squeezePage = ob_get_clean();
			}
		}else{
			$design = 'popup';
		}

		// Get subscription form url
		$baseUrl = ACYMAILING_LIVE.'index.php?option=com_acymailing&ctrl=sub&task=display&tmpl=component';
		$baseUrl .= empty($itemId) ? '' : '&Itemid='.$itemId;
		$formUrl = JRoute::_($baseUrl.'&interval='.$interval.'&formid='.$moduleId.'&design='.$design, true);

		// Script used to show the modal
		$script = "setTimeout(function() {
					SqueezeBox.setOptions({size: {x: $width, y: $height}, classWindow: 'acy_squeeze'});
					SqueezeBox.assignOptions();
					SqueezeBox.setContent('iframe', '$formUrl');
				}, $timer)";

		// Set event listener
		// TODO: Test it on safari (alert needed ?)
		/*if ($on == 'close')
		{
			$script = "window.onbeforeunload = function() {

				var message = '';

				if (!document.cookie.contains('modaldone=true'))
				{
					$script
					message = '$message'
				}

				if (Browser.chrome || Browser.ie || Browser.safari)
				{
					return message;
				}
				else if (Browser.firefox)
				{
					if (message.length)
					{
						alert(message);
					}
					return false;
				}
				else
				{
					if (message.length)
					{
						alert(message);
					}
					return '';
				}
			};";
		}
		else
		{*/
		$script = "window.addEvent('domready', function() { $script });";
		//}

		// Add the modal script to the page header
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}

	private function isMobile(){
		if(JRequest::getInt('testmobile')) return true;
		//Check if it's a mobile devise or not to display another version of the squeeze box
		if(!class_exists('Mobile_Detect', false)) include_once(dirname(__FILE__).DS.'mobile_detect.php');
		$mobileClass = new Mobile_Detect();
		return $mobileClass->isMobile();
	}

	function onTestplugin(){
		$config = acymailing_config();
		if($config->get('version') < '5.0.1') acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
		setcookie('acymailingSubscriptionState', '0', 0, '/');
		acymailing_display('Cookie successfully removed', 'success');

		$design = preg_replace('#[^a-z0-9_]#i', '', $this->params->get('design'));
		if(!empty($design)){
			$filesToUse[] = 'The system will test the following files and use them if they exist:';
			$filesToUse[] = ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'.php';
			$filesToUse[] = ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'_mobile.php';
			$currentLanguage = JFactory::getLanguage();
			$currentLang = str_replace('-', '_', strtolower($currentLanguage->getTag()));
			if(!empty($currentLang)){
				$filesToUse[] = ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$design.'_'.$currentLang.'.php';
			}
			$text = 'The system will test the following files and use them if they exist:<ul>';
			foreach($filesToUse as $oneFile){
				$text .= '<li>'.$oneFile;
				if(file_exists($oneFile)){
					$text .= '<span style="color:green"> [file detected!]</span>';
				}
				$text .= '</li>';
			}
			$text .= '</ul>';
			acymailing_display($text, 'info');
		}
	}
}//endclass