<?php
/**
 * @version     1.1
 * @package     Advanced Search Manager for Virtuemart
 * @copyright   Copyright (C) 2016 JoomDev. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      JoomDev <info@joomdev.com> - http://www.joomdev.com/
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/asvm.php';

/**
 * Bannerclient Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldProductName extends JFormFieldList
{
	
	protected $type = 'ProductName';

	public function getOptions()
	{
		$options = AsvmHelper::getProductNameOptions();

		return array_merge(parent::getOptions(), $options);
	}
}
