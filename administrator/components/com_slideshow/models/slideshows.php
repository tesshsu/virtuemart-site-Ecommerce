<?php 
/**
 * @package  Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
?>
<?php defined('_JEXEC') or die('Restricted access'); 
jimport('joomla.application.component.modellist');

class SlideshowModelSlideshows extends JModelList {

    public function getListQuery() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itslideshow_slideshows');
        return $query;
    }

    public function getSlideshow() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('#__huge_itslideshow_slideshows.name, #__huge_itslideshow_slideshows.id,count(*) as count');
        $query->from(array('#__huge_itslideshow_slideshows' => '#__huge_itslideshow_slideshows', '#__huge_itslideshow_images' => '#__huge_itslideshow_images'));
        $query->where('#__huge_itslideshow_slideshows.id = slideshow_id');
        $query->group('#__huge_itslideshow_slideshows.name');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getOther() {
        $db = JFactory::getDBO();
        $query2 = $db->getQuery(true);
        $query2->select('#__huge_itslideshow_slideshows.name, #__huge_itslideshow_slideshows.id,0 as count');
        $query2->from('#__huge_itslideshow_slideshows');
        $query2->where('#__huge_itslideshow_slideshows.id not in (select slideshow_id from #__huge_itslideshow_images)');
        $db->setQuery($query2);

        $results = $db->loadObjectList();
        return $results;
    }

}
