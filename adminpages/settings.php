<?php
$pages=get_option('emcl-pages');
$settings=array(
	'media_buttons' => false,
);
$require_activation_key=get_option('emcl-require-activation-key', 0);
$require_activation_key_sub_classes='hide-if-js';
$hide_admin_bar=get_option('emcl-hide-admin-bar', false);
$enable_recaptcha=get_option('emcl-enable-recaptcha', false);

if ($require_activation_key)
	$require_activation_key_sub_classes='';
?>

<div class="custom-login-admin wrap">
	<h1>EM Custom Login</h1>

	<form method="post" action="" method="post">
		<input type="hidden" name="custom_login_admin" value="update">
		<input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
		<?php wp_referer_field(); ?>

		<h3 class="title"><?php _e('Pages','emcl'); ?></h3>

		<table class="form-table pages">
			<tbody>
				<tr>
					<th scope="row"><label for="login_page"><?php _e('Login Page','emcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "login_page", "show_option_none" => "-- " . __('Choose One', 'emcl') . " --", "selected" => $pages['login'])); ?>
						<p class="description"><?php _e('Include the shortcode','emcl'); ?> [emcl-login-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="register_page"><?php _e('Registration Page','emcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "register_page", "show_option_none" => "-- " . __('Choose One', 'emcl') . " --", "selected" => $pages['register'])); ?>
						<p class="description"><?php _e('Include the shortcode','emcl'); ?> [emcl-registration-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="forgot_password_page"><?php _e('Forgot Password Page','emcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "forgot_password_page", "show_option_none" => "-- " . __('Choose One', 'emcl') . " --", "selected" => $pages['forgot-password'])); ?>
						<p class="description"><?php _e('Include the shortcode','emcl'); ?> [emcl-forgot-password-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="reset_page"><?php _e('Reset Password Page','emcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "reset_page", "show_option_none" => "-- " . __('Choose One', 'emcl') . " --", "selected" => $pages['reset-password'])); ?>
						<p class="description"><?php _e('Include the shortcode','emcl'); ?> [emcl-reset-password-form]</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="activate_page"><?php _e('Activation Page','emcl'); ?></label></th>
					<td>
						<?php wp_dropdown_pages(array("name" => "activate_page", "show_option_none" => "-- " . __('Choose One', 'emcl') . " --", "selected" => $pages['activate-account'])); ?>
						<p class="description"><?php _e('Include the shortcode','emcl'); ?> [emcl-user-activation]</p>
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('Redirects', 'emcl'); ?></h3>

		<table class="form-table redirects">
			<tbody>

				<tr>
					<th scope="row"><label for="redirect_users"><?php _e('Redirect Users','emcl'); ?></label></th>
					<td>
						<input name="redirect_users" type="url" id="redirect_users" value="<?php echo get_option('emcl-login-redirect',home_url()); ?>" class="regular-text code">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="redirect_after_registration"><?php _e('Redirect Users After Registration','emcl'); ?></label></th>
					<td>
						<input name="redirect_after_registration" type="url" id="redirect_after_registration" value="<?php echo get_option('emcl-register-redirect',home_url()); ?>" class="regular-text code">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="redirect_after_logout"><?php _e('Redirect Users After Logout', 'emcl'); ?></label></th>
					<td>
						<input name="redirect_after_logout" type="url" id="redirect_after_logout" value="<?php echo get_option('emcl-logout-redirect', home_url()); ?>" class="regular-text code">
					</td>
				</tr>

			</tbody>
		</table>

		<h3 class="title"><?php _e('General', 'emcl'); ?></h3>

		<table class="form-table general">
			<tbody>
				<tr>
					<th scope="row"><label for="hide_admin_bar"><?php _e('Hide Admin Bar','emcl'); ?></label></th>
					<td>
						<input name="hide_admin_bar" type="checkbox" id="hide_admin_bar" value="1" <?php checked($hide_admin_bar,1); ?>>
						<p class="description" id="hide_admin_bar_description"><?php _e('If checked, the admin bar would be hidden for non administrators.','emcl'); ?></p></td>
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('reCaptcha', 'emcl'); ?></h3>

		<table class="form-table general">
			<tbody>
				<tr>
					<th scope="row"><label for="enable_recaptcha"><?php _e('Enable reCaptcha','emcl'); ?></label></th>
					<td>
						<input name="enable_recaptcha" type="checkbox" id="enable_recaptcha" value="1" <?php checked($enable_recaptcha,1); ?>>
						<p class="description" id="enable_recaptcha_description"><?php _e('Enable reCaptcha form on registration page.','emcl'); ?></p></td>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_site_key"><?php _e('Site Key','emcl'); ?></label></th>
					<td>
						<input name="recaptcha_site_key" type="text" id="recaptcha_site_key" value="<?php echo get_option('emcl-recaptcha-site-key',''); ?>" class="regular-text code">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_secret_key"><?php _e('Secret Key</label','emcl'); ?>></th>
					<td>
						<input name="recaptcha_secret_key" type="text" id="recaptcha_secret_key" value="<?php echo get_option('emcl-recaptcha-secret-key',''); ?>" class="regular-text code">
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="title"><?php _e('Customize Emails', 'emcl'); ?></h3>

		<table class="form-table customize-emails">
			<tbody>
				<tr>
					<th scope="row"><label for="retrieve_password_email"><?php _e('Reset Password Email', 'emcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('emcl-retrieve-password-email',$this->default_email_content('retrieve_password_email'))),'retrieve_password_email',$settings); ?>
					</td>
				</tr>
				<tr class="hide-if-activation-key">
					<th scope="row"><label for="account_creation_email"><?php _e('Retrieve Password Email', 'emcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('emcl-account-creation-email',$this->default_email_content('account_creation_email'))),'account_creation_email',$settings); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="require_activation_key"><?php _e('Require Account Activation','emcl'); ?></label></th>
					<td>
						<input name="require_activation_key" type="checkbox" id="require_activation_key" value="1" <?php checked($require_activation_key,1); ?>>
						<p class="description" id="rquire-activation-key-description">If checked, users would receive an email to activate their account before they can login.</p></td>
					</td>
				</tr>
				<tr class="require_activation_key_sub <?php echo $require_activation_key_sub_classes; ?>">
					<th scope="row"><label for="account_activation_email"><?php _e('Account Creation Email','emcl'); ?></label></th>
					<td>
						<?php wp_editor(stripslashes(get_option('emcl-account-activation-email',$this->default_email_content('account_activation_email'))),'account_activation_email',$settings); ?>
						<p class="description"><?php _e('When "Require Account Activation" is active.','emcl'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','emcl'); ?>"></p>
	</form>
</div><!-- .wrap -->