<?php

final class PickleCustomLoginAdmin {
	
	protected $admin_notices=array();

	protected static $_instance=null;

	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance=new self();
		}
		
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	private function define_constants() {
		$this->define('PCL_ADMIN_PATH', plugin_dir_path(__FILE__));
		$this->define('PCL_ADMIN_URL', plugin_dir_url(__FILE__));
		
	}

	private function define($name, $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}

	public function includes() {

	}

	private function init_hooks() {
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_notices', array($this, 'admin_notices'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('admin_init', array($this, 'update_settings'));
		add_action('wp_trash_post', array($this, 'check_pages_on_trash'));		
	}

	public function admin_menu() {
		add_options_page('Pickle Custom Login', 'Pickle Custom Login', 'manage_options', 'pickle_custom_login', array($this, 'admin_page'));
	}

	public function admin_page() {
		$html=null;
		$tabs=array(
			'settings' => 'Settings',
			'emails' => 'Emails',
		);
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings';
			
		$html.='<div class="wrap pcl-admin">';
			$html.='<h1>Pickle Custom Login</h1>';
			
			$html.='<h2 class="nav-tab-wrapper">';
				foreach ($tabs as $key => $name) :
					if ($active_tab==$key) :
						$class='nav-tab-active';
					else :
						$class=null;
					endif;

					$html.='<a href="?page=pickle_custom_login&tab='.$key.'" class="nav-tab '.$class.'">'.$name.'</a>';
				endforeach;
			$html.='</h2>';

			switch ($active_tab) :
				case 'emails':
					$html.=$this->get_admin_page('emails');
					break;					
				default:
					$html.=$this->get_admin_page('settings');
			endswitch;

		$html.='</div>';

		echo $html;
	}

	public function admin_notices() {
		if (empty($this->admin_notices))
			return;

		$html=null;

		foreach ($this->admin_notices as $type => $message) :
			$html.='<div class="'.$type.'"><p>'.$message.'</p></div>';
		endforeach;

		echo $html;
	}

	public function admin_scripts_styles($hook) {		
		if ($hook!='settings_page_pickle_custom_login')
			return false;

		wp_enqueue_script('pcl-admin-script', PCL_ADMIN_URL.'js/admin.js', array('jquery'), PCL_VERSION, true);

		wp_enqueue_style('pcl-admin-style', PCL_ADMIN_URL.'css/admin.css', '', PCL_VERSION);
	}

	public function update_settings() {
echo '<pre>';
print_r($_POST);
echo '</pre>';		
		if (isset($_POST['custom_login_admin']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom-login-nonce')) :
			// update pages //
			$pages=get_option('pcl_pages');
			$pages['login']=$_POST['login_page'];
			$pages['register']=$_POST['register_page'];
			$pages['forgot-password']=$_POST['forgot_password_page'];
			$pages['reset-password']=$_POST['reset_page'];
			$pages['activate-account']=$_POST['activate_page'];

			update_option('pcl_pages',$pages);

			// update redirects //
			if ($_POST['redirect_users']!='')
				update_option('pcl-login-redirect',$_POST['redirect_users']);

			if ($_POST['redirect_after_registration']!='')
				update_option('pcl-register-redirect',$_POST['redirect_after_registration']);

			if ($_POST['redirect_after_logout']!='')
				update_option('pcl-logout-redirect', $_POST['redirect_after_logout']);

			// update admin bar //
			if (isset($_POST['hide_admin_bar'])) :
				update_option('pcl-hide-admin-bar',$_POST['hide_admin_bar']);
			else :
				delete_option('pcl-hide-admin-bar');
			endif;

			// update reCaptcha //
			if (isset($_POST['enable_recaptcha'])) :
				update_option('pcl-enable-recaptcha',$_POST['enable_recaptcha']);
			else :
				delete_option('pcl-enable-recaptcha');
			endif;

			if ($_POST['recaptcha_site_key']!='')
				update_option('pcl-recaptcha-site-key',$_POST['recaptcha_site_key']);

			if ($_POST['recaptcha_secret_key']!='')
				update_option('pcl-recaptcha-secret-key',$_POST['recaptcha_secret_key']);

			// update retrieve password email //
			if (isset($_POST['retrieve_password_email']) && $_POST['retrieve_password_email']!='')
				update_option('pcl-retrieve-password-email',wp_kses_post($_POST['retrieve_password_email']));

			// require activation key //
			if (isset($_POST['require_activation_key'])) :
				update_option('pcl-require-activation-key',$_POST['require_activation_key']);
			else :
				delete_option('pcl-require-activation-key');
			endif;

			// update account creation email //
			if (isset($_POST['account_creation_email']) && $_POST['account_creation_email']!='')
				update_option('pcl-account-creation-email',wp_kses_post($_POST['account_creation_email']));

			// update account activation email //
			if (isset($_POST['account_activation_email']) && $_POST['account_activation_email']!='')
				update_option('pcl-account-activation-email',wp_kses_post($_POST['account_activation_email']));

			$this->admin_notices['updated']='Settings Updated!';
		endif;
	}

	public function check_pages_on_trash($post_id) {
		$pages=get_option('pcl_pages');

		foreach ($pages as $slug => $id) :
			if ($post_id==$id)
				$pages[$slug]=null;
		endforeach;

		update_option('pcl_pages',$pages);
	}

	protected function default_email_content($slug='') {
		$content='';

		switch ($slug) :
			case 'retrieve_password_email':
				$content.="Hello!\r\n\r\n";
				$content.="You asked us to reset your password for your account using the email address {username}\r\n\r\n";
				$content.="If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.\r\n\r\n";
				$content.="To reset your password, visit the following address:\r\n\r\n";
				$content.="{password_reset_link}\r\n\r\n";
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

	public function get_admin_page($template_name=false) {
		if (!$template_name)
			return false;

		ob_start();

		do_action('pcl_before_admin_'.$template_name);

		include(PCL_PATH.'admin/adminpages/'.$template_name.'.php');

		do_action('pcl_after_admin_'.$template_name);

		$html=ob_get_contents();

		ob_end_clean();

		return $html;
	}
	
}
?>