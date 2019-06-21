<?php
/**
 * User activation class
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

/**
 * Pickle_Custom_Login_User_Activation class.
 */
class Pickle_Custom_Login_User_Activation {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'pcl-user-activation', array( $this, 'user_activation_form' ) );
    }

    /**
     * User activation form template gen.
     *
     * @access public
     * @return html
     */
    public function user_activation_form() {
        if ( is_user_logged_in() ) {
            return pcl_get_template_html( 'logged-in' );
        }

        return pcl_get_template_html( 'user-activation-form' );
    }

    /**
     * Activate user.
     *
     * @access public
     * @return bool
     */
    public function activate_user() {
        global $wpdb;

        if ( ! isset( $_GET['user_login'] ) || ! isset( $_GET['key'] ) ) {
            return false;
        }

        $user_id = $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_login='{$_GET['user_login']}' AND  user_activation_key='{$_GET['key']}'" );

        if ( $user_id ) :
            $code = get_user_meta( $user_id, 'has_to_be_activated', true );
            if ( $code == $_GET['key'] ) :
                delete_user_meta( $user_id, 'has_to_be_activated' );
                return true;
            endif;
        endif;

        return false;
    }

    /**
     * IChecks if user is authenticated.
     *
     * @access public
     * @param mixed $user.
     * @return bool
     */
    public function is_user_authenticated( $user ) {
        if ( ! $user || is_wp_error( $user ) ) {
            return false;
        }

        if ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
            return false;
        }

        return true;
    }

}

