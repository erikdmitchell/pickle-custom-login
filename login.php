<?php
/**
 * EMLogin class.
 *
 * @since 0.1.0
 */
class EMLogin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('init',array($this,'login_member'));
		add_action('init',array($this,'redirect_login_page'));
		add_action('emcl_before_login-form','emcl_show_error_messages');
		add_action('wp_login_failed',array($this,'login_failed'));
		add_action('wp_logout',array($this,'logout_page'));

		add_filter('authenticate',array($this,'verify_username_password'),1,3);

		add_shortcode('emcl-login-form',array($this,'login_form'));
	}

	/**
	 * login_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function login_form() {
		if (is_user_logged_in())
			return emcl_get_template_html('logged-in');

		if (isset($_GET['checkemail']) && $_GET['checkemail']=='confirm')
			echo emcl_format_error_message('','An email has been set to the address provided with instructions on how to reset your password.','success');

		if (isset($_GET['password']) && $_GET['password']=='changed')
			echo emcl_format_error_message('','Your password has been changed. Please login.','success');

		return emcl_get_template_html('login-form');
	}

	/**
	 * login_member function.
	 *
	 * @access public
	 * @return void
	 */
	public function login_member() {
		$redirect=get_option('emcl-login-redirect', home_url());

		if (isset($_POST['custom_user_login']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom-login-nonce')) :
			// this returns the user ID and other info from the user name
			$user=get_user_by('login',$_POST['custom_user_login']);

			// if the user name doesn't exist
			if (!$user)
				emcl_add_error_message('empty_username', 'Invalid username');

			// if no password was entered
			if (!isset($_POST['custom_user_pass']) || $_POST['custom_user_pass'] == '')
				emcl_add_error_message('empty_password', 'Please enter a password');

			// check the user's login with their password
			if (!isset($user->user_pass) || !wp_check_password($_POST['custom_user_pass'], $user->user_pass, $user->ID))
				emcl_add_error_message('empty_password', 'Incorrect password');

			// check if activation is required and if so, user is active //
			if (isset($user->ID) && emcl_is_activation_required() && !emcl_is_user_authenticated($user->ID))
				emcl_add_error_message('not_activated', 'You must activate your account before logging in.');

			// only log the user in if there are no errors
			if (!emcl_has_error_messages()) {
				wp_setcookie($_POST['custom_user_login'], $_POST['custom_user_pass'], true);
				wp_set_current_user($user->ID, $_POST['custom_user_login']);
				do_action('wp_login', $_POST['custom_user_login']);

				if (current_user_can('administrator'))
					$redirect=admin_url();

				wp_safe_redirect($redirect);
				exit;
			}
		endif;
	}

	/**
	 * redirect_login_page function.
	 *
	 * redirects the default wp login page to our login page (template done in fc_login_template_redirect())
	 *
	 * @access public
	 * @return void
	 */
	public function redirect_login_page() {
		$slug=emcl_page_slug('login');
		$page_viewed=basename($_SERVER['REQUEST_URI']);

		if ($slug) :
			$login_page=home_url($slug);

			if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') :
				wp_safe_redirect($login_page);
				exit;
			endif;
		endif;
	}

	/**
	 * login_failed function.
	 *
	 * redirects failed login to our page
	 *
	 * @access public
	 * @return void
	 */
	public function login_failed() {
		$slug=emcl_page_slug('login');

		if ($slug) :
			$login_page=home_url($slug);

			wp_safe_redirect($login_page.'?login=failed');
			exit;
		endif;
	}

	/**
	 * logout_page function.
	 *
	 * redirects logout to our page
	 *
	 * @access public
	 * @return void
	 */
	public function logout_page() {
		$redirect=get_option('emcl-logout-redirect', home_url());

		wp_safe_redirect($redirect.'?login=false');
		exit;
	}

	/**
	 * verify_username_password function.
	 *
	 * redirects login errors to our page
	 *
	 * @access public
	 * @param mixed $user
	 * @param mixed $username
	 * @param mixed $password
	 * @return void
	 */
	public function verify_username_password( $user, $username, $password ) {
		$slug=emcl_page_slug('login');

		if ($slug) :
			$login_page=home_url($slug);

			if ($username == "" || $password == "") :
				wp_safe_redirect($login_page."?login=empty");
				exit;
			endif;
		endif;
	}

}

new EMLogin();
?>
