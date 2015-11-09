<?php
class EMCustomPasswordReset {

	//protected $errors=array();

	public function __construct() {
		//add_action('emcl_before_forgot-password','emcl_show_error_messages');
		add_action('login_form_lostpassword',array($this,'process_reset_password_form'));
		add_action('login_form_lostpassword',array($this,'redirect_to_password_reset'));

		add_shortcode('emcl-password-form',array($this,'forgot_password_form'));
	}

	/**
	 * forgot_password_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function forgot_password_form() {
		if (is_user_logged_in())
			return 'You are already signed in.';

		if (isset($_GET['errors']))
			$this->process_error_codes($_GET['errors']);

		return emcl_get_template_html('forgot-password');
	}

	/**
	 * redirect_to_password_reset function.
	 *
	 * Redirects the user to the custom "Forgot your password?" page instead of wp-login.php?action=lostpassword.
	 *
	 * @access public
	 * @return void
	 */
	public function redirect_to_password_reset() {
		if ('GET'==$_SERVER['REQUEST_METHOD']) :
			if (is_user_logged_in()) :
				$this->redirect_logged_in_user();
				exit;
			endif;

			wp_redirect(home_url('password-reset'));
			exit;
		endif;
	}

	/**
	 * process_reset_password_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function process_reset_password_form() {
		if ('POST'==$_SERVER['REQUEST_METHOD']) :
			$_errors=array();
			$errors=retrieve_password();

			if (is_wp_error($errors)) :
				// Errors found
				$redirect_url=home_url('password-reset');
				$redirect_url=add_query_arg('errors',join(',',$errors->get_error_codes()),$redirect_url);
			else :
				// Email sent
				$redirect_url=home_url('login');
				$redirect_url=add_query_arg('checkemail','confirm',$redirect_url);
			endif;

			wp_redirect($redirect_url);
			exit;
		endif;
	}

	/**
	 * process_error_codes function.
	 *
	 * @access protected
	 * @param array $codes (default: array())
	 * @return void
	 */
	protected function process_error_codes($codes=array()) {
		if (!is_array($codes)) :
			$code=$codes;
			$codes=array($code);
		endif;

		foreach ($codes as $code) :
			switch ($code) :
				case 'empty_username':
					echo __('<b>Error</b>: You need to enter your email address to continue.','dummy');
					break;
				case 'invalid_email':
				case 'invalidcombo':
		    	echo __('<b>Error</b>: There are no users registered with this email address.','dummy');
		    	break;
		    default:
		    	break;
		  endswitch;
		 endforeach;

		return;
	}

}

new EMCustomPasswordReset();
?>