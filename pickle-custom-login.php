<?php
/*
 * Plugin Name: Pickle Custom Login
 * Plugin URI: https://wordpress.org/plugins/em-custom-login/
 * Description: Enables a completely customizable WordPress login, registration and password form.
 * Version: 0.1.5
 * Author: Erik Mitchell
 * Author URI: http://erikmitchell.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pcl
 * Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('PCL_PATH', plugin_dir_path(__FILE__));

include_once(PCL_PATH.'functions.php');
include_once(PCL_PATH.'error.php');
include_once(PCL_PATH.'login.php');
include_once(PCL_PATH.'register.php');
include_once(PCL_PATH.'password.php');
include_once(PCL_PATH.'admin.php');
include_once(PCL_PATH.'user-activation.php');
include_once(PCL_PATH.'emails.php');
include_once(PCL_PATH.'recaptchalib.php'); // google recaptcha library

/**
 * pcl_plugin_activated function.
 * 
 * @access public
 * @return void
 */
function pcl_plugin_activated() {
	$pages_arr=array();
	// Information needed for creating the plugin's pages
	$page_definitions = array(
		'login' => array(
			'title' => __('Login','pcl'),
			'content' => '[pcl-login-form]'
		),
		'register' => array(
			'title' => __('Register','pcl'),
			'content' => '[pcl-registration-form]'
		),
		'forgot-password' => array(
			'title' => __('Forgot Password','pcl'),
			'content' => '[pcl-forgot-password-form]'
		),
		'reset-password' => array(
			'title' => __('Reset Password','pcl'),
			'content' => '[pcl-reset-password-form]'
		),
		'activate-account' => array(
			'title' => __('Activate Account','pcl'),
			'content' => '[pcl-user-activation]'
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
	if (!get_option('pcl-pages'))
		update_option('pcl-pages', $pages_arr);
}
register_activation_hook( __FILE__, 'pcl_plugin_activated');
?>
