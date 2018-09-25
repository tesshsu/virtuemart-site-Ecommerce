<?php
/**
 * @package         CDN for Joomla!
 * @version         6.0.2PRO
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

use RegularLabs\CDNforJoomla\Plugin;

/**
 * Plugin that replaces stuff
 */
class PlgSystemCDNforJoomla extends Plugin
{
	public $_alias       = 'cdnforjoomla';
	public $_title       = 'CDN_FOR_JOOMLA';
	public $_lang_prefix = 'CDN';

	public $_page_types = ['html', 'feed', 'ajax', 'raw'];
}
