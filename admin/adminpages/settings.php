<?php
$pages=get_option('pcl_pages');
$settings=array(
	'media_buttons' => false,
);
$require_activation_key=get_option('pcl-require-activation-key', 0);
$require_activation_key_sub_classes='hide-if-js';
$hide_admin_bar=get_option('pcl-hide-admin-bar', false);
$enable_recaptcha=get_option('pcl-enable-recaptcha', false);

if ($require_activation_key)
	$require_activation_key_sub_classes='';
?>

<div class="custom-login-admin wrap">
	<h1>Pickle Custom Login</h1>

	<form method="post" action="" method="post">
		<input type="hidden" name="custom_login_admin" value="update">
		<input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
		<?php wp_referer_field(); ?>

		<h3 class="title"><?php _e('Pages','pcl'); ?></h3>

		<table class="form-table pages">
			<tbody>
				<tr>
					<th scope="row"><label for="login_page"><?php _e('Login Page','pcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "login_page", "show_option_none" => "-- " . __('Choose One', 'pcl') . " --", "selected" => $pages['login'])); ?>
						<p class="description"><?php _e('Include the shortcode','pcl'); ?> [pcl-login-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="register_page"><?php _e('Registration Page','pcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "register_page", "show_option_none" => "-- " . __('Choose One', 'pcl') . " --", "selected" => $pages['register'])); ?>
						<p class="description"><?php _e('Include the shortcode','pcl'); ?> [pcl-registration-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="forgot_password_page"><?php _e('Forgot Password Page','pcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "forgot_password_page", "show_option_none" => "-- " . __('Choose One', 'pcl') . " --", "selected" => $pages['forgot-password'])); ?>
						<p class="description"><?php _e('Include the shortcode','pcl'); ?> [pcl-forgot-password-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="reset_page"><?php _e('Reset Password Page','pcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "reset_page", "show_option_none" => "-- " . __('Choose One', 'pcl') . " --", "selected" => $pages['reset-password'])); ?>
						<p class="description"><?php _e('Include the shortcode','pcl'); ?> [pcl-reset-password-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="activate_page"><?php _e('Activation Page','pcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "activate_page", "show_option_none" => "-- " . __('Choose One', 'pcl') . " --", "selected" => $pages['activate-account'])); ?>
						<p class="description"><?php _e('Include the shortcode','pcl'); ?> [pcl-user-activation]</p>
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('Redirects', 'pcl'); ?></h3>

		<table class="form-table redirects">
			<tbody>

				<tr>
					<th scope="row"><label for="redirect_users"><?php _e('Redirect Users','pcl'); ?></label></th>
					<td>
						<input name="redirect_users" type="url" id="redirect_users" value="<?php echo get_option('pcl-login-redirect',home_url()); ?>" class="regular-text code">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="redirect_after_registration"><?php _e('Redirect Users After Registration','pcl'); ?></label></th>
					<td>
						<input name="redirect_after_registration" type="url" id="redirect_after_registration" value="<?php echo get_option('pcl-register-redirect',home_url()); ?>" class="regular-text code">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="redirect_after_logout"><?php _e('Redirect Users After Logout', 'pcl'); ?></label></th>
					<td>
						<input name="redirect_after_logout" type="url" id="redirect_after_logout" value="<?php echo get_option('pcl-logout-redirect', home_url()); ?>" class="regular-text code">
					</td>
				</tr>

			</tbody>
		</table>

		<h3 class="title"><?php _e('General', 'pcl'); ?></h3>

		<table class="form-table general">
			<tbody>
				<tr>
					<th scope="row"><label for="hide_admin_bar"><?php _e('Hide Admin Bar','pcl'); ?></label></th>
					<td>
						<input name="hide_admin_bar" type="checkbox" id="hide_admin_bar" value="1" <?php checked($hide_admin_bar,1); ?>>
						<p class="description" id="hide_admin_bar_description"><?php _e('If checked, the admin bar would be hidden for non administrators.','pcl'); ?></p></td>
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('reCaptcha', 'pcl'); ?></h3>

		<table class="form-table general">
			<tbody>
				<tr>
					<th scope="row"><label for="enable_recaptcha"><?php _e('Enable reCaptcha','pcl'); ?></label></th>
					<td>
						<input name="enable_recaptcha" type="checkbox" id="enable_recaptcha" value="1" <?php checked($enable_recaptcha,1); ?>>
						<p class="description" id="enable_recaptcha_description"><?php _e('Enable reCaptcha form on registration page.','pcl'); ?></p></td>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_site_key"><?php _e('Site Key','pcl'); ?></label></th>
					<td>
						<input name="recaptcha_site_key" type="text" id="recaptcha_site_key" value="<?php echo get_option('pcl-recaptcha-site-key',''); ?>" class="regular-text code">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_secret_key"><?php _e('Secret Key</label','pcl'); ?>></th>
					<td>
						<input name="recaptcha_secret_key" type="text" id="recaptcha_secret_key" value="<?php echo get_option('pcl-recaptcha-secret-key',''); ?>" class="regular-text code">
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('Customize Emails', 'pcl'); ?></h3>

		<table class="form-table customize-emails">
			<tbody>
				<tr>
					<th scope="row"><label for="retrieve_password_email"><?php _e('Reset Password Email', 'pcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('pcl-retrieve-password-email', $this->default_email_content('retrieve_password_email'))), 'retrieve_password_email', $settings); ?>
					</td>
				</tr>
				<tr class="hide-if-activation-key">
					<th scope="row"><label for="account_creation_email"><?php _e('Retrieve Password Email', 'pcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('pcl-account-creation-email', $this->default_email_content('account_creation_email'))), 'account_creation_email', $settings); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="require_activation_key"><?php _e('Require Account Activation','pcl'); ?></label></th>
					<td>
						<input name="require_activation_key" type="checkbox" id="require_activation_key" value="1" <?php checked($require_activation_key,1); ?>>
						<p class="description" id="rquire-activation-key-description">If checked, users would receive an email to activate their account before they can login.</p></td>
					</td>
				</tr>
				<tr class="require_activation_key_sub <?php echo $require_activation_key_sub_classes; ?>">
					<th scope="row"><label for="account_activation_email"><?php _e('Account Creation Email','pcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('pcl-account-activation-email',$this->default_email_content('account_activation_email'))),'account_activation_email',$settings); ?>
						<p class="description"><?php _e('When "Require Account Activation" is active.','pcl'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','pcl'); ?>"></p>
	</form>
</div><!-- .wrap -->