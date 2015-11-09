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
?>