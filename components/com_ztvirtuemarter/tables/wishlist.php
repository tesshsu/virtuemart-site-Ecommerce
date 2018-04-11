<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ztvirtuemarter
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Wishlist Table class
 *
 * @since  0.0.1
 */
class ZtvirtuemarterTableWishlist extends JTable
{
    public $id = null;
    public $virtuemart_product_id = null;
    public $userid = null;

    /**
     * Constructor
     *
     * @param   JDatabaseDriver &$db A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__wishlists', 'id', $db);
    }

    /**
     * Table fields checking
     * @return type
     */
    public function check()
    {
        if (empty($this->userid) || empty($this->virtuemart_product_id))
            return false;
        return parent::check();
    }
}