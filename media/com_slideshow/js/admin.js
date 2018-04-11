jQuery(document).ready(function () {
	jQuery('#arrows-type input[name="params[slideshow_navigation_type]"]').change(function(){
		jQuery(this).parents('ul').find('li.active').removeClass('active');
		jQuery(this).parents('li').addClass('active');
	});
	
		
	jQuery('input[data-slider="true"]').bind("slideshow:changed", function (event, data) {
		 jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
		 jQuery(this).val(parseInt(data.value));
	});
		
	
	
});

  jQuery(function() {
    jQuery( "#images-list" ).sortable({
      stop: function() {
			jQuery("#images-list li").removeClass('has-background');
			count=jQuery("#images-list li").length;
			for(var i=0;i<=count;i+=2){
					jQuery("#images-list li").eq(i).addClass("has-background");
			}
			jQuery("#images-list li").each(function(){
				jQuery(this).find('.order_by').val(count - jQuery(this).index());
			});
      },
      revert: true
    });
   // jQuery( "ul, li" ).disableSelection();
  });