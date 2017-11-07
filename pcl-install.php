<?php

if (!defined('ABSPATH')) {
	exit;
}

class Pickle_Custom_Login_Install {

	private static $updates = array(
		'1.0.0' => array(
			'pcl_update_100_shortcodes',
		),
	);

	/**
	 * init function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function init() {
		add_action('init', array(__CLASS__, 'check_version'), 5);
	}

	/**
	 * check_version function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function check_version() {
		if (get_option('pcl_version')!==pickle_custom_login()->version)
			self::install();
	}

	/**
	 * install function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function install() {
		if (!is_blog_installed())
			return;

		// Check if we are not already running this routine.
		if ('yes' === get_transient('pcl_installing'))
			return;

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient('pcl_installing', 'yes', MINUTE_IN_SECONDS*10);

		self::create_pages();
		self::update_version();
		self::update();
	
		delete_transient('pcl_installing');
	}

	/**
	 * create_pages function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function create_pages() {
		$pages_arr=array();
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			'activate-account' => array(
				'title' => __('Activate Account', 'pcl'),
				'content' => '[pcl-user-activation]'
			),
			'forgot-password' => array(
				'title' => __('Forgot Password', 'pcl'),
				'content' => '[pcl-forgot-password-form]'
			),
			'login' => array(
				'title' => __('Login', 'pcl'),
				'content' => '[pcl-login-form]'
			),
			'register' => array(
				'title' => __('Register', 'pcl'),
				'content' => '[pcl-registration-form]'
			),
			'reset-password' => array(
				'title' => __('Reset Password', 'pcl'),
				'content' => '[pcl-reset-password-form]'
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
		if (!get_option('pcl_pages'))
			update_option('pcl_pages', $pages_arr);
	}

	/**
	 * update function.
	 * 
	 * @access private
	 * @static
	 * @return void
	 */
	private static function update() {
		$current_version=get_option('pcl_version');

		foreach (self::get_update_callbacks() as $version => $update_callbacks ) :	
			if (version_compare($current_version, $version, '<')) :
				foreach ($update_callbacks as $update_callback) :
					$update_callback();
				endforeach;
			endif;
		endforeach;
	}

	/**
	 * get_update_callbacks function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function get_update_callbacks() {
		return self::$updates;
	}

	/**
	 * update_version function.
	 * 
	 * @access private
	 * @static
	 * @return void
	 */
	private static function update_version() {
		delete_option('pcl_version');
		
		add_option('pcl_version', pickle_custom_login()->version);
	}

}

Pickle_Custom_Login_Install::init();
?>