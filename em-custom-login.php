<?php
/*
Plugin Name: EM Custom Login
Plugin URI:
Description: Enables a completely customizable WordPress login, registration and password form.
Version: 0.1.0
Author: Erik Mitchell
Author URI: http://erikmitchell.net
*/

include_once(plugin_dir_path(__FILE__).'error.php');
include_once(plugin_dir_path(__FILE__).'functions.php');
include_once(plugin_dir_path(__FILE__).'login.php');

class EMCustomLogin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

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
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			'login' => array(
				'title' => __('Login','dummy'),
				'content' => '[emcl-login-form]'
			),
		);

		foreach ($page_definitions as $slug => $page) :
			// Check that the page doesn't exist already
			$query=new WP_Query('pagename='.$slug);

			if (!$query->have_posts()) :
				// Add the page using the data from the array above
				wp_insert_post(
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
			endif;
		endforeach;
	}

}

// Create the custom pages at plugin activation //
register_activation_hook( __FILE__,array('EMCustomLogin','plugin_activated'));
?>