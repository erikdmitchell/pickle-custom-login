<?php
/**
 * EMCustomLoginAdmin class.
 *
 * @sonce 0.1.0
 */
class EMCustomLoginAdmin {

	protected $admin_notices=array();

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_notices', array($this, 'admin_notices'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('init', array($this, 'update_admin_settings'));
		add_action('wp_trash_post', array($this, 'check_emcl_pages_on_trash'));
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_options_page('EM Custom Login','EM Custom Login','manage_options','em_custom_login',array($this,'admin_page'));
	}

	/**
	 * admin_scripts_styles function.
	 *
	 * @access public
	 * @param mixed $hook
	 * @return void
	 */
	public function admin_scripts_styles($hook) {
		if ($hook!='settings_page_em_custom_login')
			return false;

		wp_enqueue_script('emcl-admin-script', plugins_url('/js/admin.js', __FILE__), array('jquery'), '0.1.0', true);

		wp_enqueue_style('emcl-admin-style', plugins_url('/css/admin.css', __FILE__));
	}

	/**
	 * admin_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_page() {
		echo $this->get_admin_page('settings');
	}

	/**
	 * update_admin_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function update_admin_settings() {
		if (isset($_POST['custom_login_admin']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom-login-nonce')) :
			// update pages //
			$pages=get_option('emcl-pages');
			$pages['login']=$_POST['login_page'];
			$pages['register']=$_POST['register_page'];
			$pages['forgot-password']=$_POST['forgot_password_page'];
			$pages['reset-password']=$_POST['reset_page'];
			$pages['activate-account']=$_POST['activate_page'];

			update_option('emcl-pages',$pages);

			// update redirects //
			if ($_POST['redirect_users']!='')
				update_option('emcl-login-redirect',$_POST['redirect_users']);

			if ($_POST['redirect_after_registration']!='')
				update_option('emcl-register-redirect',$_POST['redirect_after_registration']);

			if ($_POST['redirect_after_logout']!='')
				update_option('emcl-logout-redirect', $_POST['redirect_after_logout']);

			// update admin bar //
			if (isset($_POST['hide_admin_bar'])) :
				update_option('emcl-hide-admin-bar',$_POST['hide_admin_bar']);
			else :
				delete_option('emcl-hide-admin-bar');
			endif;

			// update reCaptcha //
			if (isset($_POST['enable_recaptcha'])) :
				update_option('emcl-enable-recaptcha',$_POST['enable_recaptcha']);
			else :
				delete_option('emcl-enable-recaptcha');
			endif;

			if ($_POST['recaptcha_site_key']!='')
				update_option('emcl-recaptcha-site-key',$_POST['recaptcha_site_key']);

			if ($_POST['recaptcha_secret_key']!='')
				update_option('emcl-recaptcha-secret-key',$_POST['recaptcha_secret_key']);

			// update retrieve password email //
			if (isset($_POST['retrieve_password_email']) && $_POST['retrieve_password_email']!='')
				update_option('emcl-retrieve-password-email',wp_kses_post($_POST['retrieve_password_email']));

			// require activation key //
			if (isset($_POST['require_activation_key'])) :
				update_option('emcl-require-activation-key',$_POST['require_activation_key']);
			else :
				delete_option('emcl-require-activation-key');
			endif;

			// update account creation email //
			if (isset($_POST['account_creation_email']) && $_POST['account_creation_email']!='')
				update_option('emcl-account-creation-email',wp_kses_post($_POST['account_creation_email']));

			// update account activation email //
			if (isset($_POST['account_activation_email']) && $_POST['account_activation_email']!='')
				update_option('emcl-account-activation-email',wp_kses_post($_POST['account_activation_email']));

			$this->admin_notices['updated']='Settings Updated!';
		endif;
	}

	/**
	 * admin_notices function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_notices() {
		if (empty($this->admin_notices))
			return;

		$html=null;

		foreach ($this->admin_notices as $type => $message) :
			$html.='<div class="'.$type.'"><p>'.$message.'</p></div>';
		endforeach;

		echo $html;
	}

	/**
	 * default_email_content function.
	 *
	 * @access protected
	 * @param string $slug (default: '')
	 * @return void
	 */
	protected function default_email_content($slug='') {
		$content='';

		switch ($slug) :
			case 'retrieve_password_email':
				$content.="Hello!\r\n\r\n";
				$content.="You asked us to reset your password for your account using the email address {username}\r\n\r\n";
				$content.="If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.\r\n\r\n";
				$content.="To reset your password, visit the following address:\r\n\r\n";
				$content.="{password_reset}\r\n\r\n";
				$content.="Thanks!\r\n\r\n";
				break;
			case 'account_activation_email':
				$content="Username: {username}\r\n\r\n";
				$content.="To activate your account, visit the following address:\r\n\r\n";
				$content.="{activate_account_link}\r\n\r\n";
			  $content.="If you have any problems, please contact us at {admin_email_link}\r\n\r\n";
				$content.="Cheers!\r\n\r\n";
				break;
			case 'account_creation_email':
				$content="Username: {username}\r\n\r\n";
				$content.="To set your password, visit the following address:\r\n\r\n";
				$content.="{set_password_link}\r\n\r\n";
				$content.="Or, login here:\r\n\r\n";
				$content.="{login_url}\r\n\r\n";
			  $content.="If you have any problems, please contact us at {admin_email_link}\r\n\r\n";
				$content.="Cheers!\r\n\r\n";
			default:
				break;
		endswitch;

		return $content;
	}

	/**
	 * check_emcl_pages_on_trash function.
	 *
	 * if a user trashes one of our set pages, we need to remove the id from our settings (option)
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function check_emcl_pages_on_trash($post_id) {
		$pages=get_option('emcl-pages');

		foreach ($pages as $slug => $id) :
			if ($post_id==$id)
				$pages[$slug]=null;
		endforeach;

		update_option('emcl-pages',$pages);
	}

	/**
	 * get_admin_page function.
	 *
	 * @access public
	 * @param bool $template_name (default: false)
	 * @return void
	 */
	public function get_admin_page($template_name=false) {
		if (!$template_name)
			return false;

		ob_start();

		do_action('emcl_before_admin_'.$template_name);

		include(EMCL_PATH.'adminpages/'.$template_name.'.php');

		do_action('emcl_after_admin_'.$template_name);

		$html=ob_get_contents();

		ob_end_clean();

		return $html;
	}

}

$EMCustomLoginAdmin=new EMCustomLoginAdmin();
?>