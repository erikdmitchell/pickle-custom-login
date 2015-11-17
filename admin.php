<?php
/**
 * EMCustomLoginAdmin class.
 *
 * @sonce 0.1.0
 */
class EMCustomLoginAdmin {

	protected $admin_notices=array();
	//public $default_pages=array();

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('admin_menu',array($this,'admin_menu'));
		add_action('admin_notices',array($this,'admin_notices'));
		add_action('admin_enqueue_scripts',array($this,'admin_scripts_styles'));
		add_action('init',array($this,'update_admin_settings'));
		add_action('wp_trash_post',array($this,'check_emcl_pages_on_trash'));
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

		wp_enqueue_script('emcl-admin-script',plugins_url('/js/admin.js',__FILE__),array('jquery'));

		wp_enqueue_style('emcl-admin-style',plugins_url('/css/admin.css',__FILE__));
	}

	/**
	 * admin_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_page() {
		$pages=get_option('emcl-pages');
		$settings=array(
			'media_buttons' => false,
		);
		$require_activation_key=get_option('emcl-require-activation-key',0);
		$require_activation_key_sub_classes='hide-if-js';

		if ($require_activation_key)
			$require_activation_key_sub_classes='';
		?>
		<div class="custom-login-admin wrap">
			<h1>EM Custom Login</h1>

			<form method="post" action="" method="post">
				<input type="hidden" name="custom_login_admin" value="update">
				<input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
				<?php wp_referer_field(); ?>

				<h3 class="title">Pages</h3>

				<table class="form-table pages">
					<tbody>
						<tr>
							<th scope="row"><label for="login_page">Login Page</label></th>
							<td>
								<?php wp_dropdown_pages(array("name" => "login_page", "show_option_none" => "-- " . __('Choose One', 'dummy') . " --", "selected" => $pages['login'])); ?>
								<p class="description"><?php _e('Include the shortcode','dummy'); ?> [emcl-login-form]</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="register_page">Registration Page</label></th>
							<td>
								<?php wp_dropdown_pages(array("name" => "register_page", "show_option_none" => "-- " . __('Choose One', 'dummy') . " --", "selected" => $pages['register'])); ?>
								<p class="description"><?php _e('Include the shortcode','dummy'); ?> [emcl-registration-form]</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="forgot_password_page">Forgot Password Page</label></th>
							<td>
								<?php wp_dropdown_pages(array("name" => "forgot_password_page", "show_option_none" => "-- " . __('Choose One', 'dummy') . " --", "selected" => $pages['forgot-password'])); ?>
								<p class="description"><?php _e('Include the shortcode','dummy'); ?> [emcl-forgot-password-form]</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="reset_page">Reset Password Page</label></th>
							<td>
								<?php wp_dropdown_pages(array("name" => "reset_page", "show_option_none" => "-- " . __('Choose One', 'dummy') . " --", "selected" => $pages['reset-password'])); ?>
								<p class="description"><?php _e('Include the shortcode','dummy'); ?> [emcl-reset-password-form]</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="activate_page">Activation Page</label></th>
							<td>
								<?php wp_dropdown_pages(array("name" => "activate_page", "show_option_none" => "-- " . __('Choose One', 'dummy') . " --", "selected" => $pages['activate-account'])); ?>
								<p class="description"><?php _e('Include the shortcode','dummy'); ?> [emcl-user-activation]</p>
							</td>
						</tr>
					</tbody>
				</table>

				<h3 class="title">Redirects</h3>

				<table class="form-table redirects">
					<tbody>
						<tr>
							<th scope="row"><label for="login_page">Redirect Users</label></th>
							<td>
								<input name="redirect_users" type="url" id="redirect_users" value="<?php echo get_option('emcl-login-redirect',home_url()); ?>" class="regular-text code">
							</td>
						</tr>
					</tbody>
				</table>

				<h3 class="title">Customize Emails</h3>

				<table class="form-table customize-emails">
					<tbody>
						<tr>
							<th scope="row"><label for="retrieve_password_email">Retrieve Password Email</label></th>
							<td>
								<?php wp_editor(stripslashes(get_option('emcl-retrieve-password-email',$this->default_email_content('retrieve_password_email'))),'retrieve_password_email',$settings); ?>
							</td>
						</tr>
						<tr class="hide-if-activation-key">
							<th scope="row"><label for="account_creation_email">Retrieve Password Email</label></th>
							<td>
								<?php wp_editor(stripslashes(get_option('emcl-account-creation-email',$this->default_email_content('account_creation_email'))),'account_creation_email',$settings); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="require_activation_key">Require Account Activation</label></th>
							<td>
								<input name="require_activation_key" type="checkbox" id="require_activation_key" value="1" <?php checked($require_activation_key,1); ?>>
								<p class="description" id="rquire-activation-key-description">If checked, users would receive an email to activate their account before they can login.</p></td>
							</td>
						</tr>
						<tr class="require_activation_key_sub <?php echo $require_activation_key_sub_classes; ?>">
							<th scope="row"><label for="account_activation_email">Account Creation Email</label></th>
							<td>
								<?php wp_editor(stripslashes(get_option('emcl-account-activation-email',$this->default_email_content('account_activation_email'))),'account_activation_email',$settings); ?>
								<p class="description">When "Require Account Activation" is active.</p>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>
		</div><!-- .wrap -->
		<?php
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

}

$EMCustomLoginAdmin=new EMCustomLoginAdmin();
?>