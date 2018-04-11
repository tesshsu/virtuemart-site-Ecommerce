/*
 *
 * @Author sm-planet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2016 SM-planet.net - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */
 // prepare ajax request
var request_price = jQuery.ajax();

// zakaj je to tukaj???
var floatSign=',';
 
// set qty inputs, qty buttons and addtocart buttons trigger
jQuery(document).ready(function () {
	jQuery("form[name='catproduct_form'] .cell_quantity input").click( function() {
		changeQuantity(this);
	});
	jQuery("form[name='catproduct_form'] .cell_quantity button").click( function() {
		changeQuantity(this);
	});
	jQuery("form[name='catproduct_form'] .cell_quantity input").change( function() {
		changeQuantity(this);
	});
	jQuery("form[name='catproduct_form'] .cell_addToCart .addtocart-button").click( function() {
		handleToCart_VM3(this);
		return false;
	});
	jQuery("form[name='catproduct_form'] .cell_addToCartEach .addtocart-button").click( function() {
		handleToCartOne_VM3(this);
		return false;
	});
	jQuery(".cell_customfields :input").click(function () {
		row = jQuery(this).parents(".row_article").find("[name='quantity[]']");
		getPrice(row);
	});
});

// change quantity
// this function check all paremeters (min, max, inbox) and set right quantity for product
// parent = jquery object of input:text or qty buttons
function changeQuantity (parent) {
	form = jQuery(parent).parents("form");
	if (jQuery(parent).parents(".row_quantity").length > 0) {
		// for radio button
		group_id = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']").data("groupid");
		row_article = find_selected(group_id, form);
	} else {
		row_article = jQuery(parent).parents(".row_article");
	}
	p_data = jQuery(row_article).find(".catproduct_product_datas").data();
	p_data = typeof p_data !== 'undefined'? p_data: {};
	funcQ = "input";
	if (typeof jQuery(parent).attr("class") !== 'undefined') {
		if (jQuery(parent).attr("class").search("quantity-plus") != "-1") funcQ = "plus";
		if (jQuery(parent).attr("class").search("quantity-minus") != "-1") funcQ = "minus";
	}
	group_id = typeof p_data.group_id !== "undefined"? p_data.group_id : 0;
	// tole je potrebno pregledat - za radio layout
	if (jQuery(parent).parents(".row_quantity").length > 0) {
		qty_el = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']");
	} else {
		if (jQuery(parent).is(":radio")) {
			qty_el = jQuery(form).find("[name='quantity[]'][data-groupid='"+group_id+"']");
		} else if (jQuery(parent).is(":checkbox")) {
			qty_el = jQuery(parent);
			if (qty_el.is(":checked")) {
				qty_el.val(qty_el.data("group_def_qty"));
			} else {
				qty_el.val(0);
			}
		} else {
			qty_el = jQuery(row_article).find("input[name='quantity[]']");
		}
	}
	qty = qty_el.val();
	qty = parseFloat(qty);
	qty = isNaN(qty)?0:qty;
	
	minQ = typeof p_data.min_order_level !== 'undefined'? parseFloat(p_data.min_order_level) : 0;
	maxQ = typeof p_data.max_order_level !== 'undefined'? parseFloat(p_data.max_order_level) : 0;
	boxQ = typeof p_data.product_box !== 'undefined'? parseFloat(p_data.product_box) : 0;
	
	// tole nujno preveri - check stock
	if (typeof checkstock !== 'undefined' && checkstock == "1") {
		stock = typeof p_data.product_in_stock !== 'undefined'? parseFloat(p_data.product_in_stock) : 0;
		if (maxQ == 0) {
			maxQ = stock;
		} else {
			if (maxQ > stock) {
				maxQ = stock;
			}
		}
	}
	if (funcQ == "minus") {
		if (minQ && minQ > 0) {
			if (qty <= minQ) {
				qty = 0;
			}
			else if (maxQ && qty > maxQ) {
				qty = maxQ;
			}
			else {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty -= (qty%boxQ);
					}
					else {			
						qty -= boxQ;
					}
				}
				else
					qty--;
			}
		}
		else {
			if ( !isNaN( qty ) && qty > 0 ) {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty -= (qty%boxQ);
					}
					else {			
						qty -= boxQ;
					}
				}
				else
					qty--;
			}
			else if (qty < 0)
				qty = 0;
		}
	}
	if (funcQ == "plus") {
		if (maxQ && maxQ > 0) {
			if (qty >= maxQ) {
				qty = maxQ;
			}
			else if (qty == 0 && minQ && minQ > 0) {
				qty = minQ;
			}
			else if (qty < 0) {
				qty = 0;
			}
			else {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty += (boxQ-(qty%boxQ));
					}
					else {			
						qty += boxQ;
					}
				}
				else
					qty++;
			}
		}
		else {
			if ( !isNaN( qty ) && qty >= 0) {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty += (boxQ-(qty%boxQ));
					}
					else {			
						qty += boxQ;
					}
				}
				else
					qty++;
			}
			else 
				qty = 0;
		}
	}
	if (funcQ == "input") {
		if (maxQ && maxQ > 0 && qty >= 0) {
			if (qty >= maxQ) {
				qty = maxQ;
			}
			else {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty += (boxQ-(qty%boxQ));
					}
				}
			}
		}
		else {
			if ( !isNaN( qty ) && qty > 0) {
				if (boxQ && boxQ > 0) {
					if ((qty%boxQ) != 0) {
						qty += (boxQ-(qty%boxQ));
					}
				}
			}
		}
		if (minQ && minQ > 0 && qty < minQ && qty > 0) {
			qty = minQ;
		}
		if (qty <= 0) {
			qty = 0;
		}
	}
	qty_el.val(qty);
	if (updateprice == 1) {
		getPrice (parent, form, group_id, qty);
	}
	else {
		updateSumPrice_new(parent, form, group_id, qty);  
	}
	return false;
}


