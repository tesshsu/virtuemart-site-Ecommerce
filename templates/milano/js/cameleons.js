/**
 * jQuery.browser.mobile (http://detectmobilebrowser.com/)
 *
 * jQuery.browser.mobile will be true if the browser is a mobile device
 *
 **/

jQuery( document ).ready(function() {

    jQuery('.bxslider').bxSlider({
        video: true,
        useCSS: false,
        pager: false,
        auto: true,
        autoHover: true,
        controls: true,
        responsive: true,
        nextText: ">",
        prevText: "<"
    });

    if(jQuery("a#image-main-link").length) {
		jQuery("a#image-main-link").fancybox();
	}

    //BRUNO CAMELEONS : liste produit par cat

    //BRUNO CAMELEONS : Scrollbar
    jQuery(".listeProduitsCat").mCustomScrollbar();

    jQuery(".product-detail .listeProduitsTitre").on("click", function(){
        if(jQuery(this).hasClass('closed')) {
            jQuery(this).removeClass('closed');
            jQuery( ".product-detail .listeProduitsCat" ).slideDown( "slow");
        } else {
            jQuery(this).addClass('closed');
            jQuery( ".product-detail .listeProduitsCat" ).slideUp( "slow");
        }
    });

	//BRUNO CAMELEONS : Mise à jours de l'image à destination de la lightbox en fonction du choix du carrousel
	jQuery("body.view-productdetails a.thumb-link").on("click", function(){
		var link = jQuery(this).attr("data-image");
		jQuery("a#image-main-link").attr("href", link);
	});

	 //#issue120 : Controles sur les champs pour n'authoriser que les caracteres latins except la dernier
    jQuery("form#adminForm, form#userForm").on("submit", function(event) {
        var isTva = true;
        var isLatin = true;
        var inputsTextAdmin = jQuery('form#adminForm input[type=text]:not(#address_chinoise_field)');
        var inputsTextUser = jQuery('form#userForm input[type=text]:not(#address_chinoise_field):not(#shipto_address_chinoise_field)');
        var inputsTva = jQuery('form#adminForm #pluginistraxx_euvatchecker_field');
        var rforeign = /^[\x00-\xFF]*$/;
        var r2foreign = /^[\x00-\xFF]*$/;
        var regTvaPattern = /^(RO\d{2,10}|GB\d{5}|GBGD\d{3}|CHE\d{9}|CZ\d{8,10}|(ATU|DK|FI|HU|LU|MT|SI)\d{8}|IE[A-Z\d]{8}|(DE|BG|EE|EL|LT|BE0|PT)\d{9}|CY\d{8}[A-Z]|(ES|GB)[A-Z\d]{9}|(BE0|PL|SK|RU)\d{10}|(FR|IT|LV)\d{11}|(LT|SE)\d{12}|(NL|GB)[A-Z\d]{12})$/;
        
        jQuery.each(inputsTextAdmin, function(){
            if(!rforeign.test(jQuery(this).val())) {
                isLatin = false;
            }
        });

        jQuery.each(inputsTextUser, function(){
            if(!r2foreign.test(jQuery(this).val())) {
                isLatin = false;
            }          
        });

       jQuery.each(inputsTva, function(){
            if(!regTvaPattern.test(jQuery(this).val())) {
                isTva = false;
            }
        });
        
        if (!isLatin)  {
          event.preventDefault();
          alert('Sorry, only Latin characters are allowed in this form except Address in Chinese / 对不起，此表格中只允许使用拉丁字符，但中文地址除外。');
          jQuery(this).prop('disabled', true);
          //jQuery( "<spam class='rformErro'>Sorry, only Latin characters are allowed in this form except Address in Chinese / 对不起，此表格中只允许使用拉丁字符，但中文地址除外。</spam>" ).insertAfter( "#address_chinoise_field, #shipto_address_chinoise_field" );
        }else {
           jQuery(this).prop('disabled',false);
        }

        if (!isTva)  {
            inputsTva.val("");
            //alert('Your TVA numbers is not correct, this will not save to our database.');
            jQuery(this).prop('disabled', true);
        }else {
            inputsTva.val();
            jQuery(this).prop('disabled',false);
        }

        var tel = jQuery("form#adminForm input#phone_1_field").val();
        var portable = jQuery("form#adminForm input#phone_2_field").val();

        if(tel.length == 0 && portable.length == 0) {
            event.preventDefault();
            alert('Merci de renseigner au moins un numéro de téléphone. Please fill at least one phone number');
        }else if(tel.length > 20 || portable.length > 20 ) {
            event.preventDefault();
            alert('Phone: Remplissez moins de 20 numéros. Please fill in less then 20 numbers');
        }

    });

    //binding message if user keyin chinese address input
    jQuery("#address_chinoise_field, #shipto_address_chinoise_field").one("focus", function(event) {
        jQuery( "<spam class='rformErro'>Addresses written in Chinese are mandatory for orders shipped to China. / 运往中国的订单以中文书写的地址是强制性的。</br> 姓名,电话,省份,城市,区县,街道名称与门牌号码,邮编</spam>" ).insertAfter( "#address_chinoise_field, #shipto_address_chinoise_field" );
    });
    //binding message if user keyin address input
    jQuery("#address_1_field, #shipto_address_1_field").one("focus", function(event) {
        jQuery( "<spam class='focusMs'>Your orders will not be delivered if you add a P.O Box in your address</spam>" ).insertAfter( "#address_1_field, #shipto_address_1_field" );
    });
    //binding message if user keyin TVA
    jQuery("#pluginistraxx_euvatchecker_field").one("focus", function(event) {
        jQuery( "<spam class='rformErro'>Without spaces and dashes example: FR99999999</spam>" ).insertAfter( "#pluginistraxx_euvatchecker_field" );
    });
    
    //Issue #157  Add Regex for TVA input France site
    /*jQuery('#pluginistraxx_euvatchecker_field').on("keyup", function () {
        var inputsTVA = jQuery(this).val();
        if(inputsTVA.contains('-')){
            var result = inputsTVA.replace(new RegExp('-', 'g'),"");
        }
        var result = inputsTVA.split(' ').join('');
        jQuery(this).val(result);
    });*/

    
	//BRUNO CAMELEONS : Suppression du bug de double moins sur les réduction de paiement
	var payment_discount = jQuery("span.vmpayment span.vmpayment_cost.discount:contains('--')");
	if(payment_discount.length) {
		var text = payment_discount.text().replace('--', '-');
        payment_discount.html(text);
	}

    //FLORIAN CAMELEONS : modification de l'image en background du slider accueil
    //Fonctionne uniquement sous chrome, meilleure méthode mais innutilisable sur d'autres navigateurs pour l'instant
	/*jQuery('.catSlider').css({"background-image" : "url(/images/fond-accueil.jpg)"});
    
    var counter = 0;
    function setBckImage(){
        if(counter<2){
            counter++;
        } else {
            counter=1;
        }

        
        switch (counter){
            case 1:
                jQuery('.catSlider').css({"background-image" : "url(/images/fond-accueil.jpg)"});
                break;
            case 2:
                jQuery('.catSlider').css({"background-image" : "url(/images/slider_accueil_2.jpg)"});
                break;
        }
    }

    if(jQuery('.catSlider').length) {
        setInterval(setBckImage, 5000);
    }*/
    
    //FLORIAN CAMELEONS : modification de l'image en background du slider accueil
    //Fonctionnelle partout mais nécessite de modifier le header de la page html constamment
    var counter = 0;
    function setBckImage(){
        if(counter<3){
            counter++;
        } else {
            counter=1;
        }

        
        switch (counter){
            case 1:
                //jQuery('.catSlider::before').css({"opacity" : 1});
                jQuery('head').append('<style>.catSlider:before{opacity:1;}</style>');
                break;
            case 2:
                //jQuery('.catSlider::before').css({"opacity" : 0});
                jQuery('head').append('<style>.catSlider:before{opacity:0;}</style>');
                break;
            case 3:
                //jQuery('.catSlider::before').css({"opacity" : 0});
                jQuery('head').append('<style>.catSlider:after{opacity:0;}</style>');
                break;
        }
    }

    if(jQuery('.catSlider').length) {
        setInterval(setBckImage, 5000);
    }
    
    //FLORIAN CAMELEONS : methode 2 changement d'image en background du slider accueil
    /*var img_array = [1, 2],
        newIndex = 0,
        index = 0,
        interval = 5000;
    (function changeBg() {
        index = (index + 1) % img_array.length;

        jQuery('.catSlider').css('backgroundImage', function () {
            jQuery('#fullPage').animate({
                backgroundColor: 'transparent'
            }, 1000, function () {
                setTimeout(function () {
                    jQuery('.catSlider').animate({
                        backgroundColor: 'rgb(255,255,255)'
                    }, 1000);
                }, 3000);
            });
            return 'url(/images/slider_accueil_' + img_array[index] + '.jpg)';
        });
        setTimeout(changeBg, interval);
    })();*/

    //BRUNO CAMELEONS Modif box achat
    jQuery('body').on('click', '#fancybox-content a.continue_link', function(event) {
        event.preventDefault();
        jQuery("#fancybox-close").trigger("click");
    });

    /*BRUNO CAMELEONS Fix description read more
    jQuery('label.read-more-trigger').on('click',function(){
        jQuery('div.read-more-target').prev().css('float', 'left');
        jQuery('div.read-more-target').prev().css('margin-right', '5px');
    }); */

    jQuery.noConflict();
    if(jQuery('.nivo-column').length){
        jQuery('.nivo-column').columnize({ columns: 2 });
    }
    
    if(jQuery('.nivo-bloc-slider').length){
        jQuery('.nivo-bloc-slider').each(function(){
            var thisslider = jQuery(this);
            thisslider.find('.nivo-bloc-text').width('290px');
            var thisbloctextheight = thisslider.find('.nivo-bloc-text').height();
            var thisbloctextwidth = thisslider.find('.nivo-bloc-text').width();
            thisslider.find('.nivo-background-transparent').height(thisbloctextheight);
            thisslider.find('.door').css('top',thisbloctextheight-thisslider.find('.door').height());
            //Toggle click
            var doorparams = {lineheight:thisslider.find('.door').css('line-height'),fontsize:thisslider.find('.door').css('font-size')};
            thisslider.find('.door').toggle(function(e){
                jQuery(this).animate({top:(thisslider.height()-jQuery(this).height())});
                jQuery(this).text('-');
                jQuery(this).css({lineHeight:'22px',fontSize:'54px'});
                thisslider.find('.nivo-background-transparent').animate({height:'100%'});
                thisslider.find('.nivo-bloc-text').animate({height:'100%',width:'836px'},function(){
                    thisslider.find('.nivo-column,.nivo-bloc-images').fadeIn();
                });
                e.preventDefault();
            }, function(e) {
                jQuery(this).text('+');
                jQuery(this).css({lineHeight:doorparams.lineheight,fontSize:doorparams.fontsize});
                thisslider.find('.nivo-column,.nivo-bloc-images').fadeOut(function(){
                    thisslider.find('.door').animate({top:(thisbloctextheight-thisslider.find('.door').height())});
                    thisslider.find('.nivo-background-transparent').animate({height:thisbloctextheight});
                    thisslider.find('.nivo-bloc-text').animate({height: thisbloctextheight,width:thisbloctextwidth});
                });
                e.preventDefault();
            });
        });
    }




    
    
});