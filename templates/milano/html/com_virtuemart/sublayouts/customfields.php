<?php
/**
* sublayout products
*
* @package	VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle'])? $viewData['customTitle']: false;;
if(isset($viewData['class'])){
	$class = $viewData['class'];
} else {
	$class = 'product-fields';
}

if (!empty($product->customfieldsSorted[$position])) {
	


	if($position == 'normal') { ?>

	<div class="<?php echo $class?> product-options-wrapper">
		<?php
		if($customTitle and isset($product->customfieldsSorted[$position][0])){
			$field = $product->customfieldsSorted[$position][0]; ?>
		<div class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
			<?php if ($field->custom_tip) {
				echo JHtml::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
			} ?>
		</div> <?php
		}
		$custom_title = null;
		foreach ($product->customfieldsSorted[$position] as $field) {
			if ( $field->is_hidden || empty($field->display)) continue; //OSP http://forum.virtuemart.net/index.php?topic=99320.0
			?><div class="option-view product-field-type-<?php echo $field->field_type ?>">
				 
				<?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
					<span class="product-field-label"><?php echo vmText::_ ($field->custom_title) ?> 
						<?php if ($field->custom_tip) {
							echo JHtml::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
						} ?></span>
				<?php }

				if (!empty($field->display)){
					?><?php echo $field->display ?><?php
				}
				if (!empty($field->custom_desc)){
					?><?php echo vmText::_($field->custom_desc) ?> <?php
				}
				
				
				//DEBUT BOUCLE 
       				$imageName='';

			        if($field->custom_element == 'country' && !empty($field->display)) {
			            $imageName='country.jpg';
			            
			        }
			        else if($field->custom_element == 'culture' && !empty($field->display)) {
			            $imageName='culture.png';
			            
			        }
			        else if($field->custom_element == 'percentage' && !empty($field->display)) {
			            $imageName='percentage.jpg';
			                
			        }
			        else if($field->custom_element == 'conservative' && !empty($field->display)) {
			            $imageName='conservative.jpg';
			            
			        }
			        else if($field->custom_element == 'method' && !empty($field->display)) {
			            $imageName='method.jpg';
			            
			        }
			        else if($field->custom_element == 'certificate_file' && !empty($field->display)) {
			            $imageName='certificate_file.jpg';
			            
			        }
			        else if($field->custom_element == 'certifications' && !empty($field->display)) {
			            $imageName='certifications.jpg';
			            
			        }
			        else if($field->custom_element == 'plant_elements' && !empty($field->display)) {
			            $imageName='plant_elements.png';
			            
			        }
			        else if($field->custom_element == 'aromatogrammes' && !empty($field->display)) {
			            $imageName='aromatogrammes.png';
			            
			        }
			        else if($field->custom_element == 'natural' && !empty($field->display)) {
			            $imageName='natural.jpg';
			            
			        }
			        else if($field->custom_element == 'certified_airless' && !empty($field->display)) {
			            $imageName='certified_airless.png';
			            
			        }
			        else if($field->custom_element == 'usage_alimentaire' && !empty($field->display)) {
			            $imageName='usage_alimentaire.png';
			            
			        }  

			        if( $imageName != '') { ?>
			       
			    
			<?php  } //FIN BOUCLE

				//var_dump($field); ?>

			</div>
		<?php
			$custom_title = $field->custom_title;
		} ?>
      <div class="clear"></div>
	</div>

	<?php
	} else { ?>

		<div class="<?php echo $class?> product-options-wrapper">
			<?php
			if($customTitle and isset($product->customfieldsSorted[$position][0])){
				$field = $product->customfieldsSorted[$position][0]; ?>
			<div class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
				<?php if ($field->custom_tip) {
					echo JHtml::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
				} ?>
			</div> <?php
			}
			$custom_title = null;
			foreach ($product->customfieldsSorted[$position] as $field) {
				if ( $field->is_hidden || empty($field->display)) continue; //OSP http://forum.virtuemart.net/index.php?topic=99320.0
				?><div class="option-view product-field-type-<?php echo $field->field_type ?>">
					<?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
						<span class="product-field-label"><?php echo vmText::_ ($field->custom_title) ?> 
							<?php if ($field->custom_tip) {
								echo JHtml::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
							} ?></span>
					<?php }
					if (!empty($field->display)){
						?><?php echo $field->display ?><?php
					}
					if (!empty($field->custom_desc)){
						?><?php echo vmText::_($field->custom_desc) ?> <?php
					}
					?>
				</div>
			<?php
				$custom_title = $field->custom_title;
			} ?>
	      <div class="clear"></div>
		</div>

	<?php 
	}
} ?>