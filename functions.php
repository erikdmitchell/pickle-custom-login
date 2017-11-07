<?php

function pcl_scripts_styles() {
	wp_enqueue_style('pcl-frontend-style', plugins_url('css/style.css', __FILE__));	
}
add_action('wp_enqueue_scripts', 'pcl_scripts_styles');

function pcl_get_template_html($template_name=false,$attributes=null) {
	if (!$attributes )
		$attributes = array();

	if (!$template_name)
		return false;

	ob_start();

	do_action('pcl_before_'.$template_name);

	if (file_exists(get_stylesheet_directory().'/pickle-custom-login/'.$template_name.'.php')) :
		include(get_stylesheet_directory().'/pickle-custom-login/'.$template_name.'.php');
	elseif (file_exists(get_template_directory().'/pickle-custom-login/'.$template_name.'.php')) :
		include(get_template_directory().'/pickle-custom-login/'.$template_name.'.php');
	elseif (file_exists(get_stylesheet_directory().'/pickle-custom-login/templates/'.$template_name.'.php')) :
		include(get_stylesheet_directory().'/pickle-custom-login/templates/'.$template_name.'.php');
	elseif (file_exists(get_template_directory().'/pickle-custom-login/templates/'.$template_name.'.php')) :
		include(get_template_directory().'/pickle-custom-login/templates/'.$template_name.'.php');
	else :
		include('templates/'.$template_name.'.php');
	endif;

	do_action('pcl_after_'.$template_name);

	$html=ob_get_contents();

	ob_end_clean();

	return $html;
}

/**
 * pcl_add_error_message function.
 * 
 * @access public
 * @param string $slug (default: '')
 * @param string $message (default: '')
 * @return void
 */
function pcl_add_error_message($slug='',$message='') {
	pickle_custom_login()->errors->register_errors()->add($slug,__($message));
}

/**
 * pcl_has_error_messages function.
 * 
 * @access public
 * @return void
 */
function pcl_has_error_messages() {
	$errors=pickle_custom_login()->errors->register_errors()->get_error_messages();

	if (empty($errors))
		return false;

	return true;
}

/**
 * pcl_show_error_messages function.
 * 
 * @access public
 * @return void
 */
function pcl_show_error_messages() {
	pickle_custom_login()->errors->show_error_messages();
}

/**
 * pcl_format_error_message function.
 * 
 * @access public
 * @param string $code (default: '')
 * @param bool $message (default: false)
 * @param string $type (default: '')
 * @return void
 */
function pcl_format_error_message($code='', $message=false, $type='') {
	pickle_custom_login()->errors->format_error($code, $message, $type);
}

function pcl_login_extras($args=array()) {
	$html=null;
	$default_args=array(
		'loginout' => false,
		'register' => true,
		'password' => true
	);
	$args=array_merge($default_args,$args);
	$wp_loginout=apply_filters('pcl_login_extras_loginout_redirect','');
	$wp_register_before=apply_filters('pcl_login_extras_register_before','');
	$wp_register_after=apply_filters('pcl_login_extras_register_after','');
	$wp_lostpassword_text=apply_filters('pcl_login_extras_lostpassword_text','Lost Password?');

	extract($args);

	$html.='<ul class="custom-login-extras">';
		if ($loginout)
			$html.='<li class="loginout">'.wp_loginout($wp_loginout,false).'</li>';

		if ($register)
			$html.='<li class="wp-register">'.wp_register($wp_register_before,$wp_register_after,false).'</li>';

		if ($password)
			$html.='<li class="lost-password"><a href="'.wp_lostpassword_url().'" title="'.$wp_lostpassword_text.'">'.__($wp_lostpassword_text,'pcl').'</a></li>';
	$html.='</ul>';

	echo $html;
}

function pcl_is_activation_required() {
	$require_activation_key=get_option('pcl-require-activation-key',0);

	if ($require_activation_key)
		return true;

	return false;
}

function pcl_is_user_authenticated($user_id=0) {
	if (!$user_id)
		return false;

	if (get_user_meta($user_id,'has_to_be_activated',true))
		return false;

	return true;
}

function pcl_activate_user() {
	global $EMCustomLoginUserActivation;

	return $EMCustomLoginUserActivation->activate_user();
}

function pcl_logged_in_links($args=array()) {
	$html=null;
	$default_args=array(
		'edit_profile' => true,
		'logout' => true,
	);
	$args=array_merge($default_args,$args);

	extract($args);

	$html.='<ul class="loggedin-extras">';
		if ($edit_profile)
			$html.='<li class="edit-profile"><a href="'.get_edit_user_link().'">'.__('Edit Profile','pcl').'</a></li>';

		if ($logout)
			$html.='<li class="logout"><a href="'.wp_logout_url().'">'.__('Log Out','pcl').'</a></li>';
	$html.='</ul>';

	echo $html;
}

function pcl_page_slug($page_type='') {
	global $EMCustomLoginAdmin;

	$pages=get_option('pcl-pages');

	if (isset($pages[$page_type])) :
		$post=get_post($pages[$page_type]);

		if (isset($post->post_name)) :
			$slug=$post->post_name;
		else :
			$slug=false;
		endif;
	else :
		$slug=false;
	endif;

	return $slug;
}

function pcl_remove_admin_bar() {
	$hide_admin_bar=get_option('pcl-hide-admin-bar',false);

	if (!current_user_can('administrator') && !is_admin() && $hide_admin_bar) :
  	show_admin_bar(false);
	endif;
}
add_action('after_setup_theme','pcl_remove_admin_bar');

function pcl_recaptcha_scripts_styles() {
	if (!is_page(pcl_page_slug('register')))
		return false;

	wp_enqueue_script('google-recaptcha-api-script','https://www.google.com/recaptcha/api.js');
}
add_action('wp_enqueue_scripts','pcl_recaptcha_scripts_styles');
?>