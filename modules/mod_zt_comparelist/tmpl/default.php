<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Comparelist Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');
JFactory::getLanguage()->load('com_ztvirtuemarter');
$items = JFactory::getApplication()->getMenu('site')->getItems('component', 'com_ztvirtuemarter');
$itemid = '';
foreach ($items as $item) {
    if ($item->query['view'] === 'comparelist') {
        $itemid = $item->id;
    }
}
if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_compare == '1') :
    ?>
    <div class="ajax-dropdown vmgroup<?php echo $params->get('moduleclass_sfx') ?>" id="mod_compare">
        <div class="seldcomp" id="butseldcomp">
            <?php if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_compare == '1') : ?>
                <a class="btn-compare"
                   href="<?php echo JRoute::_('index.php?option=com_ztvirtuemarter&view=comparelist&Itemid=' . $itemid); ?>">
                    <i class="fa fa-files-o hover-dropdown"></i>
            <span>
		   <?php echo count($compareIds); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="zt-cart-inner">
            <div class="vmproduct">
                <?php
                if (count($prods) > 0) :
                    foreach ($prods as $product) : ?>
                        <div id="compare_prod_<?php echo $product->virtuemart_product_id; ?>"
                             class="modcompareprod clearfix">
                            <div class="compare-product-img">
                                <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id); ?>">
                                    <?php echo $product->images[0]->displayMediaThumb('alt="' . $product->product_name . '" title="' . $product->product_name . '" ', FALSE) ; ?>

                                </a>
                            </div>
                            <div class="compare-product-detail">
                                <div class="name">
                                    <?php echo JHTML::link($product->link, $product->product_name); ?>
                                </div>
                                <div class="remcompare">
                                    <a class="tooltip-1" title="remove"
                                       onclick="ZtVirtuemarter.compare.remove('<?php echo $product->virtuemart_product_id; ?>');">
                                        <i class="fa fa-times"></i><?php echo JText::_('REMOVE'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="not_text compare"><?php echo JText::_('YOU_HAVE_NO_PRODUCT_TO_COMPARE'); ?></div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>
	
