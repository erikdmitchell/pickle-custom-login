<?php
class EMCustomLoginErrors {

	public function __construct() {

	}

	public function register_errors() {
		static $wp_error; // Will hold global variable safely
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}

	public function show_error_messages() {
		if ($codes=$this->register_errors()->get_error_codes()) :
			echo '<div class="custom-login-errors">';
			  // Loop error codes and display errors
			  foreach ($codes as $code) :
			  	$message = $this->register_errors()->get_error_message($code);
			    echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
			  endforeach;
			echo '</div>';
		endif;
	}

}

$custom_login_errors=new EMCustomLoginErrors();
?>