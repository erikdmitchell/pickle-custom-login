<?php
/*
  Plugin Name: Pickle Custom Login
  Plugin URI: 
  Description: Enables a completely customizable WordPress login, registration and password form.
  Version: 1.0.0-alpha
  Author: Erik Mitchell
  Author URI: http://erikmitchell.net
  License: GPL-2.0+
  License URI: http://www.gnu.org/licenses/gpl-2.0.txt
  Text Domain: pcl
  Domain Path: /languages
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!defined('PCL_PLUGIN_FILE')) {
	define('PCL_PLUGIN_FILE', __FILE__);
}

final class PickleCustomLogin {

	public $version='1.0.0-alpha';
	
	public $errors='';
	
	public $activation='';
	
	public $admin='';
	
	public $registration='';
	
	public $profile='';
	
	public $email='';
	
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
		include_once(PCL_PATH.'pcl-force-login.php');
		include_once(PCL_PATH.'pcl-login.php');
		include_once(PCL_PATH.'pcl-profile.php');
		include_once(PCL_PATH.'pcl-register.php');
		include_once(PCL_PATH.'pcl-password.php');
		include_once(PCL_PATH.'admin/admin.php');
		include_once(PCL_PATH.'pcl-user-activation.php');
		include_once(PCL_PATH.'pcl-email.php');
		include_once(PCL_PATH.'libraries/recaptchalib.php'); // google recaptcha library
		include_once(PCL_PATH.'updater/updater.php'); // git hub update
		
		new Pickle_Custom_Login();
		new Pickle_Custom_Login_Reset_Password();
		
		if (is_admin()) :
			$this->admin=new Pickle_Custom_Login_Admin();
		endif;
	}

	private function init_hooks() {
		register_activation_hook(PCL_PLUGIN_FILE, array('Pickle_Custom_Login_Install', 'install'));
		add_action('init', array($this, 'init'), 0);
		add_action('admin_init', array($this, 'plugin_updater'));
	}

	public function init() {
		$this->activation=new Pickle_Custom_Login_User_Activation();
		$this->registration=new Pickle_Custom_Login_Registration();
		$this->profile=new Pickle_Custom_Login_Profile();
		$this->errors=new Pickle_Custom_Login_Errors();
		$this->email=new Pickle_Custom_Login_Email();
		$this->pages=get_option('pcl_pages');
	}
	
	public function plugin_updater() {
		if (!is_admin())
			return false;
	
		if (!defined('WP_GITHUB_FORCE_UPDATE'))
			define('WP_GITHUB_FORCE_UPDATE', true);
			
		$username='erikdmitchell';
		$repo_name='pickle-custom-login';
		$folder_name='pickle-custom-login';
	    
	    $config = array(
	        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
	        'proper_folder_name' => $folder_name, // this is the name of the folder your plugin lives in
	        'api_url' => 'https://api.github.com/repos/'.$username.'/'.$repo_name, // the github API url of your github repo
	        'raw_url' => 'https://raw.github.com/'.$username.'/'.$repo_name.'/master', // the github raw url of your github repo
	        'github_url' => 'https://github.com/'.$username.'/'.$repo_name, // the github url of your github repo
	        'zip_url' => 'https://github.com/'.$username.'/'.$repo_name.'/zipball/master', // the zip url of the github repo
	        'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
	        'requires' => '4.0', // which version of WordPress does your plugin require?
	        'tested' => '4.9', // which version of WordPress is your plugin tested up to?
	        'readme' => 'readme.txt', // which file to use as the readme for the version number
	    );
	   
		new WP_GitHub_Updater($config);
	}	

}

function pickle_custom_login() {
	return PickleCustomLogin::instance();
}

// Global for backwards compatibility.
$GLOBALS['pickle_custom_login']=pickle_custom_login();