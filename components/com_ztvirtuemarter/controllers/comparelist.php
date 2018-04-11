<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

class ZtvirtuemarterControllerComparelist extends JControllerLegacy
{
    public function __construct()
    {
        parent::__construct();
        ZtvituemarterHelper::loadVMLibrary();
    }


    public function add()
    {
        $mainframe = JFactory::getApplication();
        $compareIds = $mainframe->getUserState("com_ztvirtuemarter.site.compareIds", array());

        $jinput = JFactory::getApplication()->input;
        JFactory::getLanguage()->load('com_ztvirtuemarter');
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_ztvirtuemarter', true);

        $itemId = ZtvituemarterHelper::getItemId('comparelist');

        $productModel = VmModel::getModel('product');

        $product = array($jinput->get('product_id', null, 'INT'));
        $prods = $productModel->getProducts($product);
        $productModel->addImages($prods, 1);
        $product = $prods[0];

        $exists = 0;
        $productIds = $product->virtuemart_product_id;
        $title = '<div class="title">' . JHTML::link($product->link, $product->product_name) . '</div>';
        $prodUrl = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id);
//        if (!empty($product->file_url_thumb)) {
//            $imgUrl = $product->file_url_thumb;
//        }else if (!empty($product->file_url)) {
//            $imgUrl = $product->file_url;
//        } else {
//            $imgUrl = JURI::base() . 'images/stories/virtuemart/noimage.gif';
//        }
//        $imgProd = '<div class="compare-product-img"><a href="' . $prodUrl . '"><img src="' . JURI::base() . $imgUrl . '" alt="' . $product->product_name . '" title="' . $product->product_name . '" /></a></div>';
        $images = $product->images;
        $imgProd = '<div class="compare-product-img"><a href="' . $prodUrl . '">'.$images[0]->displayMediaThumb('alt="' . $product->product_name . '" title="' . $product->product_name . '" ', FALSE).'</a></div>';
        $prodName = '<div class="compare-product-detail"><div class="name">' . JHTML::link($product->link, $product->product_name) . '</div><div class="remcompare"><a class="tooltip-1" title="remove"  onclick="ZtVirtuemarter.compare.remove(' . $product->virtuemart_product_id . ');"><i class="fa fa-times"></i>' . JText::_('REMOVE') . '</a></div></div>';
        $link = JRoute::_('index.php?option=com_ztvirtuemarter&view=comparelist&Itemid=' . $itemId . '');
        $btncompare = '<a id="compare_go" class="button" rel="nofollow" href="' . $link . '">' . JText::_('GO_TO_COMPARE') . '</a>';
        $btncompareback = '<a id="compare_continue" class="continue button reset2" rel="nofollow" href="javascript:;">' . JText::_('CONTINUE_SHOPPING') . '</a>';
        $btnrem = '<div class="remcompare"><a class="tooltip-1" title="remove"  onclick="ZtVirtuemarter.compare.remove(' . $product->virtuemart_product_id . ');"><i class="fa fa-times"></i>' . JText::_('REMOVE') . '</a></div>';
        if (isset($compareIds) && (!in_array($jinput->get('product_id', null, 'INT'), $compareIds))) {
            $compareIds[] = $jinput->get('product_id', null, 'INT');
            $message = '<span class="successfully">' . JText::_('COM_COMPARE_MASSEDGE_ADDED_NOTREG') . '</span>';
        } else {
            $exists = 1;
            $message = '<span class="notification">' . JText::_('COM_COMPARE_MASSEDGE_ALLREADY_NOTREG') . '</span>';
        }
        $totalcompare = count($compareIds);

        //update compare list to user state
        $mainframe->setUserState("com_ztvirtuemarter.site.compareIds", $compareIds);

        //return data json
        echo json_encode(array('message' => $message, 'title' => $title, 'totalcompare' => $totalcompare, 'exists' => $exists, 'img_prod' => $imgProd, 'btnrem' => $btnrem, 'prod_name' => $prodName, 'product_ids' => $productIds, 'btncompare' => $btncompare, 'btncompareback' => $btncompareback));
        exit;
    }

    public function removed()
    {
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_ztvirtuemarter', true);
        $mainframe = JFactory::getApplication();
        $compareIds = $mainframe->getUserState("com_ztvirtuemarter.site.compareIds", array());
        $jinput = JFactory::getApplication()->input;

        $productModel = VmModel::getModel('product');

        if ($jinput->get('remove_id', null, 'INT')) {
            foreach ($compareIds as $k => $v)
                if ($jinput->get('remove_id', null, 'INT') == $v)
                    unset($compareIds[$k]);
            $prod = array($jinput->get('remove_id', null, 'INT'));
            $prods = $productModel->getProducts($prod);
            foreach ($prods as $product) {
                $title = '<span>' . JHTML::link($product->link, $product->product_name) . '</span>';
            }
            $totalrem = count($compareIds);
        }
        $mainframe->setUserState("com_ztvirtuemarter.site.compareIds", $compareIds);
        echo json_encode(array('rem' => JText::_('COM_COMPARE_MASSEDGE_REM') . ' ' . $title . ' ' . JText::_('COM_COMPARE_MASSEDGE_REM2'), 'totalrem' => $totalrem));
        exit;
    }
}