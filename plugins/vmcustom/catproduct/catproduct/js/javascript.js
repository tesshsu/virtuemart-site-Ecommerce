/*
 *
 * @Author Sandi Mlinar
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012 SM - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */
var request_price = jQuery.ajax();

var floatSign=',';
 
// set quantity to default 
function emptyQuantity () {
	jQuery("#catproduct_form input[id^='quantity_']").each(function() {	
		noQ = jQuery(this).attr('id').replace('quantity_','');
		if (jQuery(this).attr("type") == "checkbox") {
			jQuery(this).attr("checked",false);
			jQuery(this).val(0);
		} else {
			if (jQuery('#def_group_qty_'+noQ).val()) {
				jQuery(this).val(jQuery('#def_group_qty_'+noQ).val());
			} else {
				jQuery(this).val(0);
			}
		}
		updateSumPrice(noQ);
	});
	jQuery("#catproduct_form input[id^='G_quantity_']").each(function() {
		groupid = jQuery(this).attr('id').replace('G_quantity_','');
		if (jQuery('#radio_group_qty_'+groupid).val()) {
			jQuery(this).val(jQuery('#radio_group_qty_'+groupid).val());
		} else {
			jQuery(this).val(1);
		}
	});
	var group_id = 0;
	jQuery("#catproduct_form input[name^='virtuemart_product_id[]']").each(function() { 
		new_group_id = jQuery(this).attr('name').replace('virtuemart_product_id[][','').replace(']','');
		if (group_id != new_group_id) {
			jQuery("#catproduct_form input[name='virtuemart_product_id[]["+new_group_id+"]']").first().prop('checked', 'checked');
			group_id = new_group_id;
		}
	});
	jQuery("#catproduct_form input[id^='quantity_']").each(function() {	
		noQ = jQuery(this).attr('id').replace('quantity_','');
		updateSumPrice(noQ);
	});
	updateSumPrice(0);
}

// find selected radio in group
function find_selected(group_id) {
		id = jQuery("#catproduct_form input[name^='virtuemart_product_id\[\]\["+group_id+"']:checked").attr("id");
		id = id.replace("virtuemart_product_id","");
		return id;
}

// change quantity
// this function check all paremeters (min, max, inbox) and set right quantity for product
function changeQuantity (noQ, funcQ, minQ, maxQ, boxQ) {
	group_id = jQuery("#catproduct_form #group_id_"+noQ).val();
	if (jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']").val()) {
		qty_el = jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']");
	} else {
		qty_el = jQuery("#catproduct_form input[id='quantity_"+noQ+"']");
	}
	qty = qty_el.val();
	qty = parseFloat(qty);
	
	if (jQuery("#min_order_level_"+noQ).val()) minQ = jQuery("#min_order_level_"+noQ).val();
	if (jQuery("#max_order_level_"+noQ).val()) maxQ = jQuery("#max_order_level_"+noQ).val();
	if (jQuery("#product_box_"+noQ).val()) boxQ = jQuery("#product_box_"+noQ).val();

	minQ = parseFloat(minQ);
	maxQ = parseFloat(maxQ);
	boxQ = parseFloat(boxQ);
	
	if (typeof checkstock !== 'undefined' && checkstock == "1") {
		if (jQuery("#product_in_stock_"+noQ).val()) stock = jQuery("#product_in_stock_"+noQ).val();
		stock = parseFloat(stock);
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
					else {			
						qty += boxQ;
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
		getPrice (noQ);
	}
	else {
		updateSumPrice(noQ); 
	}
	return false;
}

// get price from virtuemart with ajax request after parameter changes
function getPrice (noQ) {
//getPriceAll ();
	group_id = jQuery("#catproduct_form #group_id_"+noQ).val();
	if (jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']").val()) {
		qty_el = jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']");
	} else {
		qty_el = jQuery("#catproduct_form input[id='quantity_"+noQ+"']");
	}
	quantity = qty_el.val();
	quantity = parseFloat(quantity);
	
	product_id = jQuery("#product_id_"+noQ).val();
	var url = window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&format=json' + window.vmLang;
	
	url += '&virtuemart_product_id[]='+product_id;
	if (quantity > 0) {	url += '&quantity[]='+quantity; }
	url += "&"+jQuery("#row_article_"+noQ+" [name^=customPlugin]").serialize(); 
	url += "&"+jQuery("#row_article_"+noQ+" [name^=customPrice]").serialize(); 
	url += "&"+jQuery("#row_article_"+noQ+" [name^=customProductData]").serialize();  
	url += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customPlugin]").serialize();
	url += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customPrice]").serialize(); 
	url += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customProductData]").serialize(); 
	
	//var url=encodeURI(url);

	jQuery.getJSON(url,
					function (datas, textStatus) {
						for (var key in datas) {
						var value = datas[key];
						if(value!=0 && value != '' && value != null) {
							if (decimalsymbol == ".") value = (value.replace(/[^0-9-.]/g, ''));
							if (decimalsymbol == ",") value = (value.replace(/[^0-9-,]/g, '').replace(",", "."));
							jQuery("#"+key+"_"+noQ).val(parseFloat(value.replace(/[^\d.-]/g, '')));
							jQuery("#"+key+"_text_"+noQ).find("span.Price"+key).html(datas[key]);
						}
						}
						updateSumPrice(noQ); 
					});
	return false;
}