// set quantity to default 
// form is jquery object of catproducts forms
// tole je potrebno še ptredelat in preverit!!!!!
// naj bi bilo, razen groupid, tega se je potrebno še rešit -> za radio template
function emptyQuantity (form) {
	var form = typeof form !== 'undefined' ? form : jQuery("form[name='catproduct_form']");
	jQuery(form).each (function () {
		jQuery(this).find("input[name='quantity[]']").each(function() {	
			if (jQuery(this).attr("type") == "checkbox") {
				jQuery(this).attr("checked",false);
				jQuery(this).val(0);
			} else {
				p_data = jQuery(this).parents(".row_article").find(".catproduct_product_datas").data();
				p_data = typeof p_data !== 'undefined'? p_data: {};
				if (typeof p_data.def_group_qty !== "undefined") {
					jQuery(this).val(p_data.def_group_qty);
				} else {
					jQuery(this).val(0);
				}
			}
			updateSumPrice_new(jQuery(this));
		});
		var group_id = 0;
		
		jQuery("[name='quantity[]'][data-groupid]").each(function () {
			groupid = jQuery(this).data("groupid");
			jQuery("[name^='virtuemart_product_id[]'][data-groupid='"+groupid+"']:first").prop('checked', 'checked');
			jQuery(this).val(jQuery(this).data("group_def_qty"));
			updateSumPrice_new(jQuery(this));
		});
	});
}

