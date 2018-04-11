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
foreach ($items as $item) {
    if ($item->query['view'] === 'wishlists')
        $itemid = $item->id;
}

if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_wishlist == '1') :
    ?>
    <div class="mod-wishlist mod-wishlist-header">
        <div id="cur-lang" class="header-button-wishlist">
            <div id="wishlist_total">
                <a class="wishlist_total heading"
                   href="<?php echo JRoute::_('index.php?option=com_ztvirtuemarter&view=wishlist&Itemid=' . $itemid . ''); ?>">
                    <i class="fa fa-heart-o"></i>
                <span>
                    <?php if ($user->guest) {
                        echo count($wishlistIds);
                    } else {
                        echo count($allprod['id']);
                    } ?>
                </span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>