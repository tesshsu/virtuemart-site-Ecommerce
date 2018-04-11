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

defined('_JEXEC') or die('Restricted access'); 

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

//AdminUIHelper::startAdminArea();

	
?>
<div class="clr"></div>
<br/><a href="https://www.daycounts.com/" target="_blank" title="DayCounts.com"><img src="components/com_vmmigrate/assets/images/daycounts.png"  alt="DayCounts.com" border="0" height="40" /></a>
<br/><br/>
<iframe frameborder="0" width="100%" height="1000" src="https://www.daycounts.com/component/versions/?catid=<?php echo $this->config->versioncat; ?>&tmpl=component&myVersion=<?php echo $this->config->version; ?>"></iframe>

<div class="clr"></div>
<?php 
//AdminUIHelper::endAdminArea(); 
