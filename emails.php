<?php
function emcl_get_custom_email_message($type=false,$original_message='',$key='',$user_login=false,$user_id=false) {
	$user=false;
	$message='';

	// we need an email type to send
	if (!$type)
		return false;

	// we need somethign to get the user details
	if (!$user_id && !$user_login)
		return false;

	// get user details
	if ($user_id) :
		$user=get_userdata($user_id);
	else :
		$user=get_user_by('login',$user_login);
	endif;

	// one last check
	if (!$user || is_wp_error($user))
		return false;

	switch ($type) :
		case 'password_reset':
			$message=emcl_password_reset_email($original_message,$key,$user->user_login);
			break;
		case 'account_creation_activation_required':
			$message=emcl_account_creation_activation_email($original_message,$key,$user->user_login);
			break;
		case 'account_creation':
			$message=emcl_account_creation_email($original_message,$key,$user->user_login);
			break;
		default:
			break;
	endswitch;

	return $message;
}

/**
 * emcl_password_reset_email function.
 *
 * @access public
 * @param mixed $message
 * @param mixed $key
 * @param mixed $user_login
 * @return void
 */
function emcl_password_reset_email($message,$key,$user_login) {
	$custom_message=$message;

	// check if custom message exists //
	if ($custom_message=get_option('emcl-retrieve-password-email')) :
		$custom_message=stripslashes($custom_message); // clean from db
		$custom_message=emcl_clean_up_placeholders($custom_message,$user_login,$key);
	endif;

	return $custom_message;
}

/**
 * emcl_account_creation_activation_email function.
 *
 * @access public
 * @param mixed $message
 * @param mixed $key
 * @param mixed $user_login
 * @return void
 */
function emcl_account_creation_activation_email($message,$key,$user_login) {
	$custom_message=$message;

	// check if custom message exists //
	if ($custom_message=get_option('emcl-account-activation-email')) :
		$custom_message=stripslashes($custom_message); // clean from db
		$custom_message=emcl_clean_up_placeholders($custom_message,$user_login,$key);
	endif;

	return $custom_message;
}

/**
 * emcl_account_creation_email function.
 *
 * @access public
 * @param mixed $message
 * @param mixed $key
 * @param mixed $user_login
 * @return void
 */
function emcl_account_creation_email($message,$key,$user_login) {
	$custom_message=$message;

	// check if custom message exists //
	if ($custom_message=get_option('emcl-account-creation-email')) :
		$custom_message=stripslashes($custom_message); // clean from db
		$custom_message=emcl_clean_up_placeholders($custom_message,$user_login,$key);
	endif;

	return $custom_message;
}

/**
 * emcl_clean_up_placeholders function.
 *
 * @access public
 * @param string $message (default: '')
 * @param string $user_login (default: '')
 * @param string $key (default: '')
 * @return void
 */
function emcl_clean_up_placeholders($message='',$user_login='',$key='') {
	$placeholders=array(
		'{user_login}' => $user_login,
		'{password_reset_link}' => site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login'),
		'{username}' => $user_login,
		'{activate_account_link}' => home_url("/".emcl_page_slug('activate-account')."/?key=$key&user_login=$user_login"),
		'{admin_email_link}' => get_option('admin_email'),
		'{set_password_link}' => network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login'),
		'{login_url}' => wp_login_url(),
	);

	$message=strtr($message,$placeholders);

	return $message;
}

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
		$message .= '<' . home_url("/".emcl_page_slug('activate-account')."/?key=$hashed&user_login=$user->user_login") . ">\r\n\r\n";
	  $message .= sprintf( __('If you have any problems, please contact us at %s.'), get_option('admin_email') ) . "\r\n\r\n";
		$message .= __('Cheers!') . "\r\n\r\n";

		$message=emcl_get_custom_email_message('account_creation_activation_required',$message,$hashed,$user->user_login);

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

		$message=emcl_get_custom_email_message('account_creation',$message,$key,$user->user_login);
	endif;

	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}
?>