<?php
/**
 * emcl_get_template_html function.
 *
 * @access public
 * @param bool $template_name (default: false)
 * @param mixed $attributes (default: null)
 * @return void
 */
function emcl_get_template_html($template_name=false,$attributes=null) {
	if (!$attributes )
		$attributes = array();

	if (!$template_name)
		return false;

	ob_start();

	do_action('emcl_before_'.$template_name);

	require('templates/'.$template_name.'.php');

	do_action('emcl_after_'.$template_name);

	$html=ob_get_contents();

	ob_end_clean();

	return $html;
}

/**
 * emcl_add_error_message function.
 *
 * @access public
 * @param string $slug (default: '')
 * @param string $message (default: '')
 * @return void
 */
function emcl_add_error_message($slug='',$message='') {
	global $custom_login_errors;

	$custom_login_errors->register_errors()->add($slug,__($message));
}

/**
 * emcl_has_error_messages function.
 *
 * @access public
 * @return void
 */
function emcl_has_error_messages() {
	global $custom_login_errors;

	$errors=$custom_login_errors->register_errors()->get_error_messages();

	if (empty($errors))
		return false;

	return true;
}

/**
 * emcl_show_error_messages function.
 *
 * @access public
 * @return void
 */
function emcl_show_error_messages() {
	global $custom_login_errors;

	$custom_login_errors->show_error_messages();
}

/**
 * emcl_format_error_message function.
 *
 * @access public
 * @param string $code (default: '')
 * @param bool $message (default: false)
 * @param string $type (default: '')
 * @return void
 */
function emcl_format_error_message($code='',$message=false,$type='') {
	global $custom_login_errors;

	return $custom_login_errors->format_error($code,$message,$type);
}

/**
 * emcl_login_extras function.
 *
 * @access public
 * @param array $args (default: array())
 * @return void
 */
function emcl_login_extras($args=array()) {
	$html=null;
	$default_args=array(
		'loginout' => false,
		'register' => true,
		'password' => true
	);
	$args=array_merge($default_args,$args);
	$wp_loginout=apply_filters('emcl_login_extras_loginout_redirect','');
	$wp_register_before=apply_filters('emcl_login_extras_register_before','');
	$wp_register_after=apply_filters('emcl_login_extras_register_after','');
	$wp_lostpassword_text=apply_filters('emcl_login_extras_lostpassword_text','Lost Password?');

	extract($args);

	$html.='<ul class="custom-login-extras">';
		if ($loginout)
			$html.='<li class="loginout">'.wp_loginout($wp_loginout,false).'</li>';

		if ($register)
			$html.='<li class="wp-register">'.wp_register($wp_register_before,$wp_register_after,false).'</li>';

		if ($password)
			$html.='<li class="lost-password"><a href="'.wp_lostpassword_url().'" title="'.$wp_lostpassword_text.'">'.$wp_lostpassword_text.'</a></li>';
	$html.='</ul>';

	echo $html;
}

/**
 * emcl_is_activation_required function.
 *
 * @access public
 * @return void
 */
function emcl_is_activation_required() {
	$require_activation_key=get_option('emcl-require-activation-key',0);

	if ($require_activation_key)
		return true;

	return false;
}

function emcl_activate_user() {
	global $EMCustomLoginUserActivation;

	return $EMCustomLoginUserActivation->activate_user();
}
?>