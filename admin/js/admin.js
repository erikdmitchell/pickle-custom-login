jQuery(document).ready(function($) {

    // display/hide recaptcha fields on load //
    if ($('#enable_recaptcha').is(':checked')) {
        $('.recaptcha-details-field').each(function() {
            $(this).removeClass('hidden');
        });
    }

    // display/hide recaptcha fields on click //
    $('#enable_recaptcha').on('click', function() {
        $('.recaptcha-details-field').each(function() {
            $(this).toggleClass('hidden');
        });
    });

});