<?php

class Pickle_Custom_Login_Reset_Password {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'pcl_before_forgot-password', 'pcl_show_error_messages' );
        add_action( 'login_form_lostpassword', array( $this, 'process_reset_password_form' ) );
        add_action( 'login_form_lostpassword', array( $this, 'redirect_to_password_reset' ) );
        add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
        add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );
        add_action( 'login_form_rp', array( $this, 'reset_password' ) );
        add_action( 'login_form_resetpass', array( $this, 'reset_password' ) );

        add_filter( 'retrieve_password_message', array( $this, 'replace_retrieve_password_message' ), 10, 4 );

        add_shortcode( 'pcl-forgot-password-form', array( $this, 'forgot_password_form' ) );
        add_shortcode( 'pcl-reset-password-form', array( $this, 'password_reset_form' ) );
    }

    /**
     * forgot_password_form function.
     *
     * @access public
     * @return void
     */
    public function forgot_password_form() {
        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'pcl' );
        }

        if ( isset( $_GET['errors'] ) ) {
            $this->process_error_codes( $_GET['errors'] );
        }

        return pcl_get_template_html( 'forgot-password' );
    }

    /**
     * password_reset_form function.
     *
     * @access public
     * @return void
     */
    public function password_reset_form() {
        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'pcl' );
        }

        if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) :
            if ( isset( $_REQUEST['error'] ) ) {
                $this->process_error_codes( $_REQUEST['error'] );
            }

            return pcl_get_template_html( 'reset-password' );
        else :
            return __( 'Invalid password reset link.', 'pcl' );
        endif;

        if ( isset( $_GET['login'] ) ) :
            switch ( $_GET['login'] ) :
                case 'expiredkey':
                    return pcl_format_error_message( 'Error', 'Your key has expired. Try again.', 'error' );
                    break;
                case 'invalidkey':
                    return pcl_format_error_message( 'Error', 'You need to have a valid key. Try again.', 'error' );
                    break;
          endswitch;
        endif;
    }

    /**
     * redirect_to_password_reset function.
     *
     * Redirects the user to the custom "Forgot your password?" page instead of wp-login.php?action=lostpassword.
     *
     * @access public
     * @return void
     */
    public function redirect_to_password_reset() {
        $slug = pcl_page_slug( 'forgot-password' );

        if ( 'GET' == $_SERVER['REQUEST_METHOD'] && $slug ) :
            if ( is_user_logged_in() ) :
                $this->redirect_logged_in_user();
                exit;
            endif;

            wp_redirect( home_url( $slug ) );
            exit;
        endif;
    }

    /**
     * process_reset_password_form function.
     *
     * @access public
     * @return void
     */
    public function process_reset_password_form() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) :
            $_errors = array();
            $errors = retrieve_password();

            // check recaptcha and return if failed.
            if ( ! $this->check_recaptcha( $_POST['g-recaptcha-response'] ) ) :
                echo 'sans recaptcha';

                // Errors found.
                if ( $slug = pcl_page_slug( 'forgot-password' ) ) :
                    $redirect_url = home_url( 'forgot-password' );
                else :
                    $redirect_url = wp_lostpassword_url();
                endif;

                $redirect_url = add_query_arg( 'errors', 'recaptcha error', $redirect_url );
            elseif ( is_wp_error( $errors ) ) :
                // Errors found.
                if ( $slug = pcl_page_slug( 'forgot-password' ) ) :
                    $redirect_url = home_url( 'forgot-password' );
                else :
                    $redirect_url = wp_lostpassword_url();
                endif;

                $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
            else :
                // Email sent.
                if ( $slug = pcl_page_slug( 'login' ) ) :
                    $redirect_url = home_url( $slug );
                else :
                    $redirect_url = wp_login_url();
                endif;

                $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
            endif;

            wp_redirect( $redirect_url );
            exit;
        endif;
    }

    protected function check_recaptcha( $recaptcha_response = '' ) {
        $secret = get_option( 'pcl-recaptcha-secret-key', '' ); // secret key
        $response = null; // empty response
        $reCaptcha = new ReCaptcha( $secret ); // check secret key

        if ( isset( $recaptcha_response ) ) {
            $response = $reCaptcha->verifyResponse(
                $_SERVER['REMOTE_ADDR'],
                $recaptcha_response
            );
        }

        if ( $response == null || ! $response->success ) {
            return false;
        }

        return true;
    }

    /**
     * process_error_codes function.
     *
     * @access protected
     * @param array $codes (default: array())
     * @return void
     */
    protected function process_error_codes( $codes = array() ) {
        if ( ! is_array( $codes ) ) :
            $code = $codes;
            $codes = array( $code );
        endif;

        foreach ( $codes as $code ) :
            switch ( $code ) :
                case 'empty_username':
                    echo pcl_format_error_message( 'Error', 'You need to enter your email address to continue.', 'error' );
                    break;
                case 'invalid_email':
                case 'invalidcombo':
                    echo pcl_format_error_message( 'Error', 'There are no users registered with this email address.', 'error' );
                    break;
                case 'password_reset_empty':
                    echo pcl_format_error_message( 'Error', 'Please enter a new password.', 'error' );
                    break;
                case 'password_reset_mismatch':
                    echo pcl_format_error_message( 'Error', 'The passwords do not match.', 'error' );
                    break;
                case 'recaptchaerror':
                    echo pcl_format_error_message( 'Error', 'Invalid reCAPTCHA.', 'error' );
                    break;
                default:
                    echo pcl_format_error_message( 'Error', 'There was an error, please try again.', 'error' );
                    break;
            endswitch;
         endforeach;

        return;
    }

    /**
     * replace_retrieve_password_message function.
     *
     * @access public
     * @param mixed $message
     * @param mixed $key
     * @param mixed $user_login
     * @param mixed $user_data
     * @return void
     */
    public function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
        return pickle_custom_login()->email->get_email_message(
            array(
                'type' => 'password_reset',
                'original_message' => $message,
                'key' => $key,
                'user_login' => $user_login,
            )
        );
    }

    /**
     * redirect_to_custom_password_reset function.
     *
     * Redirects to the custom password reset page, or the login page if there are errors.
     *
     * @access public
     * @return void
     */
    public function redirect_to_custom_password_reset() {
        $slug = pcl_page_slug( 'reset-password' );

        if ( 'GET' == $_SERVER['REQUEST_METHOD'] && $slug ) :
            $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] ); // Verify key / login combo
            $slug = pcl_page_slug( 'reset-password' );

            if ( ! $user || is_wp_error( $user ) ) :
                if ( $user && $user->get_error_code() === 'expired_key' ) :
                    wp_redirect( home_url( '/' . pcl_page_slug( 'reset-password' ) . '?login=expiredkey' ) );
                else :
                    wp_redirect( home_url( '/' . pcl_page_slug( 'reset-password' ) . '?login=invalidkey' ) );
                endif;
                exit;
            endif;

            $redirect_url = home_url( $slug );
            $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
            $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

            wp_redirect( $redirect_url );
            exit;
        endif;
    }

    /**
     * reset_password function.
     *
     * Resets the user's password if the password reset form was submitted.
     *
     * @access public
     * @return void
     */
    public function reset_password() {
        $slug = pcl_page_slug( 'reset-password' );

        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && $slug ) :
            $rp_key = $_REQUEST['rp_key'];
            $rp_login = $_REQUEST['rp_login'];
            $user = check_password_reset_key( $rp_key, $rp_login );
            $slug = pcl_page_slug( 'reset-password' );

            if ( ! $user || is_wp_error( $user ) ) :
                if ( $user && $user->get_error_code() === 'expired_key' ) :
                    wp_redirect( home_url( '/' . pcl_page_slug( 'reset-password' ) . '?login=expiredkey' ) );
                else :
                    wp_redirect( home_url( '/' . pcl_page_slug( 'reset-password' ) . '?login=invalidkey' ) );
                endif;

                exit;
            endif;

            if ( isset( $_POST['pass1'] ) ) :
                if ( $_POST['pass1'] != $_POST['pass2'] ) {
                    // Passwords don't match
                    $redirect_url = home_url( pcl_page_slug( 'reset-password' ) );
                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                    $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

                    wp_redirect( $redirect_url );
                    exit;
                }

                if ( empty( $_POST['pass1'] ) ) {
                    // Password is empty
                    $redirect_url = home_url( pcl_page_slug( 'reset-password' ) );
                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                    $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

                    wp_redirect( $redirect_url );
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password( $user, $_POST['pass1'] );

                $slug = pcl_page_slug( 'login' );

                if ( $slug ) :
                    wp_redirect( home_url( $slug . '?password=changed' ) );
                else :
                    wp_redirect( wp_login_url() . '?password=changed' );
                endif;
            else :
                echo 'Invalid request.';
            endif;

            exit;
        endif;
    }

}
