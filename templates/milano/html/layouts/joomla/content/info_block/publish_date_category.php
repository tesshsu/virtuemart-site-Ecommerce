<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
<dd class="published">
	<time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
		<span class="month"><?php echo JHtml::_('date', $displayData['item']->publish_up, 'F'); ?></span>
		<span class="day"><?php echo JHtml::_('date', $displayData['item']->publish_up, 'd'); ?></span>
	</time>
</dd>