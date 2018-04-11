<?php
/**
 *
 * Layout for the payment selection
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: select_payment.php 8847 2015-05-06 12:22:37Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$addClass="";
 

if ($this->layoutName!='default') {
	$headerLevel = 1;
	if($this->cart->getInCheckOut()){
		$buttonclass = 'button vm-button-correct';
	} else {
		$buttonclass = 'default';
	}


?>
	<form method="post" id="paymentForm" name="choosePaymentRate" action="<?php echo JRoute::_('index.php'); ?>" class="form-validate <?php echo $addClass ?>">
<?php } else {
		$headerLevel = 3;
		$buttonclass = 'vm-button-correct';
	}
    ?>
 

<?php
		//BRUNO CAMELEONS : Récupération de la DB
		include_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
		$config = new JConfig();
		$db_cart = new PDO('mysql:host=localhost;dbname='.$config->db, $config->user, $config->password);
		$group_id_list = array();
		$is_distributor = false;
		$is_distributor_devis = false;

		//Récupération des groupes de l'utilisateur
		$group_request = $db_cart->query('SELECT virtuemart_shoppergroup_id 
										FROM h8q2p_virtuemart_vmuser_shoppergroups
										WHERE virtuemart_user_id = '.$this->cart->user->virtuemart_user_id);

		while($group_data = $group_request->fetch()) {
			array_push($group_id_list, $group_data['virtuemart_shoppergroup_id']);
		}

		// Si l'utilisateur est assigné à au moins deux groupes, c'est forcément un distributeur

		if(count($group_id_list) > 1 ) {
			$is_distributor = true;
		}

		//Est-ce un distributeur avec devis ?
		foreach ($group_id_list as $group_id) {
			if($group_id == 25){
				$is_distributor_devis = true;
			}
		}

	 $count=0;
     if ($this->found_payment_method ) {
     	echo '<fieldset class="vm-payment-shipment-select vm-payment-select">';
		foreach ($this->paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
		    	
				foreach ($paymentplugin_payments as $paymentplugin_payment) {
					
				    if( $is_distributor_devis && strpos($paymentplugin_payment, 'id_5') ) {
				    	$newstring = str_replace('input', 'input checked="checked"', $paymentplugin_payment);
						echo '<div class="vm-payment-plugin-single">'.$newstring.'</div>';
					} 
					elseif( !$is_distributor_devis && !strpos($paymentplugin_payment, 'id_5') ) {
						echo '<div class="vm-payment-plugin-single">'.$paymentplugin_payment.'</div>';
					}

				}
		    }
		    $count++;
		}
    echo '</fieldset>';

    } else {
	 echo '<h1>'.$this->payment_not_found_text.'</h1>';
    }
?>


<button name="updatecart" class="button" type="submit"><?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?></button>

   <?php   if ($this->layoutName!='default') { ?>
<button class="button" type="reset" onClick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=cancel'); ?>'" ><?php echo vmText::_('COM_VIRTUEMART_CANCEL'); ?></button>
	<?php  } ?> 
<?php
if ($this->layoutName!='default') {
?>    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="updatecart" />
    <input type="hidden" name="controller" value="cart" />
</form>
<?php
}
?>