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
    if ($item->query['view'] === 'comparelist') {
        $itemid = $item->id;

    }
}
if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_compare == '1') :
    ?>
    <div class="mod-compare">
        <div id="cur-lang" class="header-button-compare">
            <div id="compare_total">
                <a class="compare_total heading"
                   href="<?php echo JRoute::_('index.php?option=com_ztvirtuemarter&view=comparelist&Itemid=' . $itemid . ''); ?>">
                    <i class="fa fa-files-o"></i>
                    <span><?php echo count($compareIds); ?></span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>