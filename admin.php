<?php
/**
 * EMCustomLoginAdmin class.
 *
 * @sonce 0.1.0
 */
class EMCustomLoginAdmin {

	protected $admin_notices=array();

	public function __construct() {
		add_action('admin_menu',array($this,'admin_menu'));
		add_action('admin_notices',array($this,'admin_notices'));
		add_action('admin_enqueue_scripts',array($this,'admin_scripts_styles'));
		add_action('init',array($this,'update_admin_settings'));
		//add_action('wp_ajax_send_test_email',array($this,'ajax_send_test_emails'));
	}

	public function admin_menu() {
		add_options_page('EM Custom Login','EM Custom Login','manage_options','em_custom_login',array($this,'admin_page'));
	}

	public function admin_scripts_styles($hook) {
		if ($hook!='settings_page_em_custom_login')
			return false;

		wp_enqueue_script('emcl-admin-script',plugins_url('/js/admin.js',__FILE__),array('jquery'));
	}

	public function admin_page() {
		$settings=array(
			'media_buttons' => false,
		);
		$require_activation_key=get_option('emcl-require-activation-key',0);
		?>
		<div class="wrap">
			<h1>EM Custom Login</h1>

			<p>custom emails, add option for activation key</p>

			<form method="post" action="" method="post">
				<input type="hidden" name="custom_login_admin" value="update">
				<input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
				<?php wp_referer_field(); ?>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="retrieve-password-email">Retrieve Password Email</label></th>
							<td>
								<?php wp_editor(stripslashes(get_option('emcl-retrieve-password-email',$this->default_email_content('retrieve_password_email'))),'retrieve_password_email',$settings); ?>
								<!-- <p class="submit"><input type="button" name="send_retrieve_password_email" id="send_retrieve_password_email" class="button send-demo-email" data-type="retrieve_password_email" value="Send Demo Email"></p> -->
							</td>
						</tr>
						<!--
						<tr>
							<th scope="row"><label for="blogdescription">Tagline</label></th>
							<td><input name="blogdescription" type="text" id="blogdescription" aria-describedby="tagline-description" value="The Ultimate WP Test Site but this Description Needs to be Even longer - what is up with the weird capitalization" class="regular-text">
							<p class="description" id="tagline-description">In a few words, explain what this site is about.</p></td>
						</tr>
						-->
						<tr>
							<th scope="row"><label for="require_activation_key">Require Account Activation</label></th>
							<td>
								<input name="require_activation_key" type="checkbox" id="require_activation_key" value="1" <?php checked($require_activation_key,1); ?>>
								<p class="description" id="rquire-activation-key-description">If checked, users would receive an email to activate their account before they can login.</p></td>
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

			// update retrieve password email //
			if (isset($_POST['retrieve_password_email']) && $_POST['retrieve_password_email']!='')
				update_option('emcl-retrieve-password-email',wp_kses_post($_POST['retrieve_password_email']));

			// require activation key //
			if (isset($_POST['require_activation_key'])) :
				update_option('emcl-require-activation-key',$_POST['require_activation_key']);
			else :
				delete_option('emcl-require-activation-key');
			endif;

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
	 * ajax_send_test_emails function.
	 *
	 * @access public
	 * @return void
	 */
/*
	public function ajax_send_test_emails() {
		$type='';

		if (isset($_POST['type']))
			$type=$_POST['type'];

		if (!function_exists('retrieve_password'))
			include_once('/Users/erik/Sites/wordpress/wp-login.php');

		switch ($type) :
			case 'retrieve_password_email' :
				retrieve_password(); // wp function
				break;
			default:
				break;
		endswitch;

		return;
	}
*/

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
			case 'retrieve_password_email' :
				$content.="Hello!\r\n\r\n";
				$content.="You asked us to reset your password for your account using the email address {username}\r\n\r\n"; // $user_login
				$content.="If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.\r\n\r\n";
				$content.="To reset your password, visit the following address:\r\n\r\n";
				$content.="{password_reset}\r\n\r\n";
				//$contet.=site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login),'login')."\r\n\r\n";
				$content.="Thanks!\r\n\r\n";
				break;
			default:
				break;
		endswitch;

		return $content;
	}

}

$EMCustomLoginAdmin=new EMCustomLoginAdmin();
?>