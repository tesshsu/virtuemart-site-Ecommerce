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
jimport('joomla.application.component.controller');

class SlideshowController extends  JControllerLegacy{
    public function display($cachable = false, $urlparams = array()) {
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'Slideshows'));
        parent::display($cachable);
    }

}
