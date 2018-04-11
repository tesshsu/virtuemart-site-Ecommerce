<?php
/**
*
* Layout for the shopping cart, look in mailshopper for more details
*
* @package	VirtueMart
* @subpackage Order
* @author Max Milbers, Valerie Isaksen
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
    <tr>
    <td>
<?php
//	echo vmText::_('COM_VIRTUEMART_CART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
	echo vmText::sprintf('COM_VIRTUEMART_MAIL_VENDOR_CONTENT',$this->vendor->vendor_store_name,$this->shopperName,$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total),$this->orderDetails['details']['BT']->virtuemart_order_id);
        
//FLORIAN CAMELEONS : ajout de la méthode de paiement dans le mail vendeur
$config = new JConfig();
$db_product = new PDO('mysql:host=localhost;dbname='.$config->db, $config->user, $config->password);
$product_request = $db_product->query('SELECT payment_name 
        FROM h8q2p_virtuemart_paymentmethods_fr_fr
        WHERE virtuemart_paymentmethod_id = '.$this->orderDetails['details']['BT']->virtuemart_paymentmethod_id);
while($product_data = $product_request->fetch()) {
    $payment_to_display = $product_data[payment_name];
}
echo("<br />Méthode de paiement : ".$payment_to_display);

if(!empty($this->orderDetails['details']['BT']->customer_note)){
	echo '<br /><br />'.vmText::sprintf('COM_VIRTUEMART_CART_MAIL_VENDOR_SHOPPER_QUESTION',$this->orderDetails['details']['BT']->customer_note).'<br />';
}

	?>
</td></tr></table>