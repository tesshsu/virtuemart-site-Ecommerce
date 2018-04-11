<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

require_once (JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'helper'.DS.'settings.php');

class FSTAdminHelper
{
	static function PageSubTitle2($title,$usejtext = true)
	{
		if ($usejtext)
			$title = JText::_($title);
		
		return str_replace("$1",$title,FST_Settings::get('display_h3'));
	}
	
	static function IsFAQs()
	{
		if (JRequest::getVar('option') == "com_fsf")
			return true;
		return false;	
	}
	
	static function IsTests()
	{
		if (JRequest::getVar('option') == "com_fst")
			return true;
		return false;	
	}
	
	static function GetVersion($path = "")
	{
		
		global $fsj_version;
		if (empty($fsj_version))
		{
			if ($path == "") $path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fst';
			$file = $path.DS.'fst.xml';
			
			if (!file_exists($file))
				return FST_Settings::get('version');
			
			$xml = simplexml_load_file($file);
			
			$fsj_version = $xml->version;
		}

		if ($fsj_version == "[VERSION]")
			return FST_Settings::get('version');
			
		return $fsj_version;
	}	

	static function GetInstalledVersion()
	{
		return FST_Settings::get('version');
	}
	
	static function Is16()
	{
		global $fsjjversion;
		if (empty($fsjjversion))
		{
			$version = new JVersion;
			$fsjjversion = 1;
			if ($version->RELEASE == "1.5")
				$fsjjversion = 0;
		}
		return $fsjjversion;
	}

	static function DoSubToolbar()
	{
		if (!FST_Helper::Is16())
		{
			JToolBarHelper::divider();
			JToolBarHelper::help("help.php?help=admin-view-" . JRequest::getVar('view'),true);
			return;
		}

		
		if (JFactory::getUser()->authorise('core.admin', 'com_fst'))    
		{        
			JToolBarHelper::preferences('com_fst');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help("",false,"http://www.freestyle-joomla.com/comhelp/fst/admin-view-" . JRequest::getVar('view'));

		$vName = JRequest::getCmd('view', 'fsts');
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FST_OVERVIEW'),
			'index.php?option=com_fst&view=fsts',
			$vName == 'fsts' || $vName == ""
			);
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FST_SETTINGS'),
			'index.php?option=com_fst&view=settings',
			$vName == 'settings'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_TEMPLATES'),
			'index.php?option=com_fst&view=templates',
			$vName == 'templates'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_VIEW_SETTINGS'),
			'index.php?option=com_fst&view=settingsview',
			$vName == 'settingsview'
			);

// 

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_PRODUCTS'),
			'index.php?option=com_fst&view=prods',
			$vName == 'prods'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_MODERATION'),
			'index.php?option=com_fst&view=tests',
			$vName == 'tests'
			);

// 
		JSubMenuHelper::addEntry(
			JText::_('COM_FST_USERS'),
			'index.php?option=com_fst&view=fusers',
			$vName == 'fusers'
			);
// 

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_EMAIL_TEMPLATES'),
			'index.php?option=com_fst&view=emails',
			$vName == 'emails'
			);
