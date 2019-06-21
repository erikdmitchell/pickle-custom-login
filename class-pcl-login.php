<?php
/**
 * Main Login class
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

/**
 * PCL_Login class.
 */
class PCL_Login {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'login_member' ) );
        add_action( 'init', array( $this, 'redirect_login_page' ) );
        add_action( 'pcl_before_login-form', 'pcl_show_error_messages' );
        add_action( 'wp_login_failed', array( $this, 'login_failed' ) );
        add_action( 'wp_logout', array( $this, 'logout_page' ) );

        add_filter( 'authenticate', array( $this, 'verify_username_password' ), 1, 3 );

        add_shortcode( 'pcl-login-form', array( $this, 'login_form' ) );
    }

    /**
     * Login form shortcode.
     *
     * @access public
     * @return html
     */
    public function login_form() {
        if ( is_user_logged_in() ) {
            return pcl_get_template_html( 'logged-in' );
        }

        if ( isset( $_GET['checkemail'] ) && 'confirm' == $_GET['checkemail'] ) {
            echo pcl_format_error_message( '', 'An email has been set to the address provided with instructions on how to reset your password.', 'success' );
        }

        if ( isset( $_GET['password'] ) && 'changed' == $_GET['password'] ) {
            echo pcl_format_error_message( '', 'Your password has been changed. Please login.', 'success' );
        }

        return pcl_get_template_html( 'login-form' );
    }

    /**
     * Login function.
     *
     * @access public
     * @return void
     */
    public function login_member() {
        $redirect = get_option( 'pcl-login-redirect', home_url() );

        if ( isset( $_POST['custom_user_login'] ) && wp_verify_nonce( sanitize_key( $_POST['custom_login_nonce'] ), 'custom-login-nonce' ) ) :
            // this returns the user ID and other info from the user name.
            $user = get_user_by( 'login', isset( $_POST['custom_user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_user_login'] ) ) : '' );

            // if the user name doesn't exist.
            if ( ! $user ) {
                pcl_add_error_message( 'empty_username', 'Invalid username' );
            }

            // if no password was entered.
            if ( ! isset( $_POST['custom_user_pass'] ) || '' == $_POST['custom_user_pass'] ) {
                pcl_add_error_message( 'empty_password', 'Please enter a password' );
            }

            // check the user's login with their password.
            if ( ! isset( $user->user_pass ) || ! wp_check_password( wp_unslash( $_POST['custom_user_pass'] ), $user->user_pass, $user->ID ) ) {
                pcl_add_error_message( 'empty_password', 'Incorrect password' );
            }

            // check if admin activation is required and they have been approved.
            if ( isset( $user->ID ) && pcl_require_admin_activation() && ! pcl_is_user_approved( $user->ID ) ) {
                pcl_add_error_message( 'not_approved', 'An admin must approve your account before logging in.' );
            }

            // check if activation is required and if so, user is active.
            if ( isset( $user->ID ) && pcl_is_activation_required() && ! pcl_is_user_authenticated( $user->ID ) ) {
                pcl_add_error_message( 'not_activated', 'You must activate your account before logging in.' );
            }

            // only log the user in if there are no errors.
            if ( ! pcl_has_error_messages() ) {
                // remember me/set auth cookie //
                if ( isset( $_POST['rememberme'] ) && 1 == $_POST['rememberme'] ) :
                    wp_set_auth_cookie( $user->ID, true );
                else :
                    wp_set_auth_cookie( $user->ID, false );
                endif;

                wp_set_current_user( $user->ID, $_POST['custom_user_login'] );

                do_action( 'wp_login', $_POST['custom_user_login'] );

                if ( current_user_can( 'administrator' ) ) {
                    $redirect = admin_url();
                }

                wp_safe_redirect( $redirect );
                exit;
            }
        endif;
    }

    /**
     * Redirect login page.
     *
     * @access public
     * @return void
     */
    public function redirect_login_page() {
        $slug = pcl_page_slug( 'login' );
        $page_viewed = esc_attr( isset( $_SERVER['REQUEST_URI'] ) ? basename( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' );

        if ( $slug ) :
            $login_page = home_url( $slug );

            if ( 'wp-login.php' == $page_viewed && 'GET' == $_SERVER['REQUEST_METHOD'] ) :
                wp_safe_redirect( $login_page );
                exit;
            endif;
        endif;
    }

    /**
     * Failed login redirect.
     *
     * @access public
     * @return void
     */
    public function login_failed() {
        $slug = pcl_page_slug( 'login' );

        if ( $slug ) :
            $login_page = home_url( $slug );

            wp_safe_redirect( $login_page . '?login=failed' );
            exit;
        endif;
    }

    /**
     * Logout page redirect.
     *
     * @access public
     * @return void
     */
    public function logout_page() {
        $redirect = get_option( 'pcl-logout-redirect', home_url() );

        wp_safe_redirect( $redirect . '?login=false' );
        exit;
    }

    /**
     * Verify username and password.
     *
     * @access public
     * @param mixed $user
     * @param mixed $username
     * @param mixed $password
     * @return void
     */
    public function verify_username_password( $user, $username, $password ) {
        $slug = pcl_page_slug( 'login' );

        if ( $slug ) :
            $login_page = home_url( $slug );

            if ( '' == $username || '' == $password ) :
                wp_safe_redirect( $login_page . '?login=empty' );
                exit;
            endif;
        endif;
    }

}
