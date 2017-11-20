<?php

function pcl_force_login_redirect() {
	// Exceptions for AJAX, Cron, or WP-CLI requests
	if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) )
    	return;

	if (isset($_GET['redirect_to']))
		return;

	// Redirect unauthorized visitors
	if (!is_user_logged_in() && pcl_force_Login()) :
    
    	// Get URL
		$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];

		// Apply filters
		$bypass=apply_filters('pcl_force_login_bypass', false);
		$whitelist=apply_filters('pcl_force_login_whitelist', array());
		$redirect_url=apply_filters('pcl_force_login_redirect', $url);

		// Redirect visitors
		if (preg_replace('/\?.*/', '', $url) != preg_replace('/\?.*/', '', wp_login_url()) && !in_array($url, $whitelist) && !$bypass) :
			wp_safe_redirect(wp_login_url($redirect_url), 302); 
			exit();
    	endif;
	endif;

}
add_action('template_redirect', 'pcl_force_login_redirect');