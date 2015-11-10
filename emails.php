<?php

/**
 * emcl_user_activation_email function.
 *
 * email sent when a new user registers
 *
 * @access public
 * @param mixed $user_id
 * @param string $notify (default: '')
 * @return void
 */
function emcl_user_activation_email($user_id,$notify='') {
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

	if (emcl_is_activation_required()) :
		$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= __('To activate your account, visit the following address:') . "\r\n\r\n";
		$message .= '<' . home_url("/activate/?key=$hashed&user_login=$user->user_login") . ">\r\n\r\n";
	  $message .= sprintf( __('If you have any problems, please contact us at %s.'), get_option('admin_email') ) . "\r\n\r\n";
		$message .= __('Cheers!') . "\r\n\r\n";

		add_user_meta($user_id,'has_to_be_activated',$hashed,true);
		// send notice to reg //
	else:
		$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";
		$message .= __('Login here:') . "\r\n\r\n";
		$message .= wp_login_url() . "\r\n\r\n";
	  $message .= sprintf( __('If you have any problems, please contact us at %s.'), get_option('admin_email') ) . "\r\n\r\n";
		$message .= __('Cheers!') . "\r\n\r\n";
	endif;

	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}
?>