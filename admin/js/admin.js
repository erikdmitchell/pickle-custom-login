jQuery(document).ready(function($) {

	// display/hide recaptcha fields on load //
	if ($('#enable_recaptcha').is(':checked')) {
		$('.recaptcha-details-field').each(function () {
			$(this).removeClass('hidden');
		});
	}

	// display/hide recaptcha fields on click //
	$('#enable_recaptcha').on('click', function () {		
		$('.recaptcha-details-field').each(function () {
			$(this).toggleClass('hidden');
		});
	});
// activation-key-details-field
	// display/hide require activation fields on load //
	if ($('#require_activation_key').is(':checked')) {
		$('.require_activation_key_sub').each(function () {
			$(this).removeClass('hide-if-js');
		});
		$('.hide-if-activation-key').each(function () {
			$(this).addClass('hide-if-js');
		});
	}

	// display/hide require activation fields on click //
	$('#require_activation_key').click(function () {
		$('.require_activation_key_sub, .hide-if-activation-key').each(function () {
			$(this).toggleClass('hide-if-js');
		});
	});

});