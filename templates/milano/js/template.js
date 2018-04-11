jQuery(document).ready(function($){
    
    // spinctrl
	if ($('.spinctrl').length > 0) {
		$('.spinctrl').TouchSpin({
			min: 1,
			max: 999
		});
	} 
	/* Testimonial Carousel */
	if( $(".testimonial .owl").length ) {
		$(".testimonial .owl").owlCarousel({
			items : 		1,
			nav : 	false,
			dots : 	true,				
		});
	}
	
	/*  Page loader */
	if( $('#pageloader').length ) {
		setTimeout(function() {
			$( 'body' ).addClass( 'loaded' );
				setTimeout(function () {
				$('#pageloader').remove();
			}, 1500);
		}, 1500);
	}	

	/* Goto Top */		
	$(window).scroll(function(event) {	
		if ($(this).scrollTop() > 300) {
			$('.sp-totop').fadeIn();
			$('.sp-totop').css({"visibility": "visible"});
		} else {
			$('.sp-totop').fadeOut();
		}
	});
	
	$('.sp-totop').on('click', function() {
        $('html, body').animate({
            scrollTop: $("body").offset().top
        }, 500);
    });	
	
	/* Fix conflict MooTools and Bootstrap */
	var bootstrapLoaded = (typeof $().carousel == 'function');
	var mootoolsLoaded = (typeof MooTools != 'undefined');
	if (bootstrapLoaded && mootoolsLoaded) {
		Element.implement({
			hide: function () {
				return this;
			},
			show: function (v) {
				return this;
			},
			slide: function (v) {
				return this;
			}
		});
	}
	
	/*Fix Carousel*/
	if( $(".carousel").length ){
		$(".carousel").each( function() {
			$(this).parent().addClass("wrap-carousel");
		});
	}
});