// get price from virtuemart with ajax request after parameter changes
// parent = jquery object of input:text or qty buttons
function getPrice (parent, form, group_id, quantity) {
	if (typeof parent == 'undefined') return false;
	form = typeof form !== 'undefined' ? form : jQuery(parent).parents("form");
	if (jQuery(parent).parents(".row_quantity").length > 0) {
		// for radio button
		group_id = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']").data("groupid");
		var row_article = find_selected(group_id, form);
	} else {
		var row_article = jQuery(parent).parents(".row_article");
	}
	var p_data = jQuery(row_article).find(".catproduct_product_datas");

	//getPriceAll ();
	group_id = typeof group_id !== 'undefined' ? group_id : typeof p_data.group_id !== "undefined"? p_data.group_id : 0;

	if (typeof quantity !== 'undefined') {
		// tole za radio layour je potrebno pregledat
		if (jQuery(parent).parents(".row_quantity").length > 0) {
			qty_el = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']");
		} else {
			if (jQuery(parent).is(":radio")) {
				qty_el = jQuery(form).find("[name='quantity[]'][data-groupid='"+group_id+"']");
			} else {
				qty_el = jQuery(row_article).find("input[name='quantity[]']");
			}
		}
		quantity = qty_el.val();
		quantity = parseFloat(quantity);
	}
	quantity = isNaN(quantity)?0:quantity;
	
	if (typeof p_data.data("product_id") !== "undefined") product_id = p_data.data("product_id"); else return false;
	var url = window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&format=json' + window.vmLang;
	
	url += '&virtuemart_product_id[]='+product_id;
	if (quantity > 0) {	url += '&quantity[]='+quantity; }
	// tole bi blo fajn narest tako kot je za handle to cart, ni pa ful mus
	url += "&"+jQuery(row_article).find("[name^=customPlugin]").serialize(); 
	url += "&"+jQuery(row_article).find("[name^=customPrice]").serialize(); 
	url += "&"+jQuery(row_article).find("[name^=customProductData]").serialize();  
	url += "&"+jQuery(form).find(".cell_parent_customfields [name^=customPlugin]").serialize();
	url += "&"+jQuery(form).find(".cell_parent_customfields [name^=customPrice]").serialize(); 
	url += "&"+jQuery(form).find(".cell_parent_customfields [name^=customProductData]").serialize(); 
	
	jQuery.getJSON(url,
					function (datas, textStatus) {
						for (var key in datas) {
						var value = datas[key];
						if(value!=0 && value != '' && value != null) {
							// decimalsymbol v p_data ali m_data????
							if (decimalsymbol == ".") value = (value.replace(/[^0-9-.]/g, ''));
							if (decimalsymbol == ",") value = (value.replace(/[^0-9-,]/g, '').replace(",", "."));
							p_data.data(key.toLowerCase(),parseFloat(value.replace(/[^\d.-]/g, '')));
							jQuery(row_article).find("."+key+"_text").find("span.Price"+key).html(datas[key]);
						}
						}
						updateSumPrice_new(parent, form, group_id, quantity); 
					});
	//return false;
}


// tole je potrebno še pregledat!!! ni še porihtrana, da preveri cene samo za en form, pottrebno je dodati al parent, al form itd... tako kot zgornja funkcija
function getPriceAll () {
	request_price.abort();
	var products = {};
	jQuery("form[name='catproduct_form'] .product_id").each( function() {
		id = jQuery(this).attr('id').replace("product_id_","");
		products[id] = {};
		
		group_id = jQuery("form[name='catproduct_form'] #group_id_"+id).val();
		if (jQuery("form[name='catproduct_form'] input[id='G_quantity_"+group_id+"']").val()) {
			qty_el = jQuery("form[name='catproduct_form'] input[id='G_quantity_"+group_id+"']");
		} else {
			qty_el = jQuery("form[name='catproduct_form'] input[id='quantity_"+id+"']");
		}
		quantity = qty_el.val();
		quantity = parseFloat(quantity);
		
		product_id = jQuery("#product_id_"+id).val();
		
		products[id]["virtuemart_product_id"] = product_id;
		products[id]['quantity'] = quantity;
		products[id]['customPlugin'] = jQuery("#row_article_"+id+" [name^=customPlugin]").serialize(); 
		products[id]['customPrice'] = jQuery("#row_article_"+id+" [name^=customPrice]").serialize(); 
		products[id]['customProductData'] = jQuery("#row_article_"+noQ+" [name^=customProductData]").serialize();  
		products[id]['ParentcustomPlugin'] = jQuery("form[name='catproduct_form'] .cell_parent_customfields [name^=customPlugin]").serialize();
		products[id]['ParentcustomPrice'] = jQuery("form[name='catproduct_form'] .cell_parent_customfields [name^=customPrice]").serialize(); 
		products[id]['ParentcustomProductData'] = jQuery("form[name='catproduct_form'] .cell_parent_customfields [name^=customProductData]").serialize(); 
	});
	
	var base_url = vmSiteurl + 'index.php?option=com_virtuemart&controller=blabla';
	
	var url=base_url;

	request_price = jQuery.ajax({
		type: 'POST',
		url: url,
		traditional: true,
		data: { 'products' : JSON.stringify(products) },
		success: function(datas) {
				for (var prod_id in datas) {
					if (!isNaN(prod_id)) {
					for (var key in datas[prod_id]) {
						jQuery("form[name='catproduct_form'] .product_id[value="+prod_id+"]").each ( function () {
							id = jQuery(this).attr('id').replace("product_id_","");
							value = datas[prod_id][key].toString()
							if(value!=0 && value != '' && value != null) {
								if (decimalsymbol == ".") value = parseFloat(value.replace(/[^0-9-.]/g, ''));
								if (decimalsymbol == ",") value = parseFloat(value.replace(/[^0-9-,]/g, '').replace(",", "."));
								jQuery("#"+key+"_"+id).val(value);
								jQuery("#"+key+"_text_"+id).find("span").html(datas[prod_id][key]);
								jQuery("#"+key+"_text_"+id+" div").css("display","block");
							} else {
								jQuery("#"+key+"_"+id).val(value);
								jQuery("#"+key+"_text_"+id).find("span").html(datas[prod_id][key]);
								jQuery("#"+key+"_text_"+id+" div").css("display","block");
							}
							updateSumPrice_new(id); 
						});
					}
					
					}
				}
		},
		error: function(datas) {
			//alert(datas);
		},
		dataType: 'json'
	});
}

