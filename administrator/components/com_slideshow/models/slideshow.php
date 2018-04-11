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
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.helper');

class SlideshowModelSlideshow extends JModelAdmin {

    public function getTable($type = 'Slideshow', $prefix = 'SlideshowTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {

        $form = $this->loadForm(
                $this->option . '.slideshow', 'slideshow', array('control' => 'jform', 'load_data' => $loadData)
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState($this->option . '.editslideshow.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getSlideshow() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itslideshow_slideshows');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getPropertie() {
        $db = JFactory::getDBO();
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->select('#__huge_itslideshow_images.name as name,'
                . '#__huge_itslideshow_images.id ,'
                . '#__huge_itslideshow_slideshows.name as portName,'
                . 'slideshow_id, #__huge_itslideshow_images.description as description,image_url,sl_url,sl_type,link_target,#__huge_itslideshow_images.ordering,#__huge_itslideshow_images.published,published_in_sl_width');
        $query->from(array('#__huge_itslideshow_slideshows' => '#__huge_itslideshow_slideshows', '#__huge_itslideshow_images' => '#__huge_itslideshow_images'));
        $query->where('#__huge_itslideshow_slideshows.id = slideshow_id')->where('slideshow_id=' . $id_cat);
        $query->order('ordering desc');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getImageByID() {
        $db = JFactory::getDBO();
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itslideshow_images');
        $query->where('slideshow_id=' . $id_cat);
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

   public function save($data) {
        $db = JFactory::getDBO();       
        $result = $this->getPropertie();
        $this->updarteSlideshow();
        $this->selectStyle();
        foreach ($result as $key => $value) {
            $imageId = intval($value->id);
            $id = $data['imageId'. $imageId];
            $titleimage = $db->escape($data['titleimage' . $imageId]);
            $im_description = $db->escape($data['im_description'. $imageId]);
            $sl_url = $db->escape($data['sl_url'. $imageId]);
            $sl_link_target = $db->escape($data['sl_link_target'. $imageId]);
            $ordering = $data['order_by_'. $imageId];
            $image_url = $db->escape($data['image_url'. $imageId]);
                                
            $query = $db->getQuery(true);
            $query->update('#__huge_itslideshow_images')->set('name="' . $titleimage . '"')->set('description="' . $im_description . '"')
                    ->set('sl_url="' . $sl_url . '"')->set('link_target="' . $sl_link_target . '"')
                    ->set('ordering="' . $ordering . '"')->set('image_url="' . $image_url . '"')->where('id=' . $imageId);
            $db->setQuery($query);
            $db->execute();
            
        }      
        
    }

    function updarteSlideshow() {
        $db = JFactory::getDBO();
        $data = JRequest::get('post');
        $name = $data['name'];
        $slideshow_effects_list = $data['slideshow_effects_list'];
        $sl_width = $data['sl_width'];
        $sl_height = $data['sl_height'];
        $pause_on_hover = $data['pause_on_hover'];
        $sl_pausetime = $data['sl_pausetime'];
        $sl_changespeed = $data['sl_changespeed'];
        $sl_position = $data['sl_position'];
        $id_cat = intval(JRequest::getVar('id'));

        $query = $db->getQuery(true);
        $query->update('#__huge_itslideshow_slideshows')->set('name ="' . $name . '"')
                ->set('sl_height="' . $sl_height . '"')->set('slideshow_list_effects_s="' . $slideshow_effects_list . '"')
                ->set('pause_on_hover="' . $pause_on_hover . '"')
                ->set('param="' . $sl_changespeed . '"')
                ->set('sl_position="' . $sl_position . '"')->set('description="' . $sl_pausetime . '"')->set('sl_width="' . $sl_width . '"')->where('id="' . $id_cat . '"');
        $db->setQuery($query);
        $db->execute();
    }

    function selectStyle() {
        $db = JFactory::getDBO();
        $data = JRequest::get('post');
        $styleName = $data['slideshow_effects_list'];
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->update('#__huge_itslideshow_slideshows')->set('slideshow_list_effects_s ="' . $styleName . '"')->where('id="' . $id_cat . '"');
        $db->setQuery($query);
        $db->execute();
    }

    public function saveCat() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->insert('#__huge_itslideshow_slideshows', 'id')->set('name = "New Slideshow"')
                ->set('sl_height = 375')
                ->set('sl_width = 600')
                ->set('pause_on_hover = "on"')
                ->set('slideshow_list_effects_s = "cubeH"')
                ->set('description=4000')
                ->set('param = 1000')
                ->set('sl_position = "left"');
        $db->setQuery($query);
        $db->execute();
        return $db->insertid();
    }
    
    private function getNumber($slideshowId) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('max(ordering) as maximum');
        $query->from('#__huge_itslideshow_images');
        $query->where('slideshow_id=' . $slideshowId);
        $db->setQuery($query);
        $results = $db->loadResult();
        return $results;        
    }

    function saveProject($imageUrl, $slideshowId) {
        $imageUrl = $imageUrl;
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        
        $ordering = $this->getNumber($slideshowId) + 1;
        $query->insert('#__huge_itslideshow_images', 'id')->set('slideshow_id = "' . $slideshowId . '"')
                ->set('image_url= "' . $imageUrl . '"')
                ->set('sl_type= "image"')
                ->set('ordering= "'.$ordering.'"');
        $db->setQuery($query);
        $db->execute();
        return $slideshowId;
    }

    public function deleteProject() {
        $id_cat = intval(JRequest::getVar('removeslide'));
        $id = intval(JRequest::getVar('id'));
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->delete('#__huge_itslideshow_images')->where('id =' . $id_cat);
        $db->setQuery($query);
        $db->execute();
        return;
    }
}
