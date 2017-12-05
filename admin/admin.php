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

	/**
	 * init_hooks function.
	 * 
	 * @access private
	 * @return void
	 */
	private function init_hooks() {
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_notices', array($this, 'admin_notices'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('admin_init', array($this, 'update_settings'), 0);
		add_action('admin_init', array($this, 'update_emails'), 0);
		add_action('wp_trash_post', array($this, 'check_pages_on_trash'));		
	}

	/**
	 * admin_menu function.
	 * 
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_options_page('Pickle Custom Login', 'Pickle Custom Login', 'manage_options', 'pickle_custom_login', array($this, 'admin_page'));
	}

	/**
	 * admin_page function.
	 * 
	 * @access public
	 * @return void
	 */
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
	 * admin_scripts_styles function.
	 * 
	 * @access public
	 * @param mixed $hook
	 * @return void
	 */
	public function admin_scripts_styles($hook) {		
		if ($hook!='settings_page_pickle_custom_login')
			return false;

		wp_enqueue_script('pcl-admin-script', PCL_ADMIN_URL.'js/admin.js', array('jquery'), PCL_VERSION, true);

		wp_enqueue_style('pcl-admin-style', PCL_ADMIN_URL.'css/admin.css', '', PCL_VERSION);
	}

	/**
	 * update_settings function.
	 * 
	 * @access public
	 * @return void
	 */
	public function update_settings() {	
		if (!isset($_POST['pcl_admin_update']) || !wp_verify_nonce($_POST['pcl_admin_update'], 'update_settings'))
			return;
			
		$settings_data=$_POST['pcl_settings'];
			
		// update pages //
		$pages=wp_parse_args($settings_data['pages'], get_option('pcl_pages', array()));
		
		update_option('pcl_pages', $pages);

		// update redirects //
		foreach ($settings_data['redirect'] as $option => $url) :
			if ($url!='')
				update_option($option, $url);
		endforeach;

		// update admin bar //
		if (isset($settings_data['hide_admin_bar'])) :
			update_option('pcl-hide-admin-bar', 1);
		else :
			delete_option('pcl-hide-admin-bar');
		endif;

		// update admin bar //
		if (isset($settings_data['force_login'])) :
			update_option('pcl-force-login', 1);
		else :
			delete_option('pcl-force-login');
		endif;

		// update reCaptcha //
		if (isset($settings_data['enable_recaptcha'])) :
			update_option('pcl-enable-recaptcha', 1);
		else :
			delete_option('pcl-enable-recaptcha');
		endif;

		if ($settings_data['recaptcha_site_key']!='')
			update_option('pcl-recaptcha-site-key', $settings_data['recaptcha_site_key']);
	
		if ($settings_data['recaptcha_secret_key']!='')
			update_option('pcl-recaptcha-secret-key', $settings_data['recaptcha_secret_key']);

		// require activation key //
		if (isset($settings_data['require_activation_key'])) :
			update_option('pcl-require-activation-key', 1);
		else :
			delete_option('pcl-require-activation-key');
		endif;

		// require admin activation //
		if (isset($settings_data['require_admin_activation'])) :
			update_option('pcl-require-admin-activation', 1);
		else :
			delete_option('pcl-require-admin-activation');
		endif;

		$this->admin_notices['updated']='Settings Updated!';
	}

	/**
	 * update_emails function.
	 * 
	 * @access public
	 * @return void
	 */
	public function update_emails() {	
		if (!isset($_POST['pcl_admin_update']) || !wp_verify_nonce($_POST['pcl_admin_update'], 'update_emails'))
			return;

		// update retrieve password email //
		if (isset($_POST['retrieve_password_email']) && $_POST['retrieve_password_email']!='')
			update_option('pcl-retrieve-password-email', wp_kses_post($_POST['retrieve_password_email']));

		// update account creation email //
		if (isset($_POST['account_creation_email']) && $_POST['account_creation_email']!='')
			update_option('pcl-account-creation-email', wp_kses_post($_POST['account_creation_email']));

		// update account activation email //
		if (isset($_POST['account_activation_email']) && $_POST['account_activation_email']!='')
			update_option('pcl-account-activation-email', wp_kses_post($_POST['account_activation_email']));

		$this->admin_notices['updated']='Emails Updated!';
	}

	/**
	 * check_pages_on_trash function.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function check_pages_on_trash($post_id) {
		$pages=get_option('pcl_pages');

		foreach ($pages as $slug => $id) :
			if ($post_id==$id)
				$pages[$slug]=null;
		endforeach;

		update_option('pcl_pages',$pages);
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
	
	/**
	 * pcl_admin_dropdown_pages function.
	 * 
	 * @access public
	 * @param string $name (default: '')
	 * @param string $selected (default: '')
	 * @return void
	 */
	public function pcl_admin_dropdown_pages($name='', $selected='') {
		$args=array(
			'name' => "pcl_settings[pages][$name]",
			'id' => $name,
			'show_option_none' => '-- '.__('Choose One', 'pcl').' --',
			'selected' => $selected,
			'echo' => 0,
		);
		
		echo wp_dropdown_pages($args);
	}
	
	/**
	 * email_editor function.
	 * 
	 * @access public
	 * @param string $slug (default: '')
	 * @param string $id (default: '')
	 * @return void
	 */
	public function email_editor($slug='', $id='') {
		$content=stripslashes(get_option($slug, $this->default_email_content($id)));

		wp_editor($content, $id, array('media_buttons' => false));
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

		do_action('pcl_before_admin_'.$template_name);

		include(PCL_PATH.'admin/adminpages/'.$template_name.'.php');

		do_action('pcl_after_admin_'.$template_name);

		$html=ob_get_contents();

		ob_end_clean();

		return $html;
	}
	
}
?>