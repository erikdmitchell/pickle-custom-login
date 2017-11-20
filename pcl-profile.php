<?php

class Pickle_Custom_Login_Profile {

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

	public function profile_page() {
		return pcl_get_template_html('profile');
	}

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
            
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';            




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
/*
    // Redirect so the page will show updated info.
  // I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ 
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink().'?updated=true' ); exit;
    }       

*/

/*
            <?php if ( $_GET['updated'] == 'true' ) : ?> <div id="message" class="updated"><p>Your profile has been updated.</p></div> <?php endif; ?>
                <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
*/
    }
    
    protected function update_password($password='', $password_check='') {
        $current_user = wp_get_current_user();
        
        if (empty($password) || empty($password_check))
            return;
            
        if ($password == $password_check) :
            wp_update_user(array(
                'ID' => $current_user->ID, 
                'user_pass' => esc_attr($password),
            ));
        else :
            $error[]=__('The passwords you entered do not match. Your password was not updated.', 'pcl');
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

}