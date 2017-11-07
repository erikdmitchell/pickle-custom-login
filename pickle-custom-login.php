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

if (!defined('PCL_PLUGIN_FILE')) {
	define('PCL_PLUGIN_FILE', __FILE__);
}

final class PickleCustomLogin {

	public $version='1.0.0';

	protected static $_instance=null;

	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance=new self();
		}
		
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	private function define_constants() {
		$this->define('PCL_VERSION', $this->version);
		$this->define('PCL_PATH', plugin_dir_path(__FILE__));
		$this->define('PCL_URL', plugin_dir_url(__FILE__));
		
	}

	private function define($name, $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}

	public function includes() {
		include_once(PCL_PATH.'pcl-install.php');
		include_once(PCL_PATH.'functions.php');
		include_once(PCL_PATH.'error.php');
		include_once(PCL_PATH.'login.php');
		include_once(PCL_PATH.'register.php');
		include_once(PCL_PATH.'password.php');
		include_once(PCL_PATH.'admin.php');
		include_once(PCL_PATH.'user-activation.php');
		include_once(PCL_PATH.'emails.php');
		include_once(PCL_PATH.'recaptchalib.php'); // google recaptcha library
	}

	private function init_hooks() {
		register_activation_hook(PCL_PLUGIN_FILE, array(Pickle_Custom_Login_Install, 'install'));
		add_action('init', array($this, 'init'), 0);
	}

	public function init() {

	}

}

function pickle_custom_login() {
	return PickleCustomLogin::instance();
}

// Global for backwards compatibility.
$GLOBALS['pickle_custom_login']=pickle_custom_login();
?>