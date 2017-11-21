<?php

/**
 * pcl_scripts_styles function.
 * 
 * @access public
 * @return void
 */
function pcl_scripts_styles() {
	wp_enqueue_style('pcl-frontend-style', PCL_URL.'css/style.css', '', PCL_VERSION);	
}
add_action('wp_enqueue_scripts', 'pcl_scripts_styles');

/**
 * pcl_get_template_html function.
 * 
 * @access public
 * @param bool $template_name (default: false)
 * @param mixed $attributes (default: null)
 * @return void
 */
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
	pickle_custom_login()->errors->register_errors()->add($slug, __($message));
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
	return pickle_custom_login()->errors->format_error($code, $message, $type);
}

/**
 * pcl_login_extras function.
 * 
 * @access public
 * @param array $args (default: array())
 * @return void
 */
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

/**
 * pcl_is_activation_required function.
 * 
 * @access public
 * @return void
 */
function pcl_is_activation_required() {
	$require_activation_key=get_option('pcl-require-activation-key', 0);

	if ($require_activation_key)
		return true;

	return false;
}

/**
 * pcl_is_user_authenticated function.
 * 
 * @access public
 * @param int $user_id (default: 0)
 * @return void
 */
function pcl_is_user_authenticated($user_id=0) {
	if (!$user_id)
		return false;

	if (get_user_meta($user_id, 'has_to_be_activated', true))
		return false;

	return true;
}

/**
 * pcl_activate_user function.
 * 
 * @access public
 * @return void
 */
function pcl_activate_user() {
	return pickle_custom_login()->activation->activate_user();
}

/**
 * pcl_logged_in_links function.
 * 
 * @access public
 * @param array $args (default: array())
 * @return void
 */
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
			$html.='<li class="edit-profile"><a href="'.get_edit_user_link().'">'.__('Edit Profile', 'pcl').'</a></li>';

		if ($logout)
			$html.='<li class="logout"><a href="'.wp_logout_url().'">'.__('Log Out', 'pcl').'</a></li>';
	$html.='</ul>';

	echo $html;
}

/**
 * pcl_page_slug function.
 * 
 * @access public
 * @param string $page_type (default: '')
 * @return void
 */
function pcl_page_slug($page_type='') {
	if (isset(pickle_custom_login()->pages[$page_type])) :
		$post=get_post(pickle_custom_login()->pages[$page_type]);

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

/**
 * pcl_remove_admin_bar function.
 * 
 * @access public
 * @return void
 */
function pcl_remove_admin_bar() {
	$hide_admin_bar=get_option('pcl-hide-admin-bar',false);

	if (!current_user_can('administrator') && !is_admin() && $hide_admin_bar) :
  	show_admin_bar(false);
	endif;
}
add_action('after_setup_theme','pcl_remove_admin_bar');

/**
 * pcl_recaptcha_scripts_styles function.
 * 
 * @access public
 * @return void
 */
function pcl_recaptcha_scripts_styles() {
	if (!is_page(pcl_page_slug('register')))
		return false;

	wp_enqueue_script('google-recaptcha-api-script','https://www.google.com/recaptcha/api.js');
}
add_action('wp_enqueue_scripts','pcl_recaptcha_scripts_styles');

/**
 * pcl_hide_admin_bar function.
 * 
 * @access public
 * @return void
 */
function pcl_hide_admin_bar() {
	return get_option('pcl-hide-admin-bar', false);
}

/**
 * pcl_enable_recaptcha function.
 * 
 * @access public
 * @return void
 */
function pcl_enable_recaptcha() {
	return get_option('pcl-enable-recaptcha', false);
}

/**
 * pcl_require_activation_key function.
 * 
 * @access public
 * @return void
 */
function pcl_require_activation_key() {
	return get_option('pcl-require-activation-key', 0);
}

/**
 * pcl_login_redirect_url function.
 * 
 * @access public
 * @return void
 */
function pcl_login_redirect_url() {
	return get_option('pcl-login-redirect', home_url());
}

/**
 * pcl_register_redirect_url function.
 * 
 * @access public
 * @return void
 */
function pcl_register_redirect_url() {
	return get_option('pcl-register-redirect', home_url());
}

/**
 * pcl_logout_redirect_url function.
 * 
 * @access public
 * @return void
 */
function pcl_logout_redirect_url() {
	return get_option('pcl-logout-redirect', home_url());
}

/**
 * pcl_force_login function.
 * 
 * @access public
 * @return void
 */
function pcl_force_login() {
    return get_option('pcl-force-login', 0);
}

/**
 * pcl_login_url function.
 * 
 * @access public
 * @return void
 */
function pcl_login_url() {
	return home_url(pcl_page_slug('login'));
}

/**
 * pcl_wp_login_url function.
 * 
 * @access public
 * @param mixed $login_url
 * @param mixed $redirect
 * @param mixed $force_reauth
 * @return void
 */
function pcl_wp_login_url($login_url, $redirect, $force_reauth) {
    return home_url(pcl_page_slug('login'));
}
add_filter('login_url', 'pcl_wp_login_url', 10, 3);

/**
 * pcl_force_login_whitelist function.
 * 
 * @access public
 * @param mixed $urls
 * @return void
 */
function pcl_force_login_whitelist($urls) {
    foreach (pickle_custom_login()->pages as $slug => $page_id) :
        $urls[]=get_permalink($page_id);    
    endforeach;
    
    if (pcl_logout_page_url() != home_url())
        $urls[]=pcl_logout_page_url();

    return $urls;
}
add_filter('pcl_force_login_whitelist', 'pcl_force_login_whitelist');

/**
 * pcl_logout_page_url function.
 * 
 * @access public
 * @return void
 */
function pcl_logout_page_url() {
	return get_option('pcl-logout-redirect', home_url());   
}

/**
 * pcl_get_edit_user_link function.
 * 
 * @access public
 * @param mixed $link
 * @param mixed $user_id
 * @return void
 */
function pcl_get_edit_user_link($link, $user_id) {
    $pcl_link=pcl_page_slug('profile');
    
    if ($pcl_link!=$link)
        return $pcl_link;
        
    return $link;
}
add_filter('get_edit_user_link', 'pcl_get_edit_user_link', 10, 2);

/**
 * pcl_updated_profile_message function.
 * 
 * @access public
 * @return void
 */
function pcl_updated_profile_message() {
    if (isset($_GET['updated']) && $_GET['updated'] == 'true' && !pickle_custom_login()->profile->has_errors()) : 
        echo '<div id="message" class="updated published"><p>Your profile has been updated.</p></div>';
    endif;
    
    if (pickle_custom_login()->profile->has_errors()) :
       pickle_custom_login()->profile->display_errors();    
    endif;
}