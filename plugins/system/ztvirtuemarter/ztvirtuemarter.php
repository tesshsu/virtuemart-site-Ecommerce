<?php

/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Plugins
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
if (!class_exists('VmConfig')) {
    require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
}
class plgSystemZtvirtuemarter extends JPlugin
{
    /**
     * Class Constructor
     * @param object $subject
     * @param array $config
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->document = JFactory::getDocument();
        $this->loadLanguage();
        $jlang = JFactory::getLanguage();
        $jlang->load('com_ztvirtuemarter', JPATH_SITE, $jlang->getDefault(), true);
    }

    public function onBeforeRender()
    {
        $com  = JFactory::getApplication()->input->getString('option');
        $doc = JFactory::getDocument();

        if ($com != 'com_virtuemart') {
            JFactory::getDocument()->addScript('components/com_virtuemart/assets/js/fancybox/jquery.fancybox-1.3.4.pack.js');
            JFactory::getDocument()->addScript('components/com_virtuemart/assets/js/vmprices.js');
            JFactory::getDocument()->addScript('components/com_virtuemart/assets/js/chosen.jquery.min.js');
            JFactory::getDocument()->addScript('components/com_virtuemart/assets/js/vmsite.js');
        }

        if (self::getZtvirtuemarterSetting()->enable_countdown == '1')
            $doc->addScript(JURI::root() . '/plugins/system/ztvirtuemarter/assets/js/jquery.countdown.min.js');
        if (self::getZtvirtuemarterSetting()->enable_photozoom == '1')
            $doc->addScript(JURI::root() . '/plugins/system/ztvirtuemarter/assets/js/jquery.elevateZoom-3.0.8.min.js');

        $doc->addScript(JURI::root() . '/plugins/system/ztvirtuemarter/assets/js/ztvirtuemarter.js');

        if (self::getZtvirtuemarterSetting()->enable_auto_insert == '1') {
            $scriptAction = array();
            $scriptAction[] = 'jQuery(document).ready(function () {';
            $scriptAction[] = 'ZtVirtuemarter.actionButtons(' . self::getZtvirtuemarterSetting()->enable_quickview . ', ' . self::getZtvirtuemarterSetting()->enable_compare . ', ' . self::getZtvirtuemarterSetting()->enable_wishlist . ');';
            $scriptAction[] = 'ZtVirtuemarter.countdown(' . self::getZtvirtuemarterSetting()->enable_countdown . ');';
            $scriptAction[] = '});';
            $doc->addScriptDeclaration(implode($scriptAction));
        }
    }

    public function onAfterInitialise()
    {
        $times = array();
        $jinput = JFactory::getApplication()->input;

        if ($jinput->getCmd('action') == 'countdown') {
            $productIds = $jinput->get('countdownProductIds', '');
            if (!empty($productIds)) {
                $ids = explode('-', $productIds);
                if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
                if (!class_exists('VirtueMartModelProduct')) {
                    JLoader::import('product', JPATH_ADMINISTRATOR . '/components/com_virtuemart/models');
                }
                $productModel = VmModel::getModel('product');
                $products = $productModel->getProducts($ids);
                if (count($products) > 0) {
                    foreach ($products as $product) {
                        if ($product->prices['product_price_publish_down'] > 0) {
                            $time = strtotime($product->prices['product_price_publish_down']);
                            $times[$product->virtuemart_product_id] = date('Y/m/d', $time);
                        }
                    }
                }
                echo json_encode($times);
                die;
            }
        }
        return;
    }

    public static function addCompareButton($product)
    {
        if (self::getZtvirtuemarterSetting()->enable_compare == '1'):
            $mainframe = JFactory::getApplication();
            $compareIds = $mainframe->getUserState("com_ztvirtuemarter.site.compareIds", array());
            ?>
            <div class="compare compare<?php echo $product->virtuemart_product_id; ?>">
                <a class="compare-label add-compare hasTooltip <?php echo in_array($product->virtuemart_product_id, $compareIds) ? 'go_to_compare active' : ''; ?>"
                   title="<?php echo JText::_('DR_ADD_TO_COMPARE'); ?>"
                   onclick="ZtVirtuemarter.compare.add('<?php echo $product->virtuemart_product_id; ?>');">
                    <i class="fa fa-files-o"></i>
                    <span><?php echo JText::_("DR_ADD_TO_COMPARE"); ?></span>
                </a>
            </div>
        <?php endif;
    }

    public static function addQuickviewButton($product)
    {
        if (self::getZtvirtuemarterSetting()->enable_quickview == '1'):?>
            <div class="quickview quickview<?php echo $product->virtuemart_product_id; ?>">
                <a class="quickview-label add-quickview hasTooltip"
                   title="<?php echo JText::_('PLG_ZT_QUICKVIEW_PRODUCT'); ?>"
                   onclick="ZtVirtuemarter.quickview.show('<?php echo $product->virtuemart_product_id; ?>');">
                    <i class="fa fa-search"></i>
                    <span><?php echo JText::_("PLG_ZT_QUICKVIEW_PRODUCT"); ?></span>
                </a>
            </div>
        <?php endif;
    }

    public static function addWishlistButton($product)
    {
        if (self::getZtvirtuemarterSetting()->enable_wishlist == '1'):
            if (!class_exists('ZtvirtuemarterModelWishlist')) require(JPATH_SITE . '/components/com_ztvirtuemarter/models/wishlist.php');
            $mainframe = JFactory::getApplication();
            $wishlistIds = $mainframe->getUserState("com_ztvirtuemarter.site.wishlistIds", array());
            ?>

            <div class="wishlist list_wishlists<?php echo $product->virtuemart_product_id; ?>">
                <?php
                $user = JFactory::getUser();

                if ($user->guest) :
                    ?>
                    <a class="add_wishlist hasTooltip <?php echo in_array($product->virtuemart_product_id, $wishlistIds) ? 'go_to_whishlist active' : ''; ?>"
                       title="<?php echo JText::_('ADD_TO_WHISHLIST'); ?>"
                       onclick="ZtVirtuemarter.wishlist.add('<?php echo $product->virtuemart_product_id; ?>');">
                        <i class="fa fa-heart-o"></i>
                        <span><?php echo JText::_("ADD_TO_WHISHLIST"); ?></span>
                    </a>
                <?php
                else :
                    JPluginHelper::importPlugin('System');
                    $dispatcher = JDispatcher::getInstance();
                    $results = $dispatcher->trigger('onBeforeRender');

                    if ($results[0] == 'true') {
                        $wishlistModel = new ZtvirtuemarterModelWishlist();
                        $allproducts = $wishlistModel->getProducts();
                        foreach ($allproducts as $productbd) {
                            $allprod['id'][] = $productbd['virtuemart_product_id'];
                        }
                    }
                    /* ?>
                    <a class="add_wishlist hasTooltip <?php echo in_array($product->virtuemart_product_id, $allprod['id']) ? 'go_to_whishlist active' : ''; ?>"
                       title="<?php echo JText::_('ADD_TO_WHISHLIST'); ?>"
                       data-toggle="tooltip"
                       onclick="ZtVirtuemarter.wishlist.add('<?php echo $product->virtuemart_product_id; ?>');">
                        <i class="fa fa-heart-o"></i>
                        <span><?php echo JText::_("ADD_TO_WHISHLIST"); ?></span>
                    </a> */ ?>
                <?php endif; ?>
            </div>
        <?php endif;
    }

    public static function getZtvirtuemarterSetting()
    {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ztvirtuemarter'));
        $query->where($db->quoteName('id') . ' = 1');
        $db->setQuery($query);
        $results = $db->loadObjectList();

        if (isset($results[0]) && !empty($results[0]->setting)) {
            return json_decode($results[0]->setting);
        } else {
            $setting = '{"enable_wishlist":"1","enable_compare":"1","enable_quickview":"1","enable_countdown":"1","enable_photozoom":"1","enable_auto_insert":"1"}';
            return json_decode($setting);
        }
        
    }

    public static function getCountdown($product)
    {
        if (self::getZtvirtuemarterSetting()->enable_countdown == '1' && ($product->prices['product_price_publish_down'] > 0)): ?>
            <div class="countdown-<?php echo $product->virtuemart_product_id; ?>">
                <?php $time = strtotime($product->prices['product_price_publish_down']); ?>
                <div class="count_holder">
                    <div id="product-countdown-<?php echo $product->virtuemart_product_id ?>"></div>
                    <script type="text/javascript">
                        (function ($) {
                            $('#product-countdown-<?php echo $product->virtuemart_product_id ?>').countdown("<?php echo date('Y/m/d', $time)?>", function (event) {
                                $(this).text(event.strftime('%D days %H:%M:%S')
                                );
                            });
                        })(jQuery)
                    </script>
                </div>
            </div>
        <?php
        endif;
    }
}

?>