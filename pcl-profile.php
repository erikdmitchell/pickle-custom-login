<?php

class Pickle_Custom_Login_Profile {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		//add_action('init', array($this, 'redirect_login_page'));
		add_action('pcl_before_profile', 'pcl_show_error_messages');	

		add_shortcode('pcl-profile', array($this, 'profile_page'));
	}

add_action ('init' , 'prevent_profile_access');
 
function prevent_profile_access() {
   		if (current_user_can('manage_options')) return '';
		
   		if (strpos ($_SERVER ['REQUEST_URI'] , 'wp-admin/profile.php' )){
      		wp_redirect ("http://sampledomain.com/specific_page/");
            die();
 		 }
 
}

return apply_filters( 'get_edit_user_link', $link, $user->ID );

	public function profile_page() {
		return pcl_get_template_html('profile');
	}

/*
	public function redirect_login_page() {
		$slug=pcl_page_slug('login');
		$page_viewed=basename($_SERVER['REQUEST_URI']);

		if ($slug) :
			$login_page=home_url($slug);

			if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') :
				wp_safe_redirect($login_page);
				exit;
			endif;
		endif;
	}
*/

    public function update_user_profile() {
        
    }

}