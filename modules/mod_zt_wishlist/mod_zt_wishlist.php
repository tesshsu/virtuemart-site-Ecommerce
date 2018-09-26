<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Comparelist Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

// No direct access.
defined('_JEXEC') or die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
VmConfig::loadConfig();
// Load the language file of com_virtuemart.
JFactory::getLanguage()->load('com_virtuemart');
require('helper.php');
$user = JFactory::getUser();
$ratingModel = VmModel::getModel('ratings');
$productModel = VmModel::getModel('product');

$mainframe = JFactory::getApplication();
$wishlistIds = $mainframe->getUserState( "com_ztvirtuemarter.site.wishlistIds", array() );

$prods = array();
if ($user->guest) {
    if (!empty($wishlistIds)) {
        $products = $wishlistIds;
        $prods = $productModel->getProducts($products);
        $productModel->addImages($prods, 1);
        $currency = CurrencyDisplay::getInstance();

    } else {
        $wishlistIds = null;
    }
} else {
    $wishlistModel = new ZtvirtuemarterModelWishlist();
    $prods = $wishlistModel->getProducts();
    $currency = CurrencyDisplay::getInstance();
}
require JModuleHelper::getLayoutPath('mod_zt_wishlist', $params->get('layout', 'default'));
?>