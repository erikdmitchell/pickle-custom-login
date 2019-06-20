<?php
/*
 * Plugin Name: Pickle Custom Login
 * Plugin URI:
 * Description: Enables a completely customizable WordPress login, registration and password form.
 * Version: 1.1.1
 * Author: Erik Mitchell
 * Author URI: http://erikmitchell.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pcl
 * Domain Path: /languages
 *
 * @package PickleCustomLogin
 *
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! defined( 'PCL_PLUGIN_FILE' ) ) {
    define( 'PCL_PLUGIN_FILE', __FILE__ );
}

// Include the main Pickle_Custom_Login class.
if ( ! class_exists( 'Pickle_Custom_Login' ) ) {
    include_once dirname( __FILE__ ) . '/class-pickle-custom-login.php';
}
