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
jimport('joomla.application.component.controllerform');

class SlideshowControllerGeneral extends JControllerForm
{
    function save($key = null, $urlVar = null) {
        $model = $this->getModel('General');
        $item=$model->apply1();
        $this->setRedirect(JRoute::_('index.php?option=com_Slideshow&view=Slideshows', false),JText::_('COM_SLIDESHOW_SAVE'));

    }

    function  cancel($key = NULL){
            $this->setRedirect(
            JRoute::_('index.php?option=com_Slideshow&view=Slideshows', false));
        }
        
        function apply1(){
             $model = $this->getModel('General');
             $item=$model->apply1();
             $this->setRedirect(JRoute::_('index.php?option=com_Slideshow&view=general', false), JText::_('COM_SLIDESHOW_SAVE'));

        }
}
