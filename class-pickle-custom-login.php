<?php
/**
 * Main Pickle Custom Login class
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

/**
 * Final Pickle_Custom_Login class.
 *
 * @final
 */
final class Pickle_Custom_Login {

    /**
     * version
     *
     * (default value: '1.0.0-beta.2')
     *
     * @var string
     * @access public
     */
    public $version = '1.0.0-beta.2';

    /**
     * errors
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $errors = '';

    /**
     * activation
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $activation = '';

    /**
     * admin
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $admin = '';

    /**
     * registration
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $registration = '';

    /**
     * profile
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $profile = '';

    /**
     * email
     *
     * (default value: '')
     *
     * @var string
     * @access public
     */
    public $email = '';

    /**
     * pages
     *
     * (default value: array())
     *
     * @var array
     * @access public
     */
    public $pages = array();

    /**
     * _instance
     *
     * (default value: null)
     *
     * @var mixed
     * @access protected
     * @static
     */
    protected static $_instance = null;

    /**
     * Instance.
     *
     * @access public
     * @static
     * @return instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     *
     * @access private
     * @return void
     */
    private function define_constants() {
        $this->define( 'PCL_VERSION', $this->version );
        $this->define( 'PCL_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'PCL_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Define function.
     *
     * @access private
     * @param mixed $name
     * @param mixed $value
     * @return void
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Includes.
     *
     * @access public
     * @return void
     */
    public function includes() {
        include_once( PCL_PATH . 'class-pcl-install.php' );
        include_once( PCL_PATH . 'class-pcl-uninstall.php' );
        include_once( PCL_PATH . 'pcl-update-functions.php' );
        include_once( PCL_PATH . 'pcl-deprecated-functions.php' );

        include_once( PCL_PATH . 'functions.php' );
        include_once( PCL_PATH . 'pcl-errors.php' );
        include_once( PCL_PATH . 'pcl-force-login.php' );
        include_once( PCL_PATH . 'class-pcl-login.php' );
        include_once( PCL_PATH . 'pcl-profile.php' );
        include_once( PCL_PATH . 'pcl-register.php' );
        include_once( PCL_PATH . 'pcl-password.php' );
        include_once( PCL_PATH . 'admin/admin.php' );
        include_once( PCL_PATH . 'pcl-user-activation.php' );
        include_once( PCL_PATH . 'pcl-email.php' );
        include_once( PCL_PATH . 'libraries/recaptchalib.php' ); // google recaptcha library

        new PCL_Login();
        new Pickle_Custom_Login_Reset_Password();

        if ( is_admin() ) :
            $this->admin = new Pickle_Custom_Login_Admin();
        endif;
    }

    /**
     * Init hooks.
     *
     * @access private
     * @return void
     */
    private function init_hooks() {
        register_activation_hook( PCL_PLUGIN_FILE, array( 'Pickle_Custom_Login_Install', 'install' ) );
        register_deactivation_hook( PCL_PLUGIN_FILE, array( 'PCL_Uninstall', 'uninstall' ) );

        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * Init.
     *
     * @access public
     * @return void
     */
    public function init() {
        $this->activation = new Pickle_Custom_Login_User_Activation();
        $this->registration = new Pickle_Custom_Login_Registration();
        $this->profile = new Pickle_Custom_Login_Profile();
        $this->errors = new Pickle_Custom_Login_Errors();
        $this->email = new Pickle_Custom_Login_Email();
        $this->pages = get_option( 'pcl_pages' );
    }

}

/**
 * pickle_custom_login function.
 *
 * @access public
 * @return class instance
 */
function pickle_custom_login() {
    return Pickle_Custom_Login::instance();
}

// Global for backwards compatibility.
$GLOBALS['pickle_custom_login'] = pickle_custom_login();
