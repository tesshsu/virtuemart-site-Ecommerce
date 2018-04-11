<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div class="manufacturer">
    <?php
    $i = 1;
    $manModel = VmModel::getModel('manufacturer');
    $mans = array();
    // Gebe die Hersteller aus
    foreach ($this->product->virtuemart_manufacturer_id as $manufacturer_id) :

        $manufacturers_details = $manModel->getManufacturer($manufacturer_id);

        //Link to products
        $link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturer_id, FALSE);
        $name = $manufacturers_details->mf_name;

        // Avoid JavaScript on PDF Output
        if (strtolower(vRequest::getCmd('output')) == "pdf") :
            $mans[] = JHtml::_('link', $link, $name);
        else :
            $mans[] = '<a class="manuModal" rel="{handler: \'iframe\', size: {x: 700, y: 850}}" href="' . $link . '">' . $name . '</a>';
        endif;
    endforeach;
    echo implode(', ', $mans);
    ?>
</div>