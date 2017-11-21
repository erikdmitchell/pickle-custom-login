<?php

class Pickle_Custom_Login_Profile {
    
    public $errors=array();

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action ('init' , array($this, 'pcl_change_profile_url'));
		add_action ('init' , array($this, 'update_user_profile'));
		add_action('pcl_before_profile', 'pcl_show_error_messages');	

		add_shortcode('pcl-profile', array($this, 'profile_page'));
	}

	/**
	 * profile_page function.
	 * 
	 * @access public
	 * @return void
	 */
	public function profile_page() {
		return pcl_get_template_html('profile');
	}

    /**
     * pcl_change_profile_url function.
     * 
     * @access public
     * @return void
     */
    public function pcl_change_profile_url() {
        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin/profile.php')) :
            wp_redirect(get_edit_user_link());
            exit;
        endif;
    }

    public function update_user_profile() {
        global $wp_roles;
        
        $current_user = wp_get_current_user();
        $error = array();    
        
        if (!isset($_POST['pcl_update_profile']) || !wp_verify_nonce($_POST['pcl_update_profile'], 'update-user_'.$current_user->ID))
            return false;           

        // update user password //
        $this->update_password($_POST['password'], $_POST['password_check']);
        
        // update user url //
        if (!empty($_POST['url'])) :
            wp_update_user(array(
                'ID' => $current_user->ID, 
                'user_url' => esc_attr($_POST['url'])
            ));
        endif;
        
        // update email //
        $this->update_email($_POST['email']);
        
        // update basic user information //

        if (!empty($_POST['firstname']))
            update_user_meta($current_user->ID, 'first_name', esc_attr($_POST['firstname']));
        
        if( !empty($_POST['lastname']))
            update_user_meta($current_user->ID, 'last_name', esc_attr($_POST['lastname']));
            
        if (!empty($_POST['display_name'])) :
            wp_update_user(array(
                'ID' => $current_user->ID, 
                'display_name' => esc_attr($_POST['display_name'])
            ));
            update_user_meta($current_user->ID, 'display_name', esc_attr($_POST['display_name']));
        endif;
      
        if (!empty($_POST['description']))
            update_user_meta($current_user->ID, 'description', esc_attr($_POST['description']));
        
        // "redirect" to show updated info //
        
        if (!$this->has_errors()) :
            // action hook for plugins and extra fields saving //
            do_action('edit_user_profile_update', $current_user->ID);
            
            wp_redirect(get_permalink().'?updated=true'); 
            exit;
        endif;
    }
    
    protected function update_password($password='', $password_check='') {
        $current_user = wp_get_current_user();
        
        if (empty($password) && empty($password_check))
            return;
        
        if (empty($password) || empty($password_check)) :
            $this->add_error('A password field was left empty. Your password was not updated.');
            
            return;
        endif;
            
        if ($password == $password_check) :
            wp_update_user(array(
                'ID' => $current_user->ID, 
                'user_pass' => esc_attr($password),
            ));
        else :
            $this->add_error('The passwords you entered do not match. Your password was not updated.');
        endif;        
    }
    
    protected function update_email($email='') {
        $current_user = wp_get_current_user();
        
        if (empty($email))
            return;
            
        if (!is_email(esc_attr($email))) :
            $error[]=__('The Email you entered is not valid. Please try again.', 'pcl');
        elseif (email_exists(esc_attr($email)) != $current_user->id) :
            $error[]=__('This email is already used by another user. Try a different one.', 'pcl');
        else :
            wp_update_user(array(
                'ID' => $current_user->ID, 
                'user_email' => esc_attr($email)
            ));
        endif;  
    }
    
    /**
     * add_error function.
     * 
     * @access protected
     * @param string $message (default: '')
     * @return void
     */
    protected function add_error($message='') {
        if (empty($message))
            return;
            
        $this->errors[]=$message;
    }
    
    /**
     * has_errors function.
     * 
     * @access public
     * @return void
     */
    public function has_errors() {        
        if (count($this->errors))
            return true;
            
        return false;
    }
    
    public function display_errors() {
        foreach ($this->errors as $error) :
            echo '<div id="message" class="error"><p>'.$error.'</p></div>';
        endforeach;      
    }

}