function setRadioPricesToZero (parent, form, group_id) {
	jQuery(form).find("input:radio[data-groupid='"+group_id+"']").parents('.row_article').each( function() {
		updateSumPrice_new(jQuery(this).find("input:last"), form, group_id, 0, true);
	});
}

function updateSumPrice_new(parent, form, group_id, quantity, onlySum)  {
	parent = typeof parent !== 'undefined' ? parent : jQuery("form[name='catproduct_form']:first").find("input[name='quantity[]']:first");
	form = typeof form !== 'undefined' ? form : jQuery(parent).parents("form");
	onlySum = typeof onlySum !== 'undefined' ? onlySum : false;
	if (jQuery(parent).parents(".row_quantity").length > 0) {
		// for radio button
		group_id = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']").data("groupid");
		var row_article = find_selected(group_id, form);
	} else {
		var row_article = jQuery(parent).parents(".row_article");
	}
	// get catproduct product data
	var p_data = jQuery(row_article).find(".catproduct_product_datas").data();
	p_data = typeof p_data !== 'undefined'? p_data: {};
	
	group_id = typeof group_id !== 'undefined' ? group_id : typeof p_data.group_id !== 'undefined'? p_data.group_id: 0;
	if (jQuery(parent).parents(".row_quantity").length > 0 || jQuery(parent).is(":radio")) {
		setRadioPricesToZero (parent, form, group_id);
	}
	
	// get catproduct main data
	var m_data = jQuery(form).find(".catproduct_main_data").data();
	// if no m_data, try with some default settings...
	m_data = typeof m_data !== 'undefined'? m_data: {"currency_symbol" : "€", "currency_decimal_place" : "2", "currency_decimal_symbol" : ",", "currency_thousands": "", "add_parent_from_original": "0"};
	
	if (typeof quantity == 'undefined') {
		// tole je potrebno še pregledat za radio layout
		if (jQuery(parent).parents(".row_quantity").length > 0) {
			qty_el = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']");
		} else {
			qty_el = jQuery(row_article).find("input[name='quantity[]']");
			if (qty_el.is(":checkbox")) {
				if (!qty_el.is(":checked")) {
					qty_el.val(0);
				}
			}
		}
		quantity = qty_el.val();
		quantity = parseFloat(quantity);
	}
	quantity = isNaN(quantity)?0:quantity;
	
	// sum_field
	for (var key in p_data){
		if (p_data.hasOwnProperty(key)) {
			x = "sum_";
			if (key.substring(0, x.length) === x) { 
				field_name = key.replace(x,"").toLowerCase();
				price = p_data[field_name];
				price=parseFloat(price)*parseFloat(quantity);
				price=setDecimals(price,form);
				p_data[key] = price;
			}
		}
	}
	
	// show_sum
	// KG -> for each product
	puom = typeof p_data.product_weight_uom !== 'undefined'? p_data.product_weight_uom: '';
	// KG -> for main product
	muom = typeof m_data.product_weight_uom_parent !== 'undefined'? m_data.product_weight_uom_parent: '';
	// €
	unit = typeof m_data.currency_symbol !== 'undefined'? m_data.currency_symbol: '€';

	// symbol position - od kje to pride, to je potrebno dati v m_data!
	if(symbol_position == 0) {
		unit1 = unit;
		unit = '';
	}
	if(symbol_position == 1) {
		unit1 = '';
		unit = unit;
	}
	jQuery(row_article).find("[class^='cell_sum_']").each(function () {
		field_name = "sum_"+ jQuery(this).attr("class").replace("cell_sum_","").replace("options-listing","").replace(" ","");
		price = p_data[field_name.toLowerCase()];
		price = format_p(price, form);
		if (field_name !== 'sum_product_weight') {
			jQuery(this).html('<span>'+unit1 + symbol_space + price  + symbol_space +  unit+'</span>');
		} else {
			jQuery(this).html('<span>' + price  + ' ' + puom + '</span>');
		}
	});
	
	// check if you only need only to calculate sum
	if (!onlySum) {
		// update parent prices if needed
		// add parent from original
		var add_p_original =  parseInt(m_data.add_parent_from_original);
		if (add_p_original == 1) {
			m_data = FindMainProductPrice(form);
		}
		
		// total_field
		jQuery(form).find("[class^='total_']").each(function () {
			field_name = jQuery(this).attr("class").replace("total_","");
			total = 0;
			jQuery(form).find(".catproduct_product_datas").each(function() {
				value = typeof jQuery(this).data("sum_"+field_name.toLowerCase()) !== "undefined"? jQuery(this).data("sum_"+field_name.toLowerCase()): 0;
				if (field_name == "product_weight") {
					value = calculate_to_uom(value, puom, false);
				}
				total += parseFloat(value);	
			});
			
			
			// recimo, da tole je. Potrebno je še dodati product_weight_parent v template + potrebno se je znebiti IDjev
			if (add_p_original == 1) {
				mainproductid = field_name.toLowerCase() + '_parent';
				if (typeof m_data[mainproductid] == "undefined") {
					if (field_name == "product_weight")	jQuery(form).find(".catproduct_main_data").data(mainproductid, "0");
					if (field_name == "discountAmount") jQuery(form).find(".catproduct_main_data").data(mainproductid, "0");
					m_data = FindMainProductPrice(form);
				}
				// try productdetails
				productdetails = jQuery(form).parents(".productdetails").find(".addtocart-area form");
				if (productdetails.length < 1) {
					// try browse page
					productdetails = jQuery(form).parents(".product").find(".addtocart-area form");
				}
				main_qty = jQuery(productdetails).find("input[name='quantity[]']").val();
				value = typeof m_data[mainproductid] !== "undefined"? parseFloat(m_data[mainproductid]): 0;
				value = !isNaN(value)?value:0;
				if (field_name == 'product_weight') {
					value = calculate_to_uom(value, muom, false);
				}
				total += value * main_qty;
			}
			if (field_name == 'product_weight') {
				total = calculate_to_uom(total, muom, true);
			}
			total = setDecimals(total,form);
			total = format_p(total, form);
			if (field_name !== 'product_weight') {
				jQuery(this).html('<span>'+unit1 + symbol_space + total + symbol_space + unit+'</span>'); 
			} else {
				jQuery(this).html('<span>' + total  + ' ' + muom + '</span>');
			}
		});
	}
}  

