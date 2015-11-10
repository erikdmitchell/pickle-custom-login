<?php
class EMCustomLoginUserActivation {

	public function __construct() {
		add_shortcode('user_activation',array($this,'user_activation_form'));
	}

	public function user_activation_form() {
		global $wpdb;

		$user_id=$wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login='{$_GET['user_login']}' AND  user_activation_key='{$_GET['key']}'");
		$is_user_activated=false;

		if ($user_id) :
			$code=get_user_meta($user_id,'has_to_be_activated',true);
			if ($code==$_GET['key']) :
				delete_user_meta( $user_id,'has_to_be_activated');
				$is_user_activated=true;
			endif;
		endif;

		return emcl_get_template_html('user-activation-form');
	}

}

/**
 * mdw_user_activation_email function.
 *
 * @access public
 * @param mixed $user_id
 * @param string $notify (default: '')
 * @return void
 */
function mdw_user_activation_email($user_id,$notify='') {
	if (!$user_id || is_wp_error($user_id))
		return false;

	global $wpdb, $wp_hasher;
	$user = get_userdata( $user_id );

  // The blogname option is escaped with esc_html on the way into the database in sanitize_option we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( 'admin' === $notify || empty( $notify ) ) {
		return;
	}

	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	//$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	//$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";
	$message .= __('To activate your account, visit the following address:') . "\r\n\r\n";
	$message .= '<' . home_url("/activate/?key=$hashed&user_login=$user->user_login") . ">\r\n\r\n";

	//$message .= wp_login_url() . "\r\n\r\n";
        $message .= sprintf( __('If you have any problems, please contact us at %s.'), get_option('admin_email') ) . "\r\n\r\n";
	$message .= __('Adios!') . "\r\n\r\n";

	add_user_meta($user_id,'has_to_be_activated',$hashed,true);

	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}

/**
 * mdw_is_user_authenticated function.
 *
 * checked before login (function needs to be called)
 *
 * @access public
 * @param mixed $user
 * @return void
 */
function mdw_is_user_authenticated($user) {
	if (!$user || is_wp_error($user))
		return false;

	if (get_user_meta($user->ID,'has_to_be_activated',true)!=false)
		return false;

	return true;
}
?>