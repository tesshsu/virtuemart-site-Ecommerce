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
	<dl class="info-block-extra">
		<?php if ($displayData['params']->get('show_author') && !empty($displayData['item']->author )) : ?>
			<?php echo JLayoutHelper::render('joomla.content.info_block.author', $displayData); ?>
		<?php endif; ?>
		<?php if ($displayData['params']->get('show_publish_date')) : ?>
			<?php echo JLayoutHelper::render('joomla.content.info_block.publish_date_detail', $displayData); ?>
		<?php endif; ?>
	</dl>