//calculate weight to uom
// if type = true, calculate to uom
// if type = false, calculate to gramme
function calculate_to_uom(value, uom, type) {
	type = typeof type !== 'undefined'? type : true;
	if (value == 0 || uom == '') return value;
	x = 1;
	switch(uom) {
    case 'KG':
        x = 1000;
        break;
    case 'G':
        x = 1;
        break;
	case 'MG':
        x = 0.001;
        break;
	case 'LB':
        x = 453.6;
        break;
	case 'OZ':
        x = 28.35;
        break;
    default:
        x = 1;
	}
	if (type) {
		return (value / x);
	} else {
		return (value * x);
	}
}

// set number of decimals based on VM settings
function setDecimals(number, form){
	decimals = jQuery(form).find(".catproduct_main_data").data().currency_decimal_place;
	if (typeof decimals !== 'undefined' && decimals !== '') decimals = parseInt(decimals); else decimals = 2;
	number=number.toFixed(decimals);
	return number;
}

// format price based on VM settings
function format_p(price, form) {
	m_data = jQuery(form).find(".catproduct_main_data").data();
	t_sep = m_data.currency_thousands; 
	t_sep = typeof t_sep !== "undefined" ? t_sep : "";
	d_sym = m_data.currency_decimal_symbol; 
	d_sym = typeof d_sym !== "undefined" ? d_sym : "";
	
	price = price.toString();
	a = price.split(".");
	x = a[0]; 
	y = a[1];
	z = "";

	if (typeof(x) != "undefined") {
		for (i=x.length-1;i>=0;i--)
			z += x.charAt(i);
		z = z.replace(",", "");
		z = z.replace(".", "");
		z = z.replace(/(\d{3})/g, "$1" + t_sep);
		if (z.slice(-t_sep.length) == t_sep)
			z = z.slice(0, -t_sep.length);
		x = "";
		for (i=z.length-1;i>=0;i--)
			x += z.charAt(i);
		if (typeof(y) != "undefined" && y.length > 0)
			x += d_sym + y;
	}
	return x;
}
 //get the quantity of the group
