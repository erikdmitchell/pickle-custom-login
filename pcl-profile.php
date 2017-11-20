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
/*        
 global $current_user, $wp_roles;
get_currentuserinfo();


$error = array();    
// If profile was saved, update profile. 
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    // Update user password.
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    // Update user information.
    if ( !empty( $_POST['url'] ) )
       wp_update_user( array ('ID' => $current_user->ID, 'user_url' => esc_attr( $_POST['url'] )));
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else{
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
        }
    }

    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
    if ( !empty( $_POST['display_name'] ) )
        wp_update_user(array('ID' => $current_user->ID, 'display_name' => esc_attr( $_POST['display_name'] )));
      update_user_meta($current_user->ID, 'display_name' , esc_attr( $_POST['display_name'] ));
    if ( !empty( $_POST['description'] ) )
        update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );

    // Redirect so the page will show updated info.
  // I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ 
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink().'?updated=true' ); exit;
    }       
}


            <?php if ( $_GET['updated'] == 'true' ) : ?> <div id="message" class="updated"><p>Your profile has been updated.</p></div> <?php endif; ?>
                <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
*/
       
    }

}