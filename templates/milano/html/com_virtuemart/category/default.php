<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8811 2015-03-30 23:11:08Z Milbo $
 */

defined ('_JEXEC') or die('Restricted access');

 
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";
vmJsApi::addJScript('vm.hover',$js);

if (empty($this->keyword) and !empty($this->category)) {
	?>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>
<?php
}

// Show child categories
if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if (!empty($this->category->haschildren)) {

		echo ShopFunctionsF::renderVmSubLayout('categories',array('categories'=>$this->category->children));

	}
}

if($this->showproducts){ 
if (!empty($this->keyword)) {
	//id taken in the view.html.php could be modified
	$category_id  = vRequest::getInt ('virtuemart_category_id', 0); ?>
	<h3><?php echo $this->keyword; ?></h3>
	
	<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0', FALSE); ?>" method="get">

		<!--BEGIN Search Box -->
		<div class="virtuemart_search">
			<?php echo $this->searchcustom ?>
			<br/>
			<?php echo $this->searchCustomValues ?>
			<input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
			<input type="submit" value="<?php echo vmText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="btn btn-primary" onclick="this.form.keyword.focus();"/>
		</div>
		<input type="hidden" name="search" value="true"/>
		<input type="hidden" name="view" value="category"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>"/>

	</form>
	<!-- End Search Box -->
<?php  } ?>

<?php // Show child categories

	?> 
 
<?php 

// Show orderby & displaynumber ?>
	<div class="orderby-displaynumber">				
		<div class="pull-left vm-order-list view-options ">
        	<?php echo $this->orderByList['orderby']; ?> 
			<?php //<div class="display-number"><?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); </div> ?>

			<?php
				//BRUNO CAMELEON : Fix du selecteur de nombre de produits
				$current_url = explode("?", $_SERVER['REQUEST_URI']);
				$limite = $_GET['limit'];
				$dix;$vingt;$trente;$cinquante;$cent;
				switch ($limite) {
					case 10:
						$dix = 'selected=selected';
						break;
					case 30:
						$trente = 'selected=selected';
						break;
					case 50:
						$cinquante = 'selected=selected';
						break;
					case 100:
						$cent = 'selected=selected';
						break;
					default:
						$vingt = 'selected=selected';
						break;
				}

			?>
			<select id="limit" name="" class="inputbox" size="1" onchange="window.top.location.href=this.options[this.selectedIndex].value">
				<option value="<?php echo $current_url[0]; ?>?limit=10" <?php echo $dix; ?>>10</option>
				<option value="<?php echo $current_url[0]; ?>?limit=20" <?php echo $vingt; ?>>20</option>
				<option value="<?php echo $current_url[0]; ?>?limit=30" <?php echo $trente; ?>>30</option>
				<option value="<?php echo $current_url[0]; ?>?limit=50" <?php echo $cinquante; ?>>50</option>
				<option value="<?php echo $current_url[0]; ?>?limit=100" <?php echo $cent; ?>>100</option>
			</select>


        </div>  
        <div class="pull-right list-grid">
			<ul>
				<li class="Cgrid"><a href="categories-gridview.html" class="active">Grid</a></li>
				<li class="Clist"><a href="categories-listview.html">List</a></li>
			</ul>
		</div>
        <div class="clr"></div> 
	</div> 
<!-- end of orderby-displaynumber -->
<div class="clr"></div> 
 

	<?php
	if (!empty($this->products)) {
	$products = array();
	$products[0] = $this->products;
	echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));

	?>

<div class="vm-pagination vm-pagination-bottom"><?php echo $this->vmPagination->getPagesLinks (); ?></div>

	<?php
} elseif (!empty($this->keyword)) {
	echo vmText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
 } ?> 

<?php
$j = "Virtuemart.container = jQuery('.category-view');
Virtuemart.containerSelector = '.category-view';";

vmJsApi::addJScript('ajaxContent',$j);
?>
<!-- end browse-view -->

<script type="text/javascript">
 jQuery(document).ready(function($) { 
	$('.Cgrid').click(function() {
		$('.Cgrid a').addClass('active');
		$('.Clist a').removeClass('active');
		$('#product_list').fadeOut(300, function() {
			$(this).addClass('grid').removeClass('list').fadeIn(300);
		}); 
		return false;
	});
	
	$('.Clist').click(function() {
		$('.Clist a').addClass('active');
		$('.Cgrid a').removeClass('active');						  
		$('#product_list').fadeOut(300, function() {
			$(this).removeClass('grid').addClass('list').fadeIn(300);
		}); 
		return false;
	}); 

});
</script>