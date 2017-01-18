<?php
/*
 * Plugin Name: EM Custom Login
 * Plugin URI: https://wordpress.org/plugins/em-custom-login/
 * Description: Enables a completely customizable WordPress login, registration and password form.
 * Version: 0.1.5
 * Author: Erik Mitchell
 * Author URI: http://erikmitchell.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: emcl
 * Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('EMCL_PATH', plugin_dir_path(__FILE__));

include_once(plugin_dir_path(__FILE__).'functions.php');
include_once(plugin_dir_path(__FILE__).'error.php');
include_once(plugin_dir_path(__FILE__).'login.php');
include_once(plugin_dir_path(__FILE__).'register.php');
include_once(plugin_dir_path(__FILE__).'password.php');
include_once(plugin_dir_path(__FILE__).'admin.php');
include_once(plugin_dir_path(__FILE__).'user-activation.php');
include_once(plugin_dir_path(__FILE__).'emails.php');
include_once(plugin_dir_path(__FILE__).'recaptchalib.php'); // google recaptcha library

/**
 * emcl_plugin_activated function.
 * 
 * @access public
 * @return void
 */
function emcl_plugin_activated() {
	$pages_arr=array();
	// Information needed for creating the plugin's pages
	$page_definitions = array(
		'login' => array(
			'title' => __('Login','emcl'),
			'content' => '[emcl-login-form]'
		),
		'register' => array(
			'title' => __('Register','emcl'),
			'content' => '[emcl-registration-form]'
		),
		'forgot-password' => array(
			'title' => __('Forgot Password','emcl'),
			'content' => '[emcl-forgot-password-form]'
		),
		'reset-password' => array(
			'title' => __('Reset Password','emcl'),
			'content' => '[emcl-reset-password-form]'
		),
		'activate-account' => array(
			'title' => __('Activate Account','emcl'),
			'content' => '[emcl-user-activation]'
		),
	);

	foreach ($page_definitions as $slug => $page) :
		// Check that the page doesn't exist already
		$query=new WP_Query('pagename='.$slug);

		if (!$query->have_posts()) :
			// Add the page using the data from the array above
			$post_id=wp_insert_post(
				array(
					'post_content'   => $page['content'],
					'post_name'      => $slug,
					'post_title'     => $page['title'],
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'ping_status'    => 'closed',
					'comment_status' => 'closed',
				)
			);
		else :
			$post_id=$query->queried_object_id;
		endif;

		$pages_arr[$slug]=$post_id;
	endforeach;

	// if this plugin existed before, keep their settings //
	if (!get_option('emcl-pages'))
		update_option('emcl-pages',$pages_arr);
}
register_activation_hook( __FILE__, 'emcl_plugin_activated');
?>
