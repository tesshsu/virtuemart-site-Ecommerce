<?php
/**
 * @package         Regular Labs Library
 * @version         17.1.24691
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2017 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if (!is_file(__DIR__ . '/vendor/autoload.php'))
{
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

if (is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';
}

use RegularLabs\Library\Document as RL_Document;
use RegularLabs\LibraryPlugin\AdminMenu as RL_AdminMenu;
use RegularLabs\LibraryPlugin\DownloadKey as RL_DownloadKey;
use RegularLabs\LibraryPlugin\QuickPage as RL_QuickPage;
use RegularLabs\LibraryPlugin\SearchHelper as RL_SearchHelper;

JFactory::getLanguage()->load('plg_system_regularlabs', __DIR__);

class PlgSystemRegularLabs extends JPlugin
{
	public function onAfterRoute()
	{
		if (!is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
		{
			if (JFactory::getApplication()->isAdmin())
			{
				JFactory::getApplication()->enqueueMessage('The Regular Labs Library folder is missing or incomplete: ' . JPATH_LIBRARIES . '/regularlabs', 'error');
			}

			return;
		}

		RL_DownloadKey::update();

		RL_SearchHelper::load();

		RL_QuickPage::render();
	}

	public function onAfterDispatch()
	{
		if (!is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
		{
			return;
		}

		if (!RL_Document::isAdmin() || !RL_Document::isHtml()
		)
		{
			return;
		}

		RL_Document::script('regularlabs/script.min.js');
	}

	public function onAfterRender()
	{
		if (!is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
		{
			return;
		}

		if (!RL_Document::isAdmin() || !RL_Document::isHtml()
		)
		{
			return;
		}

		RL_AdminMenu::combine();

		RL_AdminMenu::addHelpItem();
	}
}