function getQuantity(row_article){
	form = jQuery(row_article).parents("form");
	p_data = jQuery(row_article).find(".catproduct_product_datas").data();
	group_id = p_data.group_id; 
	// tole je potrebno še pregledat za radio layout
	if (jQuery(row_article).find("input[name='quantity[]']").val()) {
		qty_el = jQuery(row_article).find("input[name='quantity[]']");
		if (qty_el.is(":checkbox")) {
			if (!qty_el.is(":checked")) {
				qty_el.val(0);
			}
		}
	} else if (jQuery(form).find("[name='quantity[]'][data-groupid='"+group_id+"']").val()) {
		qty_el = jQuery(form).find("[name='quantity[]'][data-groupid='"+group_id+"']");
	}
	
	quantity = qty_el.val();
	quantity = parseFloat(quantity);
	
    if(isNaN(quantity)){
      quantity=0;
	  qty_el.val("0");
    }
    return quantity;
}
/* new function for VM3
   it's a little different how VM2 and VM3 handles adding to cart
   VM3 supports adding all products together - also can handle customfields
   VM2 can handle multiple products at once, but not with customfields
   So this function is only good for VM3 as it adds all products with customfields in one step
   not one by one.
*/
function handleToCart_VM3 (parent, form) {
	parent = typeof parent !== 'undefined' ? parent : jQuery("form[name='catproduct_form']:first").find("input:submit");
	form = typeof form !== 'undefined' ? form : jQuery(parent).parents("form");
	m_data = jQuery(form).find(".catproduct_main_data").data();
	
	if(typeof(usefancy) == 'boolean' && usefancy){
			jQuery.fancybox.showActivity();
	}
	else {
		jQuery('.catproduct-loading').hide().ajaxStart( function() {
			jQuery(this).show();  // show Loading Div
		} ).ajaxStop ( function(){
			jQuery(this).hide(); // hide loading div
		});
	}
	
	// A SE TO SPLOH NUCA???
	product_ids = new Array();
	quantities= new Array();
	
	prod_no=0;
	is_qty=0;
	data = {};
	
	// add parent from original
	add_p_original =  parseInt(m_data.add_parent_from_original);
	if (add_p_original == 1) {
		// try productdetails
		productdetails = jQuery(form).parents(".productdetails").find(".addtocart-area form");
		if (productdetails.length < 1) {
			// try browse page
			productdetails = jQuery(form).parents(".product").find(".addtocart-area form");
		}
		if (productdetails.length > 0) {
			if (jQuery(productdetails).find("input[name='addtocart']").attr('class') == 'addtocart-button-disabled') {
				// kaj v tem primeru, če je blokiran parent??? verjetno nič
			} else {
				if (jQuery(productdetails).find("input[name='quantity[]']").val() > 0) {
					temp_data = jQuery(productdetails).serializeArray();
					quantities[prod_no]=jQuery(productdetails).find("input[name='quantity[]']").val();
					product_ids[prod_no]=jQuery(productdetails).find("input[name='virtuemart_product_id[]']").val();
					for (var i = 0, len = temp_data.length; i < len; i++) {
						data[temp_data[i]['name']] = temp_data[i]['value'];
					}
					is_qty=1;
					prod_no += 1;
				}
			}
		}
	}
	
	// ZA PREMISLITI, ČE SE DA DODATI
	// .cell_parent_customfields 
	// A TO ŠE DELA V VM3 AL NE
	// PREVERI
	
	//get the product_ids of the selected products
	jQuery(form).find(".catproduct_product_datas").each(function() { 
		row_article = jQuery(this).parents(".row_article");
		prod_id = jQuery(this).data("product_id");
		if(prod_id!=0){
			qty = 0;
			if (jQuery(row_article).find("[name^='virtuemart_product_id[]']").is(":radio")) {
				if ( jQuery(row_article).find("[name^='virtuemart_product_id[]']").is(":checked")) {
					qty = getQuantity(row_article);
				} 
			} else {
				qty = getQuantity(row_article);
			}
			qty = isNaN(qty)?0:qty;
			if ( qty > 0) {
				qty=parseInt(qty);
				is_qty = 1;
				quantities[prod_no]=qty;
				product_ids[prod_no]=prod_id;
				
				temp_data = jQuery(row_article).find(".cell_customfields").find(":input").serializeArray();
				for (var i = 0, len = temp_data.length; i < len; i++) {
					data[temp_data[i]['name']] = temp_data[i]['value'];
				}
				prod_no += 1;
			}
		}
	});

	data['virtuemart_product_id[]'] = product_ids;
	data['quantity[]'] = quantities;

	if(product_ids.length > 0) {//if products
		if (is_qty != 0) {	
			addToCart_VM3(data, form);
		} // if is quantity
		else {
			faceboxShow_VM3(noquantityerror);
		}
	} //if(product_ids)
	else {	
		faceboxShow_VM3(noproducterror);
	}
}

