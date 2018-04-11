jQuery(function($) {
    $('.helix3-toggler').on('click', function(event) {
        event.preventDefault();
        $(this).parent().parent().toggleClass('opened');
    });
    var presets = $('.helix3-presets').find('li');
    presets.each(function() {
        $(this).find('a').on('click', function(event) {
            event.preventDefault();
            var currentPreset = $(this).parent().data('preset');
            presets.removeClass('active');
            $(this).parent().addClass('active'); 
            $('.preset').removeAttr('href').attr('href', helix3_template_uri + '/css/presets/preset' + currentPreset + '.css');
            $.removeCookie(helix3_template + '_preset');
            $.cookie(helix3_template + '_preset', 'preset' + currentPreset, {
                expires: 1
            });
        });
    });
    $('#helix3-boxed').on('change', function() {
        if ($(this).is(':checked')) {
            $('body').addClass('layout-boxed');
        } else {
            $('body').removeClass('layout-boxed');
        }
    });
    $('#helix3-boxed').on('change', function() {
        if ($(this).is(':checked')) {
            $('body').addClass('layout-boxed');
        } else {
            $('body').removeClass('layout-boxed').removeAttr('style');
        }
    });
});