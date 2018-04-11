<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */


// No direct access
defined('_JEXEC') or die;


/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */
class ZtvirtuemarterViewWishlist extends JViewLegacy
{
    public $products;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        vmJsApi::jPrice();
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_ztvirtuemarter', true);
        JHtml::_('behavior.modal');
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_('COM_WISHLISTS_PRODUCT'), JRoute::_('index.php?option=com_ztvirtuemarter&view=wishlist'));

        $document = JFactory::getDocument();
        $document->addScript(Juri::root() . '/components/com_ztvirtuemarter/views/wishlist/tmpl/js/jquery.lazyload.min.js');

        $this->products = $this->get('Products');
        parent::display($tpl);
    }
}