<?php
/**
 *  @package AkeebaBackup
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * Handle commercial extension update authorization
 *
 * @package     Joomla.Plugin
 * @subpackage  Installer.Akeebabackup
 * @since       2.5
 */
class plgInstallerDaycounts extends JPlugin
{
	private $_hathor,$_installfrom;

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string  $url        url from which package is going to be downloaded
	 * @param   array   $headers    headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true if credentials have been added to request or not our business, false otherwise (credentials not set by user)
	 *
	 * @since   2.5
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri = JUri::getInstance($url);

		// I don't care about download URLs not coming from our site
		$host = $uri->getHost();
		if (!in_array($host, array('www.daycounts.com', 'daycounts.com'))) {
			return true;
		}
	
		//Get the category Id.	
		$catid = $uri->getVar('catid', 0);
		$catid = intval($catid);
		if (!$catid) {
			return true;
		}
		
		JLoader::import('joomla.application.component.helper');
		$download_code = $uri->setVar('download_code', '');
		
		switch ($catid) {
			case 10: //Autocomplete
				$plugin = JPluginHelper::getPlugin('system','vm_search_autocomplete');
				$params = new JParameter($plugin->params);
				break;
			case 12: //Clone Order
				$plugin = JPluginHelper::getPlugin('system','vm_cloneorder');
				$params = new JParameter($plugin->params);
				break;
			case 14: //Virtuemart Bonus (VM2)
				$params = JComponentHelper::getParams('com_vm_bonus');
				break;
			case 15: //VM2 Facebook share plugin
				$plugin = JPluginHelper::getPlugin('vmpayment','fbshare');
				$params = new JParameter($plugin->params);
				$download_code = $params->get('downloadcode','');
				break;
			case 16: //Virtuemart Pre-Orders (VM2)
				$params = JComponentHelper::getParams('com_vm_preorder');
				break;
			case 17: //Virtuemart Mobile (VM2)
				$params = JComponentHelper::getParams('com_vm_mobile');
				break;
			case 18: //VM2 Related Articles plugin
				$plugin = JPluginHelper::getPlugin('vmcustom','article');
				$params = new JParameter($plugin->params);
				break;
			case 25: //VM2 Finalize Order
				$plugin = JPluginHelper::getPlugin('system','vm2finalize');
				$params = new JParameter($plugin->params);
				break;
			case 26: //VM2 shopper group changer
				$params = JComponentHelper::getParams('com_vm_sgc');
				break;
			case 29: //Admin Search plugin
				$plugin = JPluginHelper::getPlugin('system','adminsearch');
				$params = new JParameter($plugin->params);
				break;
			case 30: //VM2 Advanced inventory
				$params = JComponentHelper::getParams('com_vminventory');
				break;
			case 32: //VM2 Order amount Plugin
				$plugin = JPluginHelper::getPlugin('content','vmorderamount');
				$params = new JParameter($plugin->params);
				break;
			case 33: //Virtuemart Migrator
				$params = JComponentHelper::getParams('com_vmmigrate');
				break;
			case 36: //VM2 Children products
				$plugin = JPluginHelper::getPlugin('vmcustom','children');
				$params = new JParameter($plugin->params);
				break;
			case 38: //VM2 Edit Cart
				$plugin = JPluginHelper::getPlugin('system','editcart');
				$params = new JParameter($plugin->params);
				break;
			case 39: //VM2 Stock Handle
				$plugin = JPluginHelper::getPlugin('vmcustom','stockhandle');
				$params = new JParameter($plugin->params);
				break;
			case 39: //Virtuemart Language Manager
				$params = JComponentHelper::getParams('com_vmlanguage');
				break;
				
		}
		
		if ($params) {
			$download_code = $params->get('downloadcode','');
		}
		
		$uri->setScheme('http');
		$uri->setHost('www.daycounts.com');
		$url = $uri->toString();

		// Appent the Download ID to the download URL
		if (!empty($download_code))
		{
			$uri->setVar('download_code', $download_code);
			//Ensure option and tasks are there
			//$uri->setVar('option', 'com_versions');
			//$uri->setVar('task', 'updateserver');
			$uri->setVar('noredirect', 1);
			$uri->setScheme('http');
			$uri->setHost('www.daycounts.com');
			$url = $uri->toString();
			
		} else {
			JFactory::getApplication()->enqueueMessage('Please enter your download code in the component settings','error');
		}

		return true;
	}
	
	public function onInstallerViewAfterLastTab()
	{
		return;
		$ishathor = $this->isHathor() ? 1 : 0;

		if ($ishathor)
		{
			JHtml::_('jquery.framework');
?>
			<div class="clr"></div>
			<fieldset class="uploadform">
				<legend><?php echo JText::_('PLG_DAYCOUNTS_WEB_INSTALLER', true); ?></legend>
				<div id="jed-container">
					<div id="mywebinstaller" style="display:none">
						<a href="#"><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_LOAD_APPS'); ?></a>
					</div>
					<div class="well" id="web-loader" style="display:none">
						<h2><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING'); ?></h2>
					</div>
					<div class="alert alert-error" id="web-loader-error" style="display:none">
						<a class="close" data-dismiss="alert">×</a><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING_ERROR'); ?>
					</div>
				</div>
				<fieldset class="uploadform" id="uploadform-web" style="display:none">
					<div class="control-group">
						<strong><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM'); ?></strong><br />
						<span id="uploadform-web-name-label"><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_NAME'); ?>:</span> <span id="uploadform-web-name"></span><br />
						<?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_URL'); ?>: <span id="uploadform-web-url"></span>
					</div>
					<div class="form-actions">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton<?php echo $installfrom != '' ? 4 : 5; ?>()" />
						<input type="button" class="btn btn-secondary" value="<?php echo JText::_('JCANCEL'); ?>" onclick="Joomla.installfromwebcancel()" />
					</div>
				</fieldset>
			</fieldset>

<?php
		}
		else
		{
			echo JHtml::_('bootstrap.addTab', 'myTab', 'web', JText::_('PLG_DAYCOUNTS_WEB_INSTALLER', true));
?>
				<div id="jed-container" class="tab-pane">
					<div class="well" id="web-loader">
						<h2><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING'); ?></h2>
					</div>
					<div class="alert alert-error" id="web-loader-error" style="display:none">
						<a class="close" data-dismiss="alert">×</a><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING_ERROR'); ?>
					</div>
				</div>
	
				<fieldset class="uploadform" id="uploadform-web" style="display:none">
					<div class="control-group">
						<strong><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM'); ?></strong><br />
						<span id="uploadform-web-name-label"><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_NAME'); ?>:</span> <span id="uploadform-web-name"></span><br />
						<?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_URL'); ?>: <span id="uploadform-web-url"></span>
					</div>
					<div class="form-actions">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton<?php echo $installfrom != '' ? 4 : 5; ?>()" />
						<input type="button" class="btn btn-secondary" value="<?php echo JText::_('JCANCEL'); ?>" onclick="Joomla.installfromwebcancel()" />
					</div>
				</fieldset>
			
<?php
			echo JHtml::_('bootstrap.endTab');
		}

	}

	private function isHathor()
	{
		if (is_null($this->_hathor))
		{
			$app = JFactory::getApplication();
			$templateName = strtolower($app->getTemplate());
			if ($templateName == 'hathor')
			{
				$this->_hathor = true;
			}
			else
			{
				$this->_hathor = false;
			}
		}
		return $this->_hathor;
	}
}
