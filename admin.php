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
		add_action('init',array($this,'update_admin_settings'));
	}

	public function admin_menu() {
		add_options_page('EM Custom Login','EM Custom Login','manage_options','em_custom_login',array($this,'admin_page'));
	}

	public function admin_page() {
		$settings=array(
			'media_buttons' => false,

		);
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
							</td>
						</tr>
						<!--
						<tr>
							<th scope="row"><label for="blogdescription">Tagline</label></th>
							<td><input name="blogdescription" type="text" id="blogdescription" aria-describedby="tagline-description" value="The Ultimate WP Test Site but this Description Needs to be Even longer - what is up with the weird capitalization" class="regular-text">
							<p class="description" id="tagline-description">In a few words, explain what this site is about.</p></td>
						</tr>
						-->
						<!--
						<tr>
							<th scope="row">Membership</th>
							<td>
								<fieldset><legend class="screen-reader-text"><span>Membership</span></legend><label for="users_can_register">
									<input name="users_can_register" type="checkbox" id="users_can_register" value="1" checked="checked">
									Anyone can register</label>
								</fieldset>
							</td>
						</tr>
						-->
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	public function update_admin_settings() {
		if (isset($_POST['custom_login_admin']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom-login-nonce')) :

			// update retrieve password email //
			if (isset($_POST['retrieve_password_email']) && $_POST['retrieve_password_email']!='')
				update_option('emcl-retrieve-password-email',wp_kses_post($_POST['retrieve_password_email']));


			$this->admin_notices['updated']='Settings Updated!';
		endif;
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

new EMCustomLoginAdmin();
?>