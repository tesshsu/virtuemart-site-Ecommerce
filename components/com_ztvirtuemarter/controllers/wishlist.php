<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;
JFactory::getLanguage()->load('com_wishlists');
if (!class_exists('ZtvirtuemarterModelWishlist')) require(JPATH_SITE . '/components/com_ztvirtuemarter/models/wishlist.php');
class ZtvirtuemarterControllerWishlist extends JControllerLegacy
{
    public function __construct()
    {
        parent::__construct();
        ZtvituemarterHelper::loadVMLibrary();
    }

    public function add()
    {
        $jinput = JFactory::getApplication()->input;

        $mainframe = JFactory::getApplication();
        $wishlistIds = $mainframe->getUserState("com_ztvirtuemarter.site.wishlistIds", array());

        $itemID = ZtvituemarterHelper::getItemId('wishlist');

        VmConfig::loadConfig();
        VmConfig::loadJLang('com_ztvirtuemarter', true);
        $productModel = VmModel::getModel('product');
        $product = array($jinput->get('product_id', null, 'INT'));

        $prods = $productModel->getProducts($product);
        $productModel->addImages($prods, 1);
        $product = isset($prods[0]) ? $prods[0] : null;

        $title = '<div class="title">' . JHTML::link($product->link, $product->product_name) . '</div>';
        $prodUrl = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id);
//        if (!empty($product->file_url_thumb)) {
//            $imgUrl = $product->file_url_thumb;
//        }else if (!empty($product->file_url)) {
//            $imgUrl = $product->file_url;
//        } else {
//            $imgUrl = JURI::base() . 'images/stories/virtuemart/noimage.gif';
//        }
//        $imgProd = '<div class="wishlist-product-img"><a href="' . $prodUrl . '"><img src="' . JURI::base() . $imgUrl . '" alt="' . $product->product_name . '" title="' . $product->product_name . '" /></a></div>';
        $images = $product->images;
        $imgProd = '<div class="wishlist-product-img"><a href="' . $prodUrl . '">'.$images[0]->displayMediaThumb('alt="' . $product->product_name . '" title="' . $product->product_name . '" ', FALSE).'</a></div>';
        $prodName = '<div class="wishlist-product-detail"><div class="name">' . JHTML::link($product->link, $product->product_name) . '</div><div class="remwishlists"><a class="tooltip-1" title="remove"  onclick="ZtVirtuemarter.wishlist.remove(' . $product->virtuemart_product_id . ');"><i class="fa fa-times"></i>' . JText::_('REMOVE') . '</a></div></div>';
        $link = JRoute::_('index.php?option=com_ztvirtuemarter&view=wishlist&Itemid=' . $itemID . '');
        $btnwishlists = '<a id="wishlists_go" class="button" rel="nofollow" href="' . $link . '">' . JText::_('GO_TO_WISHLISTS') . '</a>';
        $btnwishlistsback = '<a id="wishlists_continue" class="continue button reset2" rel="nofollow" href="javascript:;">' . JText::_('CONTINUE_SHOPPING') . '</a>';
        $btnrem = '<div class="remwishlists"><a class="tooltip-1" title="remove"  onclick="ZtVirtuemarter.wishlist.remove(' . $product->virtuemart_product_id . ');"><i class="fa fa-times"></i>' . JText::_('REMOVE') . '</a></div>';
        $productIds = $product->virtuemart_product_id;
        $exists = 0;
        $user = JFactory::getUser();
        if ($user->guest) {
            if (!in_array($jinput->get('product_id', null, 'INT'), $wishlistIds)) {

                $wishlistIds[] = $jinput->get('product_id', null, 'INT');
                $message = '<span class="successfully">' . JText::_('COM_WHISHLISTS_MASSEDGE_ADDED_NOTREG') . '</span>';
            } else {
                $exists = 1;
                $message = '<span class="notification">' . JText::_('COM_WHISHLISTS_MASSEDGE_ALLREADY_NOTREG') . '</span>';
            }
        } else {
            $wishlistModel = new ZtvirtuemarterModelWishlist();

            if (!in_array($jinput->get('product_id', null, 'INT'), $wishlistIds)) {
                //Insert new wishlist item
                $wishlistModel->insert($jinput->get('product_id', null, 'INT'));

                $wishlistIds[] = $jinput->get('product_id', null, 'INT');
                $message = '<span class="successfully">' . JText::_('COM_WHISHLISTS_MASSEDGE_ADDED_REG') . '</span>';
            } else {
                $exists = 1;
                $message = '<span class="notification">' . JText::_('COM_WHISHLISTS_MASSEDGE_ALLREADY_REG') . '</span>';
            }
        }
        $totalwishlists = count($wishlistIds);

        $mainframe->setUserState("com_ztvirtuemarter.site.wishlistIds", $wishlistIds);

        echo json_encode(array('message' => $message, 'title' => $title, 'totalwishlists' => $totalwishlists, 'exists' => $exists, 'img_prod' => $imgProd, 'btnrem' => $btnrem, 'prod_name' => $prodName, 'product_ids' => $productIds, 'btnwishlists' => $btnwishlists, 'btnwishlistsback' => $btnwishlistsback));
        exit;
    }

    public function removed()
    {
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_ztvirtuemarter', true);
        $mainframe = JFactory::getApplication();
        $wishlistIds = $mainframe->getUserState("com_ztvirtuemarter.site.wishlistIds", array());
        $jinput = JFactory::getApplication()->input;

        $productModel = VmModel::getModel('product');
        $prods = $productModel->getProducts(array($jinput->get('remove_id', null, 'INT')));

        $user = JFactory::getUser();
        if ($jinput->get('remove_id', null, 'INT')) {
            if ($user->guest) {
                $title = '<span>' . JHTML::link($prods[0]->link, $prods[0]->product_name) . '</span>';
                $message = JText::_('COM_WHISHLISTS_MASSEDGE_REM') . ' ' . $title . ' ' . JText::_('COM_WHISHLISTS_MASSEDGE_REM2');
            } else {
                $wishlistModel = new ZtvirtuemarterModelWishlist();
                $wishlistModel->remove($jinput->get('remove_id', null, 'INT'));
                $title = '<span>' . JHTML::link($prods[0]->link, $prods[0]->product_name) . '</span>';
                $message = JText::_('COM_WHISHLISTS_MASSEDGE_REM') . ' ' . $title . ' ' . JText::_('COM_WHISHLISTS_MASSEDGE_REM2');
            }

            foreach ($wishlistIds as $k => $v) {
                if ($jinput->get('remove_id', null, 'INT') == $v) {
                    unset($wishlistIds[$k]);
                }
            }
        }

        $totalrem = count($wishlistIds);
        $mainframe->setUserState("com_ztvirtuemarter.site.wishlistIds", $wishlistIds);
        echo json_encode(array('rem' => $message, 'totalrem' => $totalrem));
        exit;
    }
}