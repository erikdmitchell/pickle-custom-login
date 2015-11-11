<?php
/**
 * EMCustomLoginUserActivation class.
 *
 * @since 0.1.0
 */
class EMCustomLoginUserActivation {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_shortcode('emcl-user-activation',array($this,'user_activation_form'));
	}

	/**
	 * user_activation_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function user_activation_form() {
		if (is_user_logged_in())
			return emcl_get_template_html('logged-in');

		return emcl_get_template_html('user-activation-form');
	}

	/**
	 * activate_user function.
	 *
	 * @access public
	 * @return void
	 */
	public function activate_user() {
		global $wpdb;

		if (!isset($_GET['user_login']) || !isset($_GET['key']))
			return false;

		$user_id=$wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login='{$_GET['user_login']}' AND  user_activation_key='{$_GET['key']}'");

		if ($user_id) :
			$code=get_user_meta($user_id,'has_to_be_activated',true);
			if ($code==$_GET['key']) :
				delete_user_meta($user_id,'has_to_be_activated');
				return true;
			endif;
		endif;

		return false;
	}

	/**
	 * is_user_authenticated function.
	 *
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
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