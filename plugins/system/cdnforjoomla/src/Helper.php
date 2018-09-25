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

namespace RegularLabs\CDNforJoomla;

defined('_JEXEC') or die;

use JFactory;

/**
 * Plugin that replaces stuff
 */
class Helper
{
	public function onAfterRender()
	{
		$html = JFactory::getApplication()->getBody();

		if ($html == '')
		{
			return;
		}

		Replace::replace($html);

		Clean::cleanLeftoverJunk($html);

		JFactory::getApplication()->setBody($html);
	}
}
