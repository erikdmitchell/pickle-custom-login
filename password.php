<?php
class EMCustomPasswordReset {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('emcl_before_forgot-password','emcl_show_error_messages');
		add_action('login_form_lostpassword',array($this,'process_reset_password_form'));
		add_action('login_form_lostpassword',array($this,'redirect_to_password_reset'));
		add_action('login_form_rp',array($this,'redirect_to_custom_password_reset'));
		add_action('login_form_resetpass',array($this,'redirect_to_custom_password_reset'));
		add_action('login_form_rp',array($this,'reset_password'));
		add_action('login_form_resetpass',array($this,'reset_password'));

		add_filter('retrieve_password_message',array($this,'replace_retrieve_password_message'),10,4);

		add_shortcode('emcl-forgot-password-form',array($this,'forgot_password_form'));
		add_shortcode('emcl-reset-password-form',array($this,'password_reset_form'));
	}

	/**
	 * forgot_password_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function forgot_password_form() {
		if (is_user_logged_in())
			return __('You are already signed in.','dummy');

		if (isset($_GET['errors']))
			$this->process_error_codes($_GET['errors']);

		return emcl_get_template_html('forgot-password');
	}

	public function password_reset_form() {
		if (is_user_logged_in())
			return __('You are already signed in.','dummy');

		if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) :
			if (isset($_REQUEST['error']))
				echo '<b>Error</b>: '.$_REQUEST['error'];

			return emcl_get_template_html('reset-password');
		else:
			return __('Invalid password reset link.','dummy');
		endif;

		if (isset($_GET['login'])) :
			switch ($_GET['login']) :
				case 'expiredkey':
					return __('<b>Error</b>: You need to have a valid key. Try again.','dummy');
					break;
				case 'invalidkey':
		    	return __('<b>Error</b>: You need to have a valid key. Try again.','dummy');
		    	break;
		  endswitch;
		endif;

		//return emcl_get_template_html('reset-password');
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

			wp_redirect(home_url('forgot-password'));
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
				$redirect_url=home_url('forgot-password');
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

	/**
	 * replace_retrieve_password_message function.
	 *
	 * @access public
	 * @param mixed $message
	 * @param mixed $key
	 * @param mixed $user_login
	 * @param mixed $user_data
	 * @return void
	 */
	public function replace_retrieve_password_message($message,$key,$user_login,$user_data) {
		$msg= __( 'Hello!', 'dummy' ) . "\r\n\r\n";
		$msg.= sprintf( __( 'You asked us to reset your password for your account using the email address %s.', 'dummy' ), $user_login ) . "\r\n\r\n";
		$msg.= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'dummy' ) . "\r\n\r\n";
		$msg.= __( 'To reset your password, visit the following address:', 'dummy' ) . "\r\n\r\n";
		$msg.= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
		$msg.= __( 'Thanks!', 'dummy' ) . "\r\n";

		return $msg;
	}

	/**
	 * redirect_to_custom_password_reset function.
	 *
	 * Redirects to the custom password reset page, or the login page if there are errors.
	 *
	 * @access public
	 * @return void
	 */
	public function redirect_to_custom_password_reset() {
		if ('GET'==$_SERVER['REQUEST_METHOD']) :
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] ); // Verify key / login combo

			if ( ! $user || is_wp_error( $user ) ) :
				if ( $user && $user->get_error_code() === 'expired_key' ) :
					wp_redirect(home_url('/reset-password?login=expiredkey'));
				else:
					wp_redirect(home_url('/reset-password?login=invalidkey'));
				endif;
				exit;
			endif;

			$redirect_url=home_url('reset-password');
			$redirect_url=add_query_arg('login',esc_attr($_REQUEST['login']),$redirect_url);
			$redirect_url=add_query_arg('key',esc_attr($_REQUEST['key']),$redirect_url);

			wp_redirect($redirect_url);
			exit;
		endif;
	}

	/**
	 * reset_password function.
	 *
	 * Resets the user's password if the password reset form was submitted.
	 *
	 * @access public
	 * @return void
	 */
	public function reset_password() {
		if ('POST'==$_SERVER['REQUEST_METHOD']) :

			$rp_key=$_REQUEST['rp_key'];
			$rp_login=$_REQUEST['rp_login'];
			$user=check_password_reset_key($rp_key,$rp_login);

			if (!$user || is_wp_error($user)) :
				if ($user && $user->get_error_code()==='expired_key') :
					wp_redirect(home_url('/reset-password?login=expiredkey'));
				else :
					wp_redirect(home_url('/reset-password?login=invalidkey'));
				endif;

				exit;
			endif;


			if ( isset( $_POST['pass1'] ) ) :
				if ( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = home_url('/reset-password/');
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

					wp_redirect($redirect_url);
					exit;
				}

				if (empty($_POST['pass1'])) {
					// Password is empty
					$redirect_url=home_url('/reset-password/');
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

					wp_redirect($redirect_url);
					exit;
				}

				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				wp_redirect( home_url( 'login?password=changed' ) );
			else :
				echo "Invalid request.";
			endif;

			exit;
		endif;
exit;
	}

}

new EMCustomPasswordReset();
?>