function addToCart_VM3 (data, form) {
	if (typeof data == 'undefined') { faceboxShow_VM3(noproducterror); return false; }
	form = typeof form !== 'undefined' ? form : jQuery("form[name='catproduct_form']:first");
	var url = vmSiteurl + 'index.php?option=com_virtuemart&view=cart&task=addJS&format=json&nosef=1';
	var request = jQuery.ajax({
		type: 'POST',
		url: url,
		traditional: true,
		data: data,
		success: function(datas) {
			switch(parseInt(datas.stat))
			{
				case 1:
					if (Virtuemart.addtocart_popup ==1) {
						faceboxShow_VM3 (datas.msg);
						emptyQuantity(form);
						if (jQuery(".vmCartModule")[0]) {
							Virtuemart.productUpdate(jQuery(".vmCartModule"));
						}
					} else {
						window.location.href = vmSiteurl+'index.php?option=com_virtuemart&view=cart';
					}
					break;
				case 2:
					faceboxShow_VM3 (datas.msg);
					break;
				default:
					if (Virtuemart.addtocart_popup ==1) {
						faceboxShow_VM3 (datas.msg);
						emptyQuantity(form);
						if (jQuery(".vmCartModule")[0]) {
							Virtuemart.productUpdate(jQuery(".vmCartModule"));
						}
					} else {
						window.location.href = vmSiteurl+'index.php?option=com_virtuemart&view=cart';
					}
			}
		},
		dataType: 'json'
	});
}

function faceboxShow_VM3 (msg) {
	if(typeof(usefancy) == 'boolean' && usefancy){		
		jQuery.fancybox({
			"titlePosition" : 	"inside",
			"transitionIn"	:	"elastic",
			"transitionOut"	:	"elastic",
			"type"			:	"html",
			"autoCenter"    :   true,
			"closeBtn"      :   false,
			"closeClick"    :   false,
			"content"       :   msg
		});
		jQuery.fancybox.hideActivity();
	} else if (typeof(jQuery.facebox) == 'function') {
		jQuery.facebox.settings.closeImage = closeImage;
		jQuery.facebox.settings.loadingImage = loadingImage;
		jQuery.facebox({ text: msg }, 'my-groovy-style');
	} else {
		alert(msg);
	}	
}

