<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Script file of AdminExile Pro
 */
class plgSystemAdminexileproInstallerScript
{
	/**
	 * Method to install the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function install($parent) 
	{
		$this->installAjaxPlugin($parent);
		echo '<p>AdminExile Pro has been installed.  Please enable both plugins.</p>';
	}
 
	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		echo '<p>AdminExile Pro has been uninstalled</p>';
	}
 
	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function update($parent) 
	{
		$this->installAjaxPlugin($parent);
		echo '<p>AdminExile Pro has been updated to version' . $parent->get('manifest')->version . '</p>';
	}
	
	private function installAjaxPlugin($parent) {	    
	    $packageFile = JPATH_ROOT.'/plugins/system/adminexilepro/installers/plg_ajax_adminexilepro-'.$parent->get('manifest')->version.'.zip';
	    $package = JInstallerHelper::unpack($packageFile);
	    if (!$package)
	    {
		    echo "<p>An error occurred while unpacking the file</p>";
	    }
	    $installer = new JInstaller;
	    $installed = $installer->install($package['extractdir']);
	    
	    // Let's cleanup the downloaded archive and the temp folder
	    if (JFolder::exists($package['extractdir']))
	    {
		    JFolder::delete($package['extractdir']);
	    }

	    if (JFile::exists($package['packagefile']))
	    {
		    JFile::delete($package['packagefile']);
	    }

	    if ($installed)
	    {
		    echo "<p>Ajax plugin installed</p>";
	    }
	    else
	    {
		    echo "<p>Ajax plugin installation failed</p>";
	    }
	}
 
	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
//	function preflight($type, $parent) 
//	{
//		echo '<p>Anything here happens before the installation/update/uninstallation of the module</p>';
//	}
 
	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
//	function postflight($type, $parent) 
//	{
//		echo '<p>Anything here happens after the installation/update/uninstallation of the module</p>';
//	}
}