<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;
?>
<div class="compare_box">
    <h3 class="module-title">
        <?php echo JText::_('COM_COMPARE_COMPARE_PRODUCT') ?>
    </h3>
    <div class="back-to-category">
        <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=virtuemart'); ?>"
           class="button_back button reset2" title="<?php echo jText::_('COM_VIRTUEMART_SHOP_HOME'); ?>">
            <i class="fa fa-reply"></i><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO', jText::_('COM_VIRTUEMART_SHOP_HOME')) ?>
            <span></span></a>
    </div>
    <div class="clear"></div>
    <?php
    $currency = CurrencyDisplay::getInstance();
    $ratingModel = VmModel::getModel('ratings');
    $table = array();
    if (!empty($this->products)) {
        ?>
        <div class="browseview browscompare_list">
            <?php
            $table['name'][] = '<div class="compare_name">' . JText::_('DR_PRODUCT_NAME') . '</div>';
            $table['image'][] = '<div class="compare_image">' . JText::_('DR_PRODUCT_IMAGE') . '</div>';

            $showRating = $ratingModel->showRating($product->virtuemart_product_id);
            if ($showRating == 'true')
                $table['rating'][] = '<div class="compare_rating">' . JText::_('DR_PRODUCT_RATING') . '</div>';

            $table['price'][] = '<div class="compare_price">' . JText::_('DR_PRODUCT_PRICE') . '</div>';
            $table['desc'][] = '<div class="compare_desc">' . JText::_('DR_PRODUCT_DESCRIPTION') . '</div>';
            $table['brand'][] = '<div class="compare_brand">' . JText::_('DR_PRODUCT_MANUFACTURER') . '</div>';
            $table['stock'][] = '<div class="compare_stock">' . JText::_('DR_PRODUCT_AVAILABILITY') . '</div>';
            $table['code'][] = '<div class="compare_code">' . JText::_('DR_PRODUCT_CODE') . '</div>';
            $table['weight'][] = '<div class="compare_weight">' . JText::_('DR_PRODUCT_WEIGHT') . '</div>';
            $table['dim'][] = '<div class="compare_dim">' . JText::_('DR_PRODUCT_DIMENSIONS') . '</div>';
            $table['pack'][] = '<div class="compare_pack">' . JText::_('DR_PRODUCT_PACKAGING') . '</div>';
            $table['unit'][] = '<div class="compare_unit">' . JText::_('DR_PRODUCT_UNITS_BOX') . '</div>';
            $table['action'][] = '<div class="compare_action">' . JText::_('DR_PRODUCT_ACTION') . '</div>';

            foreach ($this->products as $product) {

                if (!empty($this->products)) {
                    
                $isSale = (!empty($product->prices['salesPriceWithDiscount'])) ? 1 : 0; 
                if ($isSale) {
                		$dtaxs = array();
                		if($product->prices["DATax"]) $dtaxs = $product->prices["DATax"];
                		if($product->prices["DBTax"]) $dtaxs = $product->prices["DBTax"];			
                		foreach($dtaxs as $dtax){
                			if(!empty($dtax)) {
                				$discount = rtrim(rtrim($dtax[1],'0'),'.');
                				$operation = $dtax[2];
                				$percentage = "";					
                				switch($operation) {
                					case '-':
                						$percentage = "-".$discount;
                						break;
                					case '+':
                						$percentage = "+".$discount;
                						break;
                					case '-%':
                						$percentage = "-".$discount."%";
                						break;
                					case '+%':
                						$percentage = "+".$discount."%";
                						break;
                					default:
                						return true;	
                				}
                                $product->percentage = $percentage;	
                			}					
                		}
                	}  
                    $features =   $product->product_special; 
                    
                    $htmlLabel = '';
                    $dateDiff = date_diff(date_create(), date_create($product->product_available_date)); 
                    if ($dateDiff->days < 7) {
                        $htmlLabel .= '<div class="product-label product-new-label"><span>New</span></div>';
                    }
                    if ($features) {
        				$htmlLabel .= '<div class="product-label product-hot-label"><span>Hot</span></div>';
        			}
                    if (isset($product->percentage)) {
                        $htmlLabel .= '<div class="product-label product-sale-label"><span>'.$product->percentage.'</span></div>';
                    }  

                    $table['name'][] = '<div class="compare_name"><h5>' . JHTML::link($product->link, shopFunctionsF::limitStringByWord($product->product_name, '40', '...')) . '</h5></div>';

                    $table['desc'][] = '<div class="compare_desc"><div class="product_s_desc">' . shopFunctionsF::limitStringByWord($product->product_s_desc, 150, '...') . '</div></div>';

                    $table['brand'][] = '<div class="compare_brand">' . !empty($product->mf_name) ? $product->mf_name . '</div>' : JText::_('EMPTY') . '</div>';

                    $table['code'][] = '<div class="compare_code"><div class="code"></span>' . $product->product_sku . '</div></div>';

                    $table['stock'][] = '<div class="compare_stock">' . ($product->product_in_stock >= 1) ? '<div class="stock"></span><span class="green">' . JText::_('DR_IN_STOCK') . '</span> ' . $product->product_in_stock . ' ' . JText::_('DR_ITEMS') . '</div></div>' : '<div class="stock"></span><span class="red">' . JText::_('DR_OUT_STOCK') . '</span></div></div>';

                    $table['weight'][] = '<div class="compare_weight">' . ($product->product_weight > 0) ? '<div>' . $product->product_weight . $product->product_weight_uom . '</div></div>' : '<div>empty</div></div>';

                    $table['pack'][] = '<div class="compare_pack">' . ($product->product_packaging > 0) ? '<div>' . $product->product_packaging . $product->product_unit . '</div></div>' : '<div>' . JText::_('EMPTY') . '</div></div>';

                    $table['unit'][] .= '<div class="compare_unit">' . ($product->product_box) ? '<div>' . $product->product_box . '</div></div>' : JText::_('EMPTY') . '</div>';

                    //show compare image
                    $image = '<div class="compare_image"><div class="browseImage">'; 
                    $image .= $htmlLabel;
                    $imageThumb = $product->images[0]->displayMediaThumb('class="browseProductImage" id="Img_to_Js_' . $product->virtuemart_product_id . '" border="0" title="' . $product->product_name . '" ', false, 'class="vm2_modal"');
                    $image .= JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $imageThumb);
                    $image .= '</div></div>';
                    $table['image'][] = $image;
                    //end of show compare image

                    //show compare rating
                    if ($showRating == 'true') {
                        $rate = 0;
                        $rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
                        if (!empty($rating))
                            $rate = $rating->rating;
                        $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
                        $ratingwidth = ($rate * 100) / $maxrating;

                        $rating = '<div class="compare_rating"><div class="rating"><span class="vote">';
                        $rating .= '<span title="" class="vmicon ratingbox" style="display:inline-block;">';
                        $rating .= '<span class="stars-orange" style="width:' . $ratingwidth . '%">';
                        $rating .= '</span></span></span></span></div>';

                    }
                    $table['rating'][] = $rating;
                    //end of show compare rating

                    //show compare price
                    $price = '<div class="compare_price">';
                    if ((!empty($product->prices['salesPrice'])) && !$product->images[0]->file_is_downloadable) {
                        $price .= '<div class="price"><div id="productPrice' . $product->virtuemart_product_id . '" class="product-price">';
                        if ($product->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel')) {
                            $price .= "<strong>" . JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT') . ' (' . $product->product_unit . "):</strong>";
                        }
                        if (abs($product->prices['discountAmount']) > 0) {
                            $price .= '<span class="WithoutTax">' . $currency->createPriceDiv('basePriceWithTax', '', $product->prices) . '</span>';
                        }
                        $price .= $currency->createPriceDiv('salesPrice', '', $product->prices);
                        $price .= '</div></div>';
                        $price .= '</div>';
                    } else {
                        if ($product->prices['salesPrice'] <= 0 && VmConfig::get('askprice', 1)) {
                            $call_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id . '&tmpl=component');
                            $price .= '<div class="call-a-question">';
                            $price .= "<a class='call modal' rel=\"{handler: 'iframe',size: {x: 460, y: 550}}\"  href='" . $call_link . "' >" . JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE') . "</a>";
                            $price .= ' </div>';
                        }
                    }
                    $table['price'][] = $price;
                    //end of show compare price

                    //show compare Dimensions
                    $dim = '<div class="compare_dim">';
                    if (($product->product_length > 0) || ($product->product_width > 0) || ($product->product_height > 0)) {
                        $dim .= '<div>' . $product->product_length . $product->product_lwh_uom . ' x ' . $product->product_width . $product->product_lwh_uom . ' x ' . $product->product_height . $product->product_lwh_uom . '</div>';
                    } else {
                        $dim .= '<div>' . JText::_('EMPTY') . '</div>';
                    }
                    $table['dim'][] = $dim . '<div>';
                    //end of show compare Dimensions

                    //action for item
                    $action = '<div class="compare_action">';
                    if (!VmConfig::get('use_as_catalog', 0) and !empty($product->prices['salesPrice'])) {
                        $action .= '<div class="addtocart-area2">';
                        if (($product->product_in_stock < 1) || ($product->product_in_stock < $product->min_order_level) || ($product->product_in_stock - $product->product_ordered) < $product->min_order_level) {
                            $action .= '<a class="addtocart-button" title="' . JText::_('COM_VIRTUEMART_CART_NOTIFY') . '" href="' . JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $product->virtuemart_product_id) . '">' . JText::_('COM_VIRTUEMART_CART_NOTIFY') . '<span></span></a>';
                        } else {

                            $action .= '<form method="post" class="product" action="index.php" id="addtocartproduct' . $product->virtuemart_product_id . '">';

                            $action .= '';
                            if (isset($product->step_order_level) && (int)$product->step_order_level > 0) {
                                $minorder = $product->step_order_level;
                            } else if (!empty($product->min_order_level)) {
                                $minorder = $product->min_order_level;
                            } else {
                                $minorder = '1';
                            }
                            $action .= '<div class="addtocart-bar2">';
                            $action .= '<span class="box-quantity">';
                            $action .= '<span class="quantity-box"><input type="text" class="quantity-input js-recalculate" name="quantity[]" value="' . $minorder . '"/></span>';
                            $action .= '<span class="quantity-controls"><i class="quantity-controls quantity-plus">+</i><i class="quantity-controls quantity-minus">-</i></span>';
                            $action .= '</span>';
                            $action .= '<div class="clear"></div>';
                            $action .= '<span class="addtocart_button2"><input type="submit" value="' . JText::_('COM_VIRTUEMART_CART_ADD_TO') . '" title="' . JText::_('COM_VIRTUEMART_CART_ADD_TO') . '" class="addtocart-button cart-click"></span>';
                            $action .= '<input type="hidden" class="pname" value="' . $product->product_name . '"/>';
                            $action .= '<input type="hidden" name="option" value="com_virtuemart" />';
                            $action .= '<input type="hidden" name="view" value="cart" />';
                            $action .= '<input type="hidden" name="task" value="add" />';
                            $action .= '<input type="hidden" class="item_id" name="virtuemart_product_id[]" value="' . $product->virtuemart_product_id . '"/>';
                            $action .= '<input type="hidden" name="virtuemart_category_id[]" value="' . $product->virtuemart_category_id . '" />';

                            $action .= '</div>';
                            $action .= '</form>';
                        }
                        $action .= ' </div>';
                    }
                    $action .= '<div class="clear"></div>';
                    $action .= '<div class="remcompare"><a class="compare_del" title=""  onclick="ZtVirtuemarter.compare.remove(' . $product->virtuemart_product_id . ');"><i class="fa fa-times"></i>remove</a></div>';
                    $action .= '</div>';

                    $table['action'][] = $action;
                    //end of action
                }
            }
            ?>
            <!-- Render table of content -->
            <table id="compare_list_prod">
                <tbody>
                <?php foreach ($table as $name => $tr): ?>
                    <tr class="items  <?php echo $name; ?>" >
                        <?php foreach ($tr as $key => $td): ?>
                            <?php if($key <=4 ) :?>
                                <td><?php echo $td; ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>  
                <?php endforeach; ?>
                </tbody>
            </table>
            <!-- End table of content -->
        </div>
        <h3 class="module-title compare no-products" style="display:none;">
            <i class="fa fa-info-circle"></i><?php echo JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_COMPARE'); ?>
        </h3>
    <?php
    } else {
        echo '<h3 class="module-title compare no-products" ><i class="fa fa-info-circle"></i>' . JText::_('COM_VIRTUEMART_ITEMS_NO_PRODUCTS_COMPARE') . '</h3>';
    }
?>
</div>
    