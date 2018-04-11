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
jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');
?>

<?php
class SlideshowViewFeatured extends JViewLegacy
{
	protected $item;
        protected $form;
        protected $script;

	public function display($tpl = null)
	{
		try
		{
			
			$this->form = $this->get('Form');
			$this->item = $this->get('Item');
                        JHtml::stylesheet(Juri::root() . 'media/com_Slideshow/style/portfolios.style.css');
                        $this->addToolBar();
                        parent::display($tpl);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	protected function addToolBar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		JToolBarHelper::title( JText::_('COM_SLIDESHOW_MANAGER_SLIDESHOW_FEAUTURED_OPTIONS'), 'COM_SLIDESHOW_MANAGER_SLIDESHOW_FEAUTURED_OPTIONS');
		JToolBarHelper::cancel('general.cancel');
	}

}