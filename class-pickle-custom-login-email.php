<?php
/**
 * Main login email class
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

/**
 * PCL_Login class.
 */
class Pickle_Custom_Login_Email {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {

    }

    /**
     * send_email function.
     *
     * @access public
     * @param string $args (default: '')
     * @return void
     */
    public function send_email( $args = '' ) {
        $default_args = array(
            'user_id' => 0,
            'type' => 'registration',
            'notify' => 'both',
        );
        $args = wp_parse_args( $args, $default_args );

        if ( ! $args['user_id'] ) {
            return false;
        }

        $user = get_userdata( $args['user_id'] );

        $this->notify_admin( $user, $args['type'] );

        if ( 'admin' === $args['notify'] || empty( $args['notify'] ) ) {
            return;
        }

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        $hashed = $this->update_user_activation_hash( $user );

        switch ( $args['type'] ) :
            case 'account_verification':
                $title = sprintf( __( '[%s] Account verification' ), $blogname );
                $message = $this->get_email_message(
                    array(
                        'type' => 'account_creation_activation_required',
                        'key' => $hashed,
                        'user_login' => $user->user_login,
                    )
                );

                add_user_meta( $user->ID, 'has_to_be_activated', $hashed, true );
                break;
            default:
                if ( pcl_require_admin_activation() ) :
                    $title = sprintf( __( '[%s] Thank you for registering' ), $blogname );
                    $message = $this->get_email_message(
                        array(
                            'type' => 'admin_activation_required',
                            'key' => $hashed,
                            'user_login' => $user->user_login,
                        )
                    );

                    add_user_meta( $user->ID, 'has_to_be_approved', 1, true ); // THIS PROBABLY NEEDS TO CHANGE
                elseif ( pcl_is_activation_required() ) :
                    $title = sprintf( __( '[%s] Account verification' ), $blogname );
                    $message = $this->get_email_message(
                        array(
                            'type' => 'account_creation_activation_required',
                            'key' => $hashed,
                            'user_login' => $user->user_login,
                        )
                    );

                    add_user_meta( $user->ID, 'has_to_be_activated', $hashed, true );
                else :
                    $title = sprintf( __( '[%s] Your username and password info' ), $blogname );
                    $message = $this->get_email_message(
                        array(
                            'type' => 'account_creation',
                            'key' => wp_generate_password( 20, false ),
                            'user_login' => $user->user_login,
                        )
                    );
                endif;
        endswitch;

        $mail = wp_mail( $user->user_email, $title, $message );
    }

    /**
     * get_email_message function.
     *
     * @access public
     * @param string $args (default: '')
     * @return void
     */
    public function get_email_message( $args = '' ) {
        $default_args = array(
            'type' => '',
            'original_message' => '',
            'key' => '',
            'user_login' => '',
            'user_id' => 0,
            'user' => false,
        );
        $args = wp_parse_args( $args, $default_args );
        $message = '';

        // we need an email type to send //
        if ( empty( $args['type'] ) ) {
            return false;
        }

        // we need somethign to get the user details //
        if ( ! $args['user_id'] && empty( $args['user_login'] ) ) {
            return false;
        }

        // get user details //
        if ( $args['user_id'] ) :
            $user = get_userdata( $args['user_id'] );
        else :
            $user = get_user_by( 'login', $args['user_login'] );
        endif;

        // one last check //
        if ( ! $user || is_wp_error( $user ) ) {
            return false;
        }

        switch ( $args['type'] ) :
            case 'admin_activation_required':
                $message = $this->add_custom_message(
                    array(
                        'option' => 'pcl-admin-activation-email',
                        'message' => $args['original_message'],
                        'key' => $args['key'],
                        'user_login' => $user->user_login,
                    )
                );

                break;
            case 'password_reset':
                $message = $this->add_custom_message(
                    array(
                        'option' => 'pcl-retrieve-password-email',
                        'message' => $args['original_message'],
                        'key' => $args['key'],
                        'user_login' => $user->user_login,
                    )
                );

                break;
            case 'account_creation_activation_required':
                $message = $this->add_custom_message(
                    array(
                        'option' => 'pcl-account-activation-email',
                        'message' => $args['original_message'],
                        'key' => $args['key'],
                        'user_login' => $user->user_login,
                    )
                );

                break;
            case 'account_creation':
                $message = $this->add_custom_message(
                    array(
                        'option' => 'pcl-account-creation-email',
                        'message' => $args['original_message'],
                        'key' => $args['key'],
                        'user_login' => $user->user_login,
                    )
                );

                break;
            default:
                break;
        endswitch;

        return $message;
    }

    /**
     * add_custom_message function.
     *
     * @access protected
     * @param string $args (default: '')
     * @return void
     */
    protected function add_custom_message( $args = '' ) {
        $default_args = array(
            'option' => '',
            'message' => '',
            'key' => '',
            'user_login' => '',
        );
        $args = wp_parse_args( $args, $default_args );
        $message = $args['message'];

        if ( empty( $args['option'] ) ) {
            return $message;
        }

        // check if custom message exists //
        if ( $custom_message = get_option( $args['option'] ) ) :
            $custom_message = stripslashes( $custom_message ); // clean from db
            $message = $this->clean_placeholders( $custom_message, $args['user_login'], $args['key'] );
        endif;

        // message is still empty.
        if ( empty( $message ) ) :
            switch ( $args['option'] ) :
                case 'pcl-retrieve-password-email':
                    $id = 'retrieve_password_email';
                    break;
                case 'pcl-account-creation-email':
                    $id = 'account_creation_email';
                    break;
                case 'pcl-account-activation-email':
                    $id = 'account_activation_email';
                    break;
                case 'pcl-admin-activation-email':
                    $id = 'admin_activation_email';
                    break;
            endswitch;

            $message = $this->default_email_content( $id );
            $message = $this->clean_placeholders( $message, $args['user_login'], $args['key'] );
        endif;

        return $message;
    }

    public function default_email_content( $slug = '' ) {
        $content = '';

        switch ( $slug ) :
            case 'admin_activation_email':
                $content = "Username: {username}\r\n\r\n";
                $content .= "Thank you for registering for access to the Dell Boomi Partner Resources Center.\r\n\r\n";
                $content .= "We will process your request within the next 48 business hours.\r\n\r\n";
                $content .= "You will be notified using the partner company email you provided in your registration submission when your request for access has been approved.\r\n\r\n";
                $content .= "Questions? Please contact us at: BoomiPartners@Dell.com\r\n\r\n";
                $content .= "The Dell Boomi Partner Program Team\r\n\r\n";
                break;
            case 'retrieve_password_email':
                $content .= "Hello!\r\n\r\n";
                $content .= "You asked us to reset your password for your account using the email address {username}\r\n\r\n";
                $content .= "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.\r\n\r\n";
                $content .= "To reset your password, visit the following address:\r\n\r\n";
                $content .= "{password_reset_link}\r\n\r\n";
                $content .= "Thanks!\r\n\r\n";
                break;
            case 'account_activation_email':
                $content = "Username: {username}\r\n\r\n";
                $content .= "To activate your account, visit the following address:\r\n\r\n";
                $content .= "{activate_account_link}\r\n\r\n";
                $content .= "Questions? Please contact us at: BoomiPartners@Dell.com\r\n\r\n";
                $content .= "The Dell Boomi Partner Program Team\r\n\r\n";

                break;
            case 'account_creation_email':
                $content = "Username: {username}\r\n\r\n";
                $content .= "To set your password, visit the following address:\r\n\r\n";
                $content .= "{set_password_link}\r\n\r\n";
                $content .= "Or, login here:\r\n\r\n";
                $content .= "{login_url}\r\n\r\n";
                $content .= "If you have any problems, please contact us at BoomiPartners@Dell.com\r\n\r\n";
                $content .= "The Dell Boomi Partner Program Team\r\n\r\n";
            default:
                break;
        endswitch;

        return $content;
    }

    /**
     * clean_placeholders function.
     *
     * @access protected
     * @param string $message (default: '')
     * @param string $user_login (default: '')
     * @param string $key (default: '')
     * @return void
     */
    protected function clean_placeholders( $message = '', $user_login = '', $key = '' ) {
        $placeholders = array(
            '{user_login}' => $user_login,
            '{password_reset_link}' => site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ),
            '{username}' => $user_login,
            '{activate_account_link}' => home_url( '/' . pcl_page_slug( 'activate-account' ) . "/?key=$key&user_login=$user_login" ),
            '{admin_email_link}' => get_option( 'admin_email' ),
            '{set_password_link}' => network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ),
            '{login_url}' => wp_login_url(),
        );

        $message = strtr( $message, $placeholders );

        return $message;
    }

    /**
     * notify_admin function.
     *
     * @access private
     * @param string $user (default: '')
     * @param string $type (default: 'registration')
     * @return void
     */
    private function notify_admin( $user = '', $type = 'registration' ) {
        // The blogname option is escaped with esc_html on the way into the database in sanitize_option we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

        switch ( $type ) :
            case 'account_verification':
                return;
            default:
                if ( pcl_require_admin_activation() ) :
                    $title = sprintf( __( '[%s] Thank you for registering' ), $blogname );

                    $message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
                    $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
                    $message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "\r\n";
                    $message .= sprintf( __( 'Phone: %s' ), $user->user_phone ) . "\r\n";

                    $message .= __( 'Please login and verify the user. They will not be able to access the site until verified.' ) . "\r\n";
                else :
                    $title = sprintf( __( '[%s] New User Registration' ), $blogname );

                    $message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
                    $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
                    $message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "\r\n";
                endif;
        endswitch;

        wp_mail( get_option( 'admin_email' ), $title, $message );
    }

    /**
     * update_user_activation_hash function.
     *
     * @access protected
     * @param string $user (default: '')
     * @return void
     */
    protected function update_user_activation_hash( $user = '' ) {
        global $wpdb, $wp_hasher;

        // Generate something random for a password reset key.
        $key = wp_generate_password( 20, false );

        // This action is documented in wp-login.php //
        do_action( 'retrieve_password_key', $user->user_login, $key );

        // Now insert the key, hashed, into the DB.
        if ( empty( $wp_hasher ) ) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            $wp_hasher = new PasswordHash( 8, true );
        }
        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

        return $hashed;
    }

}
