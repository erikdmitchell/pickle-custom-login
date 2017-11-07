<?php
/*
 * Plugin Name: Pickle Custom Login
 * Plugin URI: https://wordpress.org/plugins/em-custom-login/
 * Description: Enables a completely customizable WordPress login, registration and password form.
 * Version: 1.0.0
 * Author: Erik Mitchell
 * Author URI: http://erikmitchell.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pcl
 * Domain Path: /languages
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!defined('PCL_PLUGIN_FILE')) {
	define('PCL_PLUGIN_FILE', __FILE__);
}

final class PickleCustomLogin {

	public $version='1.0.0';
	
	public $errors='';
	
	public $activation='';
	
	public $admin='';
	
	public $pages=array();

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
		include_once(PCL_PATH.'pcl-update-functions.php');
		include_once(PCL_PATH.'pcl-deprecated-functions.php');
		
		include_once(PCL_PATH.'functions.php');
		include_once(PCL_PATH.'pcl-errors.php');
		include_once(PCL_PATH.'pcl-login.php');
		include_once(PCL_PATH.'pcl-register.php');
		include_once(PCL_PATH.'pcl-password.php');
		include_once(PCL_PATH.'admin/admin.php');
		include_once(PCL_PATH.'pcl-user-activation.php');
		include_once(PCL_PATH.'pcl-email-functions.php');
		include_once(PCL_PATH.'libraries/recaptchalib.php'); // google recaptcha library
		
		new Pickle_Custom_Login();
		new Pickle_Custom_Login_Registration();
		new Pickle_Custom_Login_Reset_Password();
		
		if (is_admin()) :
			$this->admin=new PickleCustomLoginAdmin();
		endif;
	}

	private function init_hooks() {
		register_activation_hook(PCL_PLUGIN_FILE, array('Pickle_Custom_Login_Install', 'install'));
		add_action('init', array($this, 'init'), 0);
	}

	public function init() {
		$this->activation=new Pickle_Custom_Login_User_Activation();
		$this->errors=new Pickle_Custom_Login_Errors();
		$this->pages=get_option('pcl_pages');
	}

}

function pickle_custom_login() {
	return PickleCustomLogin::instance();
}

// Global for backwards compatibility.
$GLOBALS['pickle_custom_login']=pickle_custom_login();
?>