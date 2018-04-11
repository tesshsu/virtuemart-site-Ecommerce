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

jimport('joomla.application.component.view');

class SlideshowViewSlideshows extends JViewLegacy
{
	
	protected $items;
	protected $pagination;
        protected $slideshow;
        protected $other;
       

	public function display($tpl = null)
	{
		try
		{
			
			$this->items = $this->get('Items');
                        $this ->slideshow = $this->get('Slideshow');
                        $this->other=$this->get('Other');
			$this->pagination = $this->get('Pagination');
                        JHtml::stylesheet(Juri::root() . 'media/com_slideshow/style/portfolios.style.css');
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

		JToolBarHelper::title(JText::_('COM_SLIDESHOW_MANAGER_SLIDESHOWS'),  JText::_('COM_SLIDESHOW_MANAGER_SLIDESHOWS'));
             	JToolBarHelper::addNew('slideshows.add');
                JToolBarHelper::divider();
		JToolBarHelper::editList('slideshow.edit');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'slideshows.delete');
	}
}
