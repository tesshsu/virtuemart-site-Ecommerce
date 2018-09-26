<?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
?>
<?php defined('_JEXEC') or die('Restricted access'); 

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJSColor extends JFormFieldList  {

    protected $type = 'jscolor';

    public function getOptions() {

        
         
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id, name, value');
        $query->from('#__huge_itslideshow_params');
        $query->where('name="' . $this->element['name'] . '"');
        $db->setQuery($query);
        $results = $db->loadAssocList();

        $query1 = $db->getQuery(true);
        $query1->select('*');
        $query1->from('#__huge_itslideshow_slideshows');
        //$query1->where('id = '.$exp2[0]);
        $db->setQuery($query1);
        $results2 = $db->loadAssocList();
        $type_ = $this->element['type_'];
       
       $options = array();
            foreach ($results2 as $rowpar) {
                $port_name = $rowpar['name'];
                $port_id = $rowpar['id'];
                $options[$port_id] = $port_name;
            }
        return $options;;
        
        
    }

}
