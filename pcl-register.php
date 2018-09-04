<?php

class Pickle_Custom_Login_Registration {

    protected $admin_activate_account_required = false;

    protected $activate_account_required = false;

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_new_user' ) );
        add_action( 'login_form_register', array( $this, 'register_form_redirect' ) );
        add_action( 'pcl_before_register-form', 'pcl_show_error_messages' );

        add_shortcode( 'pcl-registration-form', array( $this, 'registration_form' ) );
    }

    /**
     * registration_form function.
     *
     * @access public
     * @return void
     */
    public function registration_form() {
        if ( is_user_logged_in() ) {
            return pcl_get_template_html( 'logged-in' );
        }

        if ( $this->admin_activate_account_required ) :
            echo pcl_format_error_message( '', 'You will receive an email when your account is approved by an admin.', 'success' );
        elseif ( $this->activate_account_required ) :
            echo pcl_format_error_message( '', 'Please check your email to activate your account.', 'success' );
        endif;

        return pcl_get_template_html( 'register-form' );
    }

    /**
     * form_username_field function.
     *
     * @access public
     * @return void
     */
    public function form_username_field() {
        echo '<label for="pcl_username" class="required">' . __( 'Username' ) . '</label>';
        echo '<input name="pcl_registration[username]" id="pcl_username" class="" type="text"/>';
    }

    /**
     * form_email_field function.
     *
     * @access public
     * @return void
     */
    public function form_email_field() {
        echo '<label for="pcl_email" class="required">' . __( 'Email' ) . '</label>';
        echo '<input name="pcl_registration[email]" id="pcl_email" class="email" type="email"/>';
    }

    /**
     * form_name_field function.
     *
     * @access public
     * @return void
     */
    public function form_name_field() {
        echo '<label for="pcl_firstname">' . __( 'First Name' ) . '</label>';
        echo '<input name="pcl_registration[firstname]" id="pcl_firstname" type="text"/>';

        echo '<label for="pcl_lastname">' . __( 'Last Name' ) . '</label>';
        echo '<input name="pcl_registration[lastname]" id="pcl_lastname" type="text"/>';
    }

    /**
     * form_password_field function.
     *
     * @access public
     * @return void
     */
    public function form_password_field() {
        echo '<label for="pcl_password" class="required">' . __( 'Password' ) . '</label>';
        echo '<input name="pcl_registration[password]" id="pcl_password" class="password" type="password"/>';

        echo '<label for="pcl_password_check" class="required">' . __( 'Password Again' ) . '</label>';
        echo '<input name="pcl_registration[password_check]" id="pcl_password_check" class="password" type="password"/>';
    }

    /**
     * form_recaptcha_field function.
     *
     * @access public
     * @return void
     */
    public function form_recaptcha_field() {
        do_action( 'pcl_registraion_before_recaptcha' );

        if ( get_option( 'pcl-enable-recaptcha', false ) ) :
            echo '<div class="g-recaptcha" data-sitekey="' . get_option( 'pcl-recaptcha-site-key', '' ) . '"></div>';
         endif;
    }

    /**
     * form_register_button function.
     *
     * @access public
     * @return void
     */
    public function form_register_button() {
        echo '<input type="hidden" name="custom_register_nonce" value="' . wp_create_nonce( 'custom-register-nonce' ) . '" />';
        echo wp_nonce_field( 'pcl-register', 'pcl_registration_form', true, false );
        echo '<input type="submit" value="' . __( 'Register' ) . '" />';
    }

    /**
     * register_form_redirect function.
     *
     * @access public
     * @return void
     */
    public function register_form_redirect() {
        $slug = pcl_page_slug( 'register' );

        if ( $slug ) :
            wp_safe_redirect( home_url( $slug ) );
            exit;
        endif;
    }

    /**
     * add_new_user function.
     *
     * @access public
     * @return void
     */
    public function add_new_user() {
        if ( ! isset( $_POST['pcl_registration_form'] ) || ! wp_verify_nonce( $_POST['pcl_registration_form'], 'pcl-register' ) ) {
            return;
        }

        $fields = $_POST['pcl_registration'];

        // check username //
        $this->check_username( $fields['username'] );

        // check email //
        $this->check_email( $fields['email'] );

        // check password //
        $this->check_password( $fields['password'], $fields['password_check'] );

        // check recaptcha, if active
        if ( get_option( 'pcl-enable-recaptcha', false ) ) {
            $this->check_recaptcha( $_POST['g-recaptcha-response'] );
        }

        // only create the user in if there are no errors
        if ( ! pcl_has_error_messages() ) {
            $this->add_user( $fields, $_POST );
        }
    }

    /**
     * check_username function.
     *
     * @access protected
     * @param string $username (default: '')
     * @return void
     */
    protected function check_username( $username = '' ) {
        // Username already registered
        if ( username_exists( $username ) ) {
            pcl_add_error_message( 'username_unavailable', 'Username already taken' );
        }

        // invalid username
        if ( ! validate_username( $username ) ) {
            pcl_add_error_message( 'username_invalid', 'Invalid username' );
        }

        // empty username
        if ( $username == '' ) {
            pcl_add_error_message( 'username_empty', 'Please enter a username' );
        }
    }

    /**
     * check_email function.
     *
     * @access protected
     * @param string $email (default: '')
     * @return void
     */
    protected function check_email( $email = '' ) {
        // invalid email
        if ( ! is_email( $email ) ) {
            pcl_add_error_message( 'email_invalid', 'Invalid email' );
        }

        // Email address already registered
        if ( email_exists( $email ) ) {
            pcl_add_error_message( 'email_used', 'Email already registered' );
        }
    }

    /**
     * check_password function.
     *
     * @access protected
     * @param string $password (default: '')
     * @param string $password_check (default: '')
     * @return void
     */
    protected function check_password( $password = '', $password_check = '' ) {
        // passwords empty
        if ( $password == '' || $password_check == '' ) {
            pcl_add_error_message( 'password_empty', 'Please enter a password' );
        }

        // passwords do not match
        if ( $password != $password_check ) {
            pcl_add_error_message( 'password_mismatch', 'Passwords do not match' );
        }
    }

    /**
     * check_recaptcha function.
     *
     * @access protected
     * @param string $recaptcha_response (default: '')
     * @return void
     */
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
            pcl_add_error_message( 'recaptcha', 'Issue with the recaptcha' );
        }
    }

    /**
     * add_user function.
     *
     * @access protected
     * @param array $fields (default: array())
     * @param array $post_data (default: array())
     * @return void
     */
    protected function add_user( $fields = array(), $post_data = array() ) {
        $user_login = $fields['username'];
        $user_pass = $fields['password'];
        $redirect = get_option( 'pcl-register-redirect', home_url() );

        if ( ! isset( $fields['firstname'] ) ) :
            $first_name = '';
        else :
            $first_name = $fields['firstname'];
        endif;

        if ( ! isset( $fields['lastname'] ) ) :
            $last_name = '';
        else :
            $last_name = $fields['lastname'];
        endif;

        do_action( 'pcl_before_user_registration', $fields, $post_data );

        $user_args = array(
            'user_login' => $user_login,
            'user_pass' => $user_pass,
            'user_email' => $fields['email'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_registered' => date( 'Y-m-d H:i:s' ),
            'role' => 'subscriber',
        );
        $user_args = apply_filters( 'pcl_insert_user_args', $user_args, $fields, $post_data );

        $new_user_id = wp_insert_user( $user_args );

        do_action( 'pcl_after_user_registration', $new_user_id, $fields, $post_data );

        if ( $new_user_id ) :
            // send an email to the admin alerting them of the registration //
            pickle_custom_login()->email->send_email( array( 'user_id' => $new_user_id ) );

            // check our activation flags - admin activation, user (email) activation, other //
            if ( pcl_require_admin_activation() ) :
                $this->admin_activate_account_required = true;
            elseif ( pcl_is_activation_required() ) :
                $this->activate_account_required = true;
            else :
                // log the new user in
                wp_set_auth_cookie( $new_user_id );
                wp_set_current_user( $new_user_id, $user_login );
                do_action( 'wp_login', $user_login );

                // send the newly created user to the redirect page after logging them in
                wp_safe_redirect( $redirect );
                exit;
            endif;
        endif;
    }

}