function handleToCartOne_VM3 (parent) {
	parent = typeof parent !== 'undefined' ? parent : false;
	if (!parent) return false;
	
	if(typeof(usefancy) == 'boolean' && usefancy){
			jQuery.fancybox.showActivity();
	}
	else {
		jQuery('.catproduct-loading').hide().ajaxStart( function() {
			jQuery(this).show();  // show Loading Div
		} ).ajaxStop ( function(){
			jQuery(this).hide(); // hide loading div
		});
	}
	
	if (jQuery(parent).parents(".row_quantity").length > 0) {
		form = jQuery(parent).parents("form");
		group_id = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']").data("groupid");
		row_article = find_selected(group_id, form); //jQuery(parent).parents(".row_article");
		p_data = jQuery(row_article).find(".catproduct_product_datas").data();
		product_id = p_data.product_id;
		quantity = jQuery(parent).parents(".row_quantity").find("[name='quantity[]']").val();
	} else {
		row_article = jQuery(parent).parents(".row_article");
		p_data = jQuery(row_article).find(".catproduct_product_datas").data();
		
		product_id = p_data.product_id;
		quantity = jQuery(row_article).find("[name='quantity[]']").val();
	}
	quantity = isNaN(quantity)?0:quantity;
	
	if (typeof product_id !== 'undefined') {
		if (typeof quantity !== 'undefined' && quantity > 0) {
			data = {};
			temp_data = jQuery(row_article).find(".cell_customfields").find(":input").serializeArray();
			for (var i = 0, len = temp_data.length; i < len; i++) {
				data[temp_data[i]['name']] = temp_data[i]['value'];
			}
			data['virtuemart_product_id[]'] = product_id;
			data['quantity[]'] = quantity;
			addToCart_VM3 (data, row_article);
		}	else {
			faceboxShow_VM3(noquantityerror);
		}
	} else {
		faceboxShow_VM3(noproducterror);
	}
}

// find main product price
function FindMainProductPrice (form) {
	form = typeof form !== 'undefined' ? form : jQuery("form[name='catproduct_form']");
	var m_data = jQuery(form).find(".catproduct_main_data");
	add_p_original = parseInt(m_data.data("add_parent_from_original"));
	
	if (typeof m_data == 'undefined') {
		jQuery(form).append('<input type="hidden" class=".catproduct_main_data" >');
		m_data = jQuery(form).find(".catproduct_main_data");
	}	
	if (add_p_original == 1) { 
		var url = window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&format=json' + window.vmLang;
		// try productdetails
		productdetails = jQuery(form).parents(".productdetails").find(".addtocart-area form");
		// try browse page
		if (productdetails.length < 1) { productdetails = jQuery(form).parents(".product").find(".addtocart-area form"); }
		url += "&"+jQuery(productdetails).serialize();
		url = url.replace("&view=cart", "");
		
		jQuery.ajax({
		  url: url,
		  dataType: 'json',
		  success: function (datas, textStatus) {
				for (var key in datas) {
					var value = datas[key];
					if(value!=0 && value != '' && value != null) {
						if (decimalsymbol == ".") value = parseFloat(value.replace(/[^0-9-.]/g, ''));
						if (decimalsymbol == ",") value = parseFloat(value.replace(/[^0-9-,]/g, '').replace(",", "."));
						m_data.data(key.toLowerCase() + "_parent",value);
					}
				}
				return m_data.data();
			}	
		});
	} else {
		return m_data.data();
	}
	return m_data.data();
}


// tole je potrebno še dodat, kot parameter show total quantity
jQuery(document).ready(function() {
	jQuery("form[name='catproduct_form'] .quantity-input").keyup(function() {
		calculateTotalQty();
	});
});
	
function calculateTotalQty () {
	qty = 0;
	jQuery("form[name='catproduct_form'] .quantity-input").each(function () {
		qty += parseInt(jQuery(this).val());
	});
	jQuery("form[name='catproduct_form'] #quantity_total").html(qty);
}
  

// find selected radio in group
// tole je za radio layout, preveri, kaj je s tem
function find_selected(group_id, form) {
	return jQuery(form).find("input:radio[data-groupid='"+group_id+"']:checked").parents('.row_article');
	/*	id = jQuery("form[name='catproduct_form'] input[name^='virtuemart_product_id\[\]\["+group_id+"']:checked").attr("id");
		id = id.replace("virtuemart_product_id","");
		return id;*/
}

