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
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldPortfolio extends JFormFieldList
{
   
    protected $type = 'Slider';
    protected function getOptions() 
    {
       
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, name')
                ->from('#__huge_itslider_sliders');
        $db->setQuery($query);
        $messages = $db->loadObjectList();
        $options = array();

        if ($messages)
        {
            foreach($messages as $message) 
            {
                $options[] = JHtml::_('select.option', $message->id, $message->name);
            }
        }
        $options = array_merge(parent::getOptions(), $options);
       
        return $options;
    }
}
