jQuery(document).ready(function($) {

	$('.button.send-demo-email').click(function(e) {
		e.preventDefault();

		var data = {
			'action': 'send_test_email',
			'type': $(this).data('type')
		};

		jQuery.post(ajaxurl,data,function(response) {
console.log(response);
		});
	});

});