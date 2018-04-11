<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Plugin
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// No direct access
defined('_JEXEC') or die;

class plgSystemZtvirtuemarterInstallerScript
{

    public function postflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions')->set('enabled=1')->where('type=' . $db->q('plugin'))->where('element=' . $db->q('ztvirtuemarter'));
        $db->setQuery($query)->execute();

    }
}