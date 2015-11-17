<?php
/**
 * EMCustomRegistration class.
 *
 * @since 0.1.0
 */
class EMCustomRegistration {

	protected $registration_success_notice=false;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('emcl_before_register-form','emcl_show_error_messages');
		add_action('init',array($this,'add_new_user'));
		add_action('login_form_register', array($this,'register_form_redirect'));

		add_shortcode('emcl-registration-form',array($this,'registration_form'));
	}

	/**
	 * registration_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function registration_form() {
		if (is_user_logged_in())
			return emcl_get_template_html('logged-in');

		if ($this->registration_success_notice)
			echo emcl_format_error_message('','Please check your email to activate your account.','success');

		return emcl_get_template_html('register-form');
	}

	/**
	 * register_form_redirect function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_form_redirect() {
		$slug=emcl_page_slug('register');

		if ($slug) :
			wp_redirect(home_url($slug));
			exit;
		endif;
	}

	/**
	 * add_new_user function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_new_user() {
	  if (isset($_POST["custom_user_login_reg"]) && wp_verify_nonce($_POST['custom_register_nonce'],'custom-register-nonce')) :
			$user_login=$_POST["custom_user_login_reg"];
			$user_email=$_POST["custom_user_email"];
			$user_first=$_POST["custom_user_first"];
			$user_last=$_POST["custom_user_last"];
			$user_pass=$_POST["custom_user_pass"];
			$pass_confirm=$_POST["custom_user_pass_confirm"];

			// Username already registered
			if (username_exists($user_login))
				emcl_add_error_message('username_unavailable','Username already taken');

			// invalid username
			if (!validate_username($user_login))
				emcl_add_error_message('username_invalid','Invalid username');

			// empty username
			if ($user_login == '')
				emcl_add_error_message('username_empty','Please enter a username');

			//invalid email
			if (!is_email($user_email))
				emcl_add_error_message('email_invalid','Invalid email');

			//Email address already registered
			if (email_exists($user_email))
				emcl_add_error_message('email_used','Email already registered');

			// passwords do not match
			if ($user_pass == '')
				emcl_add_error_message('password_empty','Please enter a password');

			// passwords do not match
			if ($user_pass != $pass_confirm)
				emcl_add_error_message('password_mismatch','Passwords do not match');

			// only create the user in if there are no errors
			if (!emcl_has_error_messages()) :
				$new_user_id = wp_insert_user(array(
						'user_login'		=> $user_login,
						'user_pass'	 		=> $user_pass,
						'user_email'		=> $user_email,
						'first_name'		=> $user_first,
						'last_name'			=> $user_last,
						'user_registered'	=> date('Y-m-d H:i:s'),
						'role'				=> 'subscriber'
					)
				);

				if ($new_user_id) :
					// send an email to the admin alerting them of the registration
					emcl_user_activation_email($new_user_id,'both');

					// if activation is required, we skip
					if (!emcl_is_activation_required()) :
						// log the new user in
						wp_setcookie($user_login, $user_pass, true);
						wp_set_current_user($new_user_id, $user_login);
						do_action('wp_login', $user_login);

						// send the newly created user to the home page after logging them in
						wp_redirect(home_url());
						exit;
					else :
						$this->registration_success_notice=true;
					endif;
				endif;
			endif;

		endif;
	}

}

new EMCustomRegistration();
?>