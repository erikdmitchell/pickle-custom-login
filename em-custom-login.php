<?php
/*
Plugin Name: EM Custom Login
Plugin URI:
Description: Enables a completely customizable WordPress login, registration and password form.
Version: 0.1.0
Author: Erik Mitchell
Author URI: http://erikmitchell.net
*/

include_once(plugin_dir_path(__FILE__).'functions.php');
include_once(plugin_dir_path(__FILE__).'error.php');
include_once(plugin_dir_path(__FILE__).'login.php');
include_once(plugin_dir_path(__FILE__).'register.php');
include_once(plugin_dir_path(__FILE__).'password.php');
include_once(plugin_dir_path(__FILE__).'admin.php');
include_once(plugin_dir_path(__FILE__).'user-activation.php');
include_once(plugin_dir_path(__FILE__).'emails.php');


/**
 * EMCustomLogin class.
 *
 * @since 0.1.0
 */
class EMCustomLogin {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts',array($this,'scripts_styles'));
	}

	public function scripts_styles() {
		wp_enqueue_style('emcl-style',plugins_url('/css/style.css',__FILE__));
	}

	/**
	 * plugin_activated function.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function plugin_activated() {
		$pages_arr=array();
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			'login' => array(
				'title' => __('Login','dummy'),
				'content' => '[emcl-login-form]'
			),
			'register' => array(
				'title' => __('Register','dummy'),
				'content' => '[emcl-registration-form]'
			),
			'forgot-password' => array(
				'title' => __('Forgot Password','dummy'),
				'content' => '[emcl-forgot-password-form]'
			),
			'reset-password' => array(
				'title' => __('Reset Password','dummy'),
				'content' => '[emcl-reset-password-form]'
			),
			'activate-account' => array(
				'title' => __('Activate Account','dummy'),
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

}

new EMCustomLogin();

// Create the custom pages at plugin activation //
register_activation_hook( __FILE__,array('EMCustomLogin','plugin_activated'));
?>