<?php
/**
 * EMCustomLoginUserActivation class.
 *
 * @since 0.1.0
 */
class EMCustomLoginUserActivation {

	public function __construct() {
		add_shortcode('emcl-user-activation',array($this,'user_activation_form'));
	}

	public function user_activation_form() {
		return emcl_get_template_html('user-activation-form');
	}

	public function activate_user() {
		global $wpdb;

		$user_id=$wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login='{$_GET['user_login']}' AND  user_activation_key='{$_GET['key']}'");

		if ($user_id) :
			$code=get_user_meta($user_id,'has_to_be_activated',true);
			if ($code==$_GET['key']) :
				delete_user_meta( $user_id,'has_to_be_activated');
				return true;
			endif;
		endif;

		return false;
	}

	public function is_user_authenticated($user) {
		if (!$user || is_wp_error($user))
			return false;

		if (get_user_meta($user->ID,'has_to_be_activated',true)!=false)
			return false;

		return true;
	}

}

$EMCustomLoginUserActivation=new EMCustomLoginUserActivation();
?>