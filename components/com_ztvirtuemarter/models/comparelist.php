<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

class ZtvirtuemarterModelComparelist extends JModelLegacy
{
    /**
     * Class constructor
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     *
     * @param null
     * @return Array object
     */
    public function getProducts()
    {
        $mainframe = JFactory::getApplication();
        $compareIds = $mainframe->getUserState( "com_ztvirtuemarter.site.compareIds", array() );

        $productModel = VmModel::getModel('product');

        $prods = $productModel->getProducts($compareIds);
        $productModel->addImages($prods, 1);

        return $prods;
    }
}
