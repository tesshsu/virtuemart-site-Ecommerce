<?php
/**
 *
 * Template for the shipment selection
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
 * @version $Id: cart.php 2400 2010-05-11 19:30:47Z milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


 

	if ($this->layoutName!='default') {
		$headerLevel = 1;
		if($this->cart->getInCheckOut()){
			$buttonclass = 'button vm-button-correct';
		} else {
			$buttonclass = 'default';
		}
		?>
<form method="post" id="userForm" name="chooseShipmentRate" action="<?php echo JRoute::_('index.php'); ?>" class="form-validate">
	<?php
	} else {
		$headerLevel = 3;
		$buttonclass = 'vm-button-correct';
	}
 

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

	?>
 


<?php
    if ($this->found_shipment_method ) {

	   echo '<fieldset class="vm-payment-shipment-select vm-shipment-select">';
		// if only one Shipment , should be checked by default
	    $count = 0;
	    foreach ($this->shipments_shipment_rates as $shipment_shipment_rates) {
			if (is_array($shipment_shipment_rates)) {
				foreach ($shipment_shipment_rates as $shipment_shipment_rate) {	
					
					if( $is_distributor_devis && strpos($shipment_shipment_rate, 'id_873') ) {
						echo '<div class="vm-payment-plugin-single">'.$shipment_shipment_rate.'</div>';
					} 
					elseif( !$is_distributor_devis && $is_distributor && !strpos($shipment_shipment_rate, 'id_874') && !strpos($shipment_shipment_rate, 'id_873') && !strpos($shipment_shipment_rate, 'id_797') ) {
							echo '<div class="vm-payment-plugin-single">'.$shipment_shipment_rate.'</div>';
					}
					elseif( !$is_distributor_devis && !$is_distributor ) {
							echo '<div class="vm-payment-plugin-single">'.$shipment_shipment_rate.'</div>';
					}

					$count++;
				}
			}
			
	    }
	    echo '</fieldset>';
    } else {
	 echo '<h'.$headerLevel.'>'.$this->shipment_not_found_text.'</h'.$headerLevel.'>';
    }
?>

    <button  name="updatecart" class="button" type="submit" ><?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?></button>
    <?php   if ($this->layoutName!='default') { ?>
    <button class="button" type="reset" onClick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=cancel'); ?>'" ><?php echo vmText::_('COM_VIRTUEMART_CANCEL'); ?></button>
    <?php  } ?> 

<?php
if ($this->layoutName!='default') {
?> <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="updatecart" />
    <input type="hidden" name="controller" value="cart" />
</form>
<?php
}
?>