function getPriceAll () {
	request_price.abort();
	var products = {};
	jQuery("#catproduct_form .product_id").each( function() {
		id = jQuery(this).attr('id').replace("product_id_","");
		products[id] = {};
		
		group_id = jQuery("#catproduct_form #group_id_"+id).val();
		if (jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']").val()) {
			qty_el = jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']");
		} else {
			qty_el = jQuery("#catproduct_form input[id='quantity_"+id+"']");
		}
		quantity = qty_el.val();
		quantity = parseFloat(quantity);
		
		product_id = jQuery("#product_id_"+id).val();
		
		products[id]["virtuemart_product_id"] = product_id;
		products[id]['quantity'] = quantity;
		products[id]['customPlugin'] = jQuery("#row_article_"+id+" [name^=customPlugin]").serialize(); 
		products[id]['customPrice'] = jQuery("#row_article_"+id+" [name^=customPrice]").serialize(); 
		products[id]['customProductData'] = jQuery("#row_article_"+noQ+" [name^=customProductData]").serialize();  
		products[id]['ParentcustomPlugin'] = jQuery("#catproduct_form .cell_parent_customfields [name^=customPlugin]").serialize();
		products[id]['ParentcustomPrice'] = jQuery("#catproduct_form .cell_parent_customfields [name^=customPrice]").serialize(); 
		products[id]['ParentcustomProductData'] = jQuery("#catproduct_form .cell_parent_customfields [name^=customProductData]").serialize(); 
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
						jQuery("#catproduct_form .product_id[value="+prod_id+"]").each ( function () {
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
							updateSumPrice(id); 
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

 //get the quantity of the group
function getQuantity(noQ){
	group_id = jQuery("#catproduct_form #group_id_"+noQ).val();
	if (jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']").val()) {
		qty_el = jQuery("#catproduct_form input[id='G_quantity_"+group_id+"']");
	} else {
		qty_el = jQuery("#catproduct_form input[id='quantity_"+noQ+"']");
	}
	quantity = qty_el.val();
	quantity = parseFloat(quantity);

    if(isNaN(quantity)){
      quantity=0;
	  qty_el.val("0");
    }
    return quantity;
}

// calculate sum field
function sum_field (art_id,quantity,getvalue,setvalue){
	var fieldid= "#"+getvalue+art_id;
	var price = jQuery("#"+getvalue+art_id).val();
	
	//if (price<0) price=0;
	//else {
		price=price*quantity;
		price=setDecimals(price,2);
	//}
	jQuery("#"+setvalue+art_id).val(price); 
}

// show sum field
function show_sum (art_id, unit, getvalue, setvalue) {
	var price =  jQuery("#"+getvalue+art_id).val();
	if(symbol_position == 0) {
		unit1 = unit;
		unit = '';
	}
	if(symbol_position == 1) {
		unit1 = '';
		unit = unit;
	}
	price = format_p(price);
	jQuery("#"+setvalue+art_id).html('<span>'+unit1 + symbol_space + price  + symbol_space +  unit+'</span>');
}

// calculate and show total field
function total_field (getvalue, setvalue, unit){
	var total = 0;
	
	jQuery('.'+getvalue).each(function() {
		value=jQuery(this).val();
		if(value && floatSign==',') value=setFloatingPoint(value);
		if(value && !isNaN(value)){
			total += parseFloat(value);	
		}
	});
	

	if (addparentoriginal == 1) {
		//FindMainProductPrice();
		mainproductid = '#' + getvalue.replace('sum_','') + '_parent';
		if (typeof jQuery(mainproductid).val() == 'undefined') FindMainProductPrice();
		main_qty = jQuery(".spacer-buy-area").find("input[name='quantity[]']").val();
		total += (parseFloat(jQuery(mainproductid).val()) * main_qty);
	}
	//if(total){

		total = setDecimals(total,2);
	//}

	if(symbol_position == 0) {
		unit1 = unit;
		unit = '';
	}
	if(symbol_position == 1) {
		unit1 = '';
		unit = unit;
	}
	total = format_p(total);
	jQuery("#"+setvalue).html('<span>'+unit1 + symbol_space + total + symbol_space + unit+'</span>'); 
}

  
    function setFloatingPoint(price){
     //replace comma with dot-this way it can converted to number
     if(floatSign==',')price=(price.toString().replace(/,/g,'.'));
	 return price;
   }
     
  
  function setDecimals(number, decimals){
   decimals = typeof decimals !== 'undefined' ? decimals : 2;
   decimalplaces = typeof decimalplaces !== 'undefined' ? decimalplaces: decimals;
   number_dec=number.toFixed(decimalplaces);
   return number_dec;
  }
  
  function format_p(price) {
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
      z = z.replace(/(\d{3})/g, "$1" + thousandssep);
    if (z.slice(-thousandssep.length) == thousandssep)
      z = z.slice(0, -thousandssep.length);
    x = "";
    for (i=z.length-1;i>=0;i--)
      x += z.charAt(i);
    if (typeof(y) != "undefined" && y.length > 0)
      x += decimalsymbol + y;
  }
  return x;
}


var prod_length;
var prod_names;
var gr_length;
var product_ids;
var quantities;
var active_gr;
var message = '';
var message_final = '';
var is_qty = 0;
var qty_ok = 0;

// handletocart all together (this is used if there are no other customfield)
function handleToCart() {
	if(typeof(usefancy) == 'boolean' && usefancy){
			jQuery.fancybox.showActivity();

	}
	else {
		jQuery('#catproduct-loading').hide().ajaxStart( function() {
			jQuery(this).show();  // show Loading Div
		} ).ajaxStop ( function(){
			jQuery(this).hide(); // hide loading div
		});
	}

  product_ids = new Array();
  quantities=new Array();
  prod_names = new Array();
  active_gr=new Array();
  message_final = '';

	// add main/parent product to cart if set
	if (addparentoriginal == 1) { 
		if (jQuery(".addtocart-area").find("input[name='addtocart']").attr('class') == 'addtocart-button-disabled') {
			faceboxError(jQuery(".addtocart-area").find("input[name='addtocart']").val());
			return;
		}
		else {
			addParentToCart();
		}
	}

	i=0;
	is_qty=0;
	
   //get the product_ids of the selected products
	jQuery(".product_id").each(function() { 
		prod_id = jQuery(this).val();
		if(prod_id!=0){
			noQ = jQuery(this).attr('id').replace('product_id_','');
			if (jQuery("#catproduct_form #virtuemart_product_id"+noQ).attr("type") == "radio") {
				qty = 0;
				if ( jQuery("#catproduct_form #row_article_"+noQ+" input[name^='virtuemart_product_id[]']").is(":checked")) {
					qty = getQuantity(noQ);
				}
			} else {
				qty = getQuantity(noQ);
			}
			if ( qty > 0) {
				qty=parseInt(qty);
				is_qty = 1;
				quantities[i]=qty;
				
				prname = jQuery("#product_name_"+noQ).val();
				if(!prname)prname='';
					prod_names[i]=prname;
				
				active_gr.push(i);
				product_ids[i]=prod_id;
				i += 1;
			}
		}
	});
	
	gr_length=active_gr.length;
	prod_length=product_ids.length;

	if(prod_length>0) {//if products
		if (is_qty != 0) {	
			addToCart(product_ids,quantities,prod_names);
		} // if is quantity
		else {
			if (qty_ok == 0) faceboxError(noquantityerror); else faceboxShow();
		}
	} //if(product_ids)
	else {	
		if (qty_ok == 0) faceboxError(noproducterror); else faceboxShow();
	}
}
  
function addToCart(product_id,quantity,product_name) {
	var base_url = vmSiteurl + 'index.php?option=com_virtuemart&view=cart&task=addJS&format=json&nosef=1';
	var sub_url='';
	
	sub_url += "&"+jQuery("#catproduct_form [name^=customPlugin]").serialize(); 
	sub_url += "&"+jQuery("#catproduct_form [name^=customPrice]").serialize(); 
	sub_url += "&"+jQuery("#catproduct_form [name^=customProductData]").serialize(); 
	
	var url=base_url+sub_url;

	var request = jQuery.ajax({
		type: 'POST',
		url: url,
		traditional: true,
		data: { 'virtuemart_product_id[]' : product_id, 'quantity[]' : quantity },
		success: function(datas) {
			switch(datas.stat)
			{
				case "1":
					prepareMessage(datas,product_name,quantity); 
					faceboxShow();
					break;
				case "2":
					prepareMessageError(datas,product_name,quantity); 
					faceboxShow();
					break;
				case 1:
					prepareMessage(datas,product_name,quantity); 
					faceboxShow();
					break;
				case 2:
					prepareMessageError(datas,product_name,quantity); 
					faceboxShow();
					break;
				default:
					message_final = "<H4>"+vmCartError+"</H4>"+datas.msg;
					faceboxShow();
			}
		},
		dataType: 'json'
	});
}  
  
function faceboxShow() {
	if (Virtuemart.addtocart_popup ==1) {
		message_final = removeNoQ(message_final);
		if(typeof(usefancy) == 'boolean' && usefancy){		
			jQuery.fancybox({
				"titlePosition" : 	"inside",
				"transitionIn"	:	"elastic",
				"transitionOut"	:	"elastic",
				"type"			:	"html",
				"autoCenter"    :   true,
				"closeBtn"      :   false,
				"closeClick"    :   false,
				"content"       :   message_final
			});
			jQuery.fancybox.hideActivity();
		} else if (typeof(jQuery.facebox) == 'function') {
			jQuery.facebox.settings.closeImage = closeImage;
			jQuery.facebox.settings.loadingImage = loadingImage;
			jQuery.facebox({ text: message_final }, 'my-groovy-style');
		}
		else {
			window.location.href = vmSiteurl+'index.php?option=com_virtuemart&view=cart';
		}
			
		emptyQuantity();
		if (jQuery(".vmCartModule")[0]) {
			Virtuemart.productUpdate(jQuery(".vmCartModule"));
		}
	} else {
		window.location.href = vmSiteurl+'index.php?option=com_virtuemart&view=cart';
	}
}

function faceboxError(message) {
		if(typeof(usefancy) == 'boolean' && usefancy){
			jQuery.fancybox({
				"titlePosition" : 	"inside",
				"transitionIn"	:	"elastic",
				"transitionOut"	:	"elastic",
				"type"			:	"html",
				"autoCenter"    :   true,
				"closeBtn"      :   false,
				"closeClick"    :   false,
				"content"       :   message
			});
			jQuery.fancybox.hideActivity();
		} else if (typeof(jQuery.facebox) == 'function') {
			jQuery.facebox.settings.closeImage = closeImage;
			jQuery.facebox.settings.loadingImage = loadingImage;
			jQuery.facebox({ text: message }, 'my-groovy-style');
			jQuery('#catproduct-loading').hide()
		}
		else {
			alert(message);
		}			
		emptyQuantity();
		if (jQuery(".vmCartModule")[0]) {
			Virtuemart.productUpdate(jQuery(".vmCartModule"));
		}
}
  
function prepareMessage (datas,product_name,quantity) {
	if (message_final == '') message_final = datas.msg.replace(/<h4>[^>]*>/g,"");
	for(i=gr_length-1;i>=0;i--){
		if(quantity[i]>0){
			message_final += "<H4>" + product_name[i] + ' ' + vmCartText.replace("%2$s x %1$s","") + "</H4>";
		}
    }
}

function prepareMessageError (datas,product_name,quantity) {
	message_final = datas.msg;
	for(i=gr_length-1;i>=0;i--){
		if(quantity[i]>0){
			message_final += "<H4>" + product_name[i] + "</H4>";
		}
    }
}  
  
function handleToCartOneByOne() {
	if(typeof(usefancy) == 'boolean' && usefancy){
			jQuery.fancybox.showActivity();
	}
	else {
		jQuery('#catproduct-loading').show();  // show Loading Div
	}
	
	var prod_count = 0;
	var is_qty = 0;
	var i=0;
	var sub_url=vmSiteurl+'index.php?option=com_virtuemart&view=cart&task=addJS&format=json&nosef=1';
	
	message_final = '';
	
	// add main/parent product to cart if set
	if (addparentoriginal == 1) { 
		if (jQuery(".addtocart-area").find("input[name='addtocart']").attr('class') == 'addtocart-button-disabled') {
			faceboxError(jQuery(".addtocart-area").find("input[name='addtocart']").val());
			return;
		}
		else {
			addParentToCart();
		}
	}  
  
 	jQuery.when(jQuery("#catproduct_form .product_id").each(function() {
		product_id = jQuery(this).val();
		if(product_id!=0){
			sub_url1 = '';
			noQ = jQuery(this).attr('id').replace('product_id_','');
			if (jQuery("#catproduct_form #virtuemart_product_id"+noQ).attr("type") == "radio") {
				quantity = 0;
				if ( jQuery("#catproduct_form #row_article_"+noQ+" input[name^='virtuemart_product_id[]']").is(":checked")) {
					quantity = getQuantity(noQ);
				}
			} else {
				quantity = getQuantity(noQ);
			}
			if (quantity > 0) {
 				product_name = jQuery("#product_name_"+noQ).val();
				is_qty=1;	
				sub_url1 += "&"+jQuery("#catproduct_form #row_article_"+noQ+" [name^=customPlugin]").serialize(); 
				sub_url1 += "&"+jQuery("#catproduct_form #row_article_"+noQ+" [name^=customPrice]").serialize(); 
				sub_url1 += "&"+jQuery("#catproduct_form #row_article_"+noQ+" [name^=customProductData]").serialize(); 
				sub_url1 += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customPlugin]").serialize();
				sub_url1 += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customPrice]").serialize(); 
				sub_url1 += "&"+jQuery("#catproduct_form .cell_parent_customfields [name^=customProductData]").serialize(); 
				
				var url= sub_url + "&" + sub_url1 ;
				
				var request = jQuery.ajax({
					type: 'POST',
					url: url,
					traditional: true,
					data: { 'virtuemart_product_id[]' : product_id, 'quantity[]' : quantity },
					success: function(datas) { 
						if (message_final == '') message_final = datas.msg.replace(/<h4>[^>]*>/g,"");
						message_final += "<H4>" + product_name + ' ' + vmCartText.replace("%2$s x %1$s","") + "</H4>";
					},
					async: false,
					dataType: 'json'
				});
				
			}
			prod_count += 1;
		} 
	})).done(function() { 
		if(prod_count>0) {//if products

			if (is_qty != 0) {
				faceboxShow();
				jQuery('#catproduct-loading').hide();

			} // if is quantity
			else {if (qty_ok == 0) faceboxError(noquantityerror); else faceboxShow(); jQuery('#catproduct-loading').hide();}
		} //if(product_ids)
		else {if (qty_ok == 0) faceboxError(noproducterror); else faceboxShow(); jQuery('#catproduct-loading').hide();}
	});  
}

function addOneProduct(rowID) {
	jQuery('#catproduct-loading').hide().ajaxStart( function() {
	jQuery(this).show();  // show Loading Div
	} ).ajaxStop ( function(){
	jQuery(this).hide(); // hide loading div
	});
	
	product_id = jQuery("#product_id_"+rowID).val();
	quantity = jQuery("#quantity_"+rowID).val();
	product_name = jQuery("#product_name_"+rowID).val();
	
	var sub_url=vmSiteurl+'index.php?option=com_virtuemart&view=cart&task=addJS&format=json&nosef=1';
	//sub_url+= '&view=cart&task=addJS&format=json';
	var url=sub_url;
	//alert(url);
	var request = jQuery.ajax({
	type: 'POST',
	url: url,
	traditional: true,
	data: { 'virtuemart_product_id[]' : product_id, 'quantity[]' : quantity },
	success: function(datas) { message_final = datas.msg; 
	message_final += "<H4>" + product_name + ' ' + vmCartText + "</H4>"; faceboxShow();},
	dataType: 'json'
	});
}

function addParentToCart() {
	var sub_url=vmSiteurl+'index.php?option=com_virtuemart&view=cart&task=addJS&format=json&nosef=1';
	//productdetails
	sub_url1 = jQuery(originaladdtocartareclass).find('form').serialize()

	var url= sub_url + "&" + sub_url1 ;
				
	var request = jQuery.ajax({
		type: 'POST',
		url: url,
		traditional: true,
		success: function(datas) { 
			if (message_final == '') message_final = datas.msg.replace(/<h4>[^>]*>/g,"");
			message_final += "<H4>" + mainproductname + ' ' + vmCartText.replace("%2$s x %1$s","") + "</H4>";
			qty_ok = 1;
		},
		async: false,
		dataType: 'json'
	});
}

// find main product price
function FindMainProductPrice () {
if (addparentoriginal == 1) { 
	var noQ = 'parent';
	
	var url = window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&format=json' + window.vmLang;
	url += "&"+jQuery(originaladdtocartareclass).find("form").serialize();
	url = url.replace("&view=cart", "");
	
	jQuery.getJSON(url,
					function (datas, textStatus) {
						for (var key in datas) {
						var value = datas[key];
						if(value!=0 && value != '' && value != null) {
							if (decimalsymbol == ".") value = parseFloat(value.replace(/[^0-9-.]/g, ''));
							if (decimalsymbol == ",") value = parseFloat(value.replace(/[^0-9-,]/g, '').replace(",", "."));
							if (typeof jQuery("#catproduct_form").find("#"+key+"_"+noQ).val() == 'undefined') {
								jQuery("#catproduct_form").append('<input type="hidden" name="'+key+'_'+noQ+'" value="'+value+'" id="'+key+'_'+noQ+'">'); 
							}
							else { 
								jQuery("#catproduct_form").find("#"+key+"_"+noQ).val(value);
							}
						}
						}
						updateSumPrice(0); 
					});
	return false;
}
}

jQuery(document).ready(function() {
	jQuery("#catproduct_form .quantity-input").keyup(function() {
		calculateTotalQty();
	});
});
	
function calculateTotalQty () {
	qty = 0;
	jQuery("#catproduct_form .quantity-input").each(function () {
		qty += jQuery(this).val().toInt();
	});
	jQuery("#catproduct_form #quantity_total").html(qty);
}
  