// ##NOT_FAQS_END##

		JSubMenuHelper::addEntry(
			JText::_('COM_FST_ADMIN'),
			'index.php?option=com_fst&view=backup',
			$vName == 'backup'
			);

	}	
	
	
	static function IncludeHelp($file)
	{
		$lang =& JFactory::getLanguage();
		$tag = $lang->getTag();
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fst'.DS.'help'.DS.$tag.DS.$file;
		if (file_exists($path))
			return file_get_contents($path);
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fst'.DS.'help'.DS.'en-GB'.DS.$file;
		
		return file_get_contents($path);
	}
	
	static $langs;
	static $lang_bykey;
	static function DisplayLanguage($language)
	{
		if (empty(FSTAdminHelper::$langs))
		{
			FSTAdminHelper::LoadLanguages();
		}
		
		if (array_key_exists($language, FSTAdminHelper::$lang_bykey))
			return FSTAdminHelper::$lang_bykey[$language]->text;
		
		return "";
	}
	
	static function LoadLanguages()
	{		
		$deflang = new stdClass();
		$deflang->value = "*";
		$deflang->text = JText::_('JALL');
		
		FSTAdminHelper::$langs = array_merge(array($deflang) ,JHtml::_('contentlanguage.existing'));
		
		foreach (FSTAdminHelper::$langs as $lang)
		{
			FSTAdminHelper::$lang_bykey[$lang->value] = $lang;	
		}		
	}
	
	static function GetLanguagesForm($value)
	{
		if (empty(FSTAdminHelper::$langs))
		{
			FSTAdminHelper::LoadLanguages();
		}
		
		return JHTML::_('select.genericlist',  FSTAdminHelper::$langs, 'language', 'class="inputbox" size="1" ', 'value', 'text', $value);
	}
	
	static $access_levels;
	static $access_levels_bykey;
	
	static function DisplayAccessLevel($access)
	{
		if (empty(FSTAdminHelper::$access_levels))
		{
			FSTAdminHelper::LoadAccessLevels();
		}
		
		if (array_key_exists($access, FSTAdminHelper::$access_levels_bykey))
			return FSTAdminHelper::$access_levels_bykey[$access];
		
		return "";
		
	}
	
	static function LoadAccessLevels()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__viewlevels AS a');
		$query->group('a.id, a.title, a.ordering');
		$query->order('a.ordering ASC');
		$query->order($query->qn('title') . ' ASC');

		// Get the options.
		$db->setQuery($query);
		FSTAdminHelper::$access_levels = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return null;
		}

		foreach (FSTAdminHelper::$access_levels as $al)
		{
			FSTAdminHelper::$access_levels_bykey[$al->value] = $al->text;
		}	
	}
	
	static function GetAccessForm($value)
	{
		return JHTML::_('access.level',	'access',  $value, 'class="inputbox" size="1"', false);
	}
	
	static $filter_lang;
	static $filter_access;
	static function LA_GetFilterState()
	{
		$mainframe = JFactory::getApplication();
		FSTAdminHelper::$filter_lang	= $mainframe->getUserStateFromRequest( 'la_filter.'.'fst_filter_language', 'fst_filter_language', '', 'string' );
		FSTAdminHelper::$filter_access	= $mainframe->getUserStateFromRequest( 'la_filter.'.'fst_filter_access', 'fst_filter_access', 0, 'int' );
	}
	
	static function LA_Filter($nolangs = false)
	{
		if (!FSTAdminHelper::Is16()) return;
		
		if (empty(FSTAdminHelper::$access_levels))
		{
			FSTAdminHelper::LoadAccessLevels();
		}
		
		if (!$nolangs && empty(FSTAdminHelper::$langs))
		{
			FSTAdminHelper::LoadLanguages();
		}
	
		if (empty(FSTAdminHelper::$filter_lang))
		{
			FSTAdminHelper::LA_GetFilterState();
		}
		
		$options = FSTAdminHelper::$access_levels;		
		array_unshift($options, JHtml::_('select.option', 0, JText::_('JOPTION_SELECT_ACCESS')));
		echo JHTML::_('select.genericlist',  $options, 'fst_filter_access', 'class="inputbox" size="1"  onchange="document.adminForm.submit( );"', 'value', 'text', FSTAdminHelper::$filter_access);
		
		if (!$nolangs)
		{
			$options = FSTAdminHelper::$langs;		
			array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_SELECT_LANGUAGE')));
			echo JHTML::_('select.genericlist',  $options, 'fst_filter_language', 'class="inputbox" size="1"  onchange="document.adminForm.submit( );"', 'value', 'text', FSTAdminHelper::$filter_lang);
		}
	}
	
	static function LA_Header($obj, $nolangs = false)
	{
		if (FSTAdminHelper::Is16())
		{
			if (!$nolangs)
			{
				?>
 				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'LANGUAGE', 'fst_filter_language', @$obj->lists['order_Dir'], @$obj->lists['order'] ); ?>
				</th>
				<?php
			}
			
			?>
 			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'ACCESS_LEVEL', 'fst_filter_access', @$obj->lists['order_Dir'], @$obj->lists['order'] ); ?>
			</th>
			<?php
		}
	}
	
	static function LA_Row($row, $nolangs = false)
	{
		if (FSTAdminHelper::Is16())
		{
			if (!$nolangs)
			{
				?>
				<td>
					<?php echo FSTAdminHelper::DisplayLanguage($row->language); ?></a>
				</td>
				<?php
			}
			
			?>
			<td>
			    <?php echo FSTAdminHelper::DisplayAccessLevel($row->access); ?></a>
			</td>
			<?php
		}
	}
	
	static function LA_Form($item, $nolangs = false)
	{
		if (FSTAdminHelper::Is16())
		{
			?>
			<tr>
				<td width="135" align="right" class="key">
					<label for="title">
						<?php echo JText::_("JFIELD_ACCESS_LABEL"); ?>:
					</label>
				</td>
				<td>
					<?php echo FSTAdminHelper::GetAccessForm($item->access); ?>
				</td>
			</tr>
			
			<?php
			if (!$nolangs)
			{
			?>

				<tr>
					<td width="135" align="right" class="key">
						<label for="title">
							<?php echo JText::_("JFIELD_LANGUAGE_LABEL"); ?>:
						</label>
					</td>
					<td>
						<?php echo FSTAdminHelper::GetLanguagesForm($item->language); ?>
					</td>
				</tr>
				
			<?php
			}
		}
	}
	
}