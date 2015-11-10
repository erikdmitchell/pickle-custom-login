jQuery(document).ready(function($) {

	if ($('#require_activation_key').is(':checked')) {
		$('.require_activation_key_sub').each(function () {
			$(this).removeClass('hide-if-js');
		});
		$('.hide-if-activation-key').each(function () {
			$(this).addClass('hide-if-js');
		});
	}

	$('#require_activation_key').click(function () {
		$('.require_activation_key_sub, .hide-if-activation-key').each(function () {
			$(this).toggleClass('hide-if-js');
		});
	});

});