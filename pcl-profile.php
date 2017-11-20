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
        
    }

}