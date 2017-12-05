<?php

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
    public function send_email($args='') {        
        $default_args=array(
            'user_id' => 0,
            'type' => 'registration',
            'notify' => 'both',
        );
        $args=wp_parse_args($args, $default_args);
 
     	if (!$args['user_id'])
    		return false;
    
    	$user=get_userdata($args['user_id']);
    	
    	$this->notify_admin($user);
    	
        if ('admin' === $args['notify'] || empty($args['notify']))
            return;

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);        
        $hashed=$this->update_user_activation_hash($user);

        switch ($args['type']) :
            default:
                if (pcl_require_admin_activation()) :
                    $title=sprintf(__('[%s] Thank you for registering'), $blogname);
                    $message=$this->get_email_message(array(
                        'type' => 'admin_activation_required', 
                        'key' => $hashed, 
                        'user_login' => $user->user_login,                       
                    ));
                    
                    add_user_meta($user_id, 'has_to_be_approved', 1, true); // THIS PROBABLY NEEDS TO CHANGE
                elseif (pcl_is_activation_required()) :
                    $title=sprintf(__('[%s] Account verification'), $blogname);
                    $message=$this->get_email_message(array(
                        'type' => 'account_creation_activation_required',  
                        'key' => $hashed, 
                        'user_login' => $user->user_login,                       
                    ));

                    add_user_meta($user_id, 'has_to_be_activated', $hashed, true);
                else:
                    $title=sprintf(__('[%s] Your username and password info'), $blogname);
                    $message=$this->get_email_message(array(
                        'type' => 'account_creation',  
                        'key' => wp_generate_password(20, false), 
                        'user_login' => $user->user_login,                       
                    ));
                endif;                
        endswitch;

        wp_mail($user->user_email, $title, $message);                    	    
    }
    
    /**
     * get_email_message function.
     * 
     * @access public
     * @param string $args (default: '')
     * @return void
     */
    public function get_email_message($args='') {
        $default_args=array(
            'type' => '', 
            'original_message' => '', 
            'key' => '', 
            'user_login' => '', 
            'user_id' => 0,
            'user' => false,
        );
        $args=wp_parse_args($args, $default_args);
        $message='';

    	// we need an email type to send //
    	if (empty($args['type']))
    		return false;
    
    	// we need somethign to get the user details //
    	if (!$args['user_id'] && empty($args['user_login']))
    		return false;
    
    	// get user details //
    	if ($args['user_id']) :
    		$user=get_userdata($args['user_id']);
    	else :
    		$user=get_user_by('login', $args['user_login']);
    	endif;
    
    	// one last check //
    	if (!$user || is_wp_error($user))
    		return false;
    
    	switch ($args['type']) :
    	    case 'admin_activation_required':
    	        $message=$this->add_custom_message(array(
        	       'option' => 'pcl-admin-activation-email',
                   'message' => $args['original_message'],
                   'key' => $args['key'],
                   'user_login' => $user->user_login, 
    	        ));
    	        
    	        break;
    		case 'password_reset':
    	        $message=$this->add_custom_message(array(
        	       'option' => 'pcl-retrieve-password-email',
                   'message' => $args['original_message'],
                   'key' => $args['key'],
                   'user_login' => $user->user_login, 
    	        ));
    			
    			break;
    		case 'account_creation_activation_required':
    	        $message=$this->add_custom_message(array(
        	       'option' => 'pcl-account-activation-email',
                   'message' => $args['original_message'],
                   'key' => $args['key'],
                   'user_login' => $user->user_login, 
    	        ));
    			
    			break;
    		case 'account_creation':
    	        $message=$this->add_custom_message(array(
        	       'option' => 'pcl-account-creation-email',
                   'message' => $args['original_message'],
                   'key' => $args['key'],
                   'user_login' => $user->user_login, 
    	        ));
    			
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
    protected function add_custom_message($args='') {
        $default_args=array(
            'option' => '',
            'message' => '',
            'key' => '',
            'user_login' => '',  
        );
        $args=wp_parse_args($args, $default_args);
        $message=$args['message'];
        
        if (empty($args['option']))
            return $message;
            
     	// check if custom message exists //
    	if ($custom_message=get_option($args['option'])) :
    		$custom_message=stripslashes($custom_message); // clean from db
    		$message=$this->clean_placeholders($custom_message, $args['user_login'], $args['key']);
    	endif;
    
    	return $message;       
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
    protected function clean_placeholders($message='', $user_login='', $key='') {
    	$placeholders=array(
    		'{user_login}' => $user_login,
    		'{password_reset_link}' => site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login), 'login'),
    		'{username}' => $user_login,
    		'{activate_account_link}' => home_url("/".pcl_page_slug('activate-account')."/?key=$key&user_login=$user_login"),
    		'{admin_email_link}' => get_option('admin_email'),
    		'{set_password_link}' => network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login), 'login'),
    		'{login_url}' => wp_login_url(),
    	);
    
    	$message=strtr($message, $placeholders);
    
    	return $message;
    }
    
    /**
     * notify_admin function.
     * 
     * @access private
     * @param string $user (default: '')
     * @return void
     */
    private function notify_admin($user='') {
        // The blogname option is escaped with esc_html on the way into the database in sanitize_option we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message); // THIS NEEDS TO BE CUSTOMIZED
    }
    
    /**
     * update_user_activation_hash function.
     * 
     * @access protected
     * @param string $user (default: '')
     * @return void
     */
    protected function update_user_activation_hash($user='') {
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