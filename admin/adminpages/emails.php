<h2>Emails</h2>

$require_activation_key_sub_classes='hide-if-js';



if ($require_activation_key)
	$require_activation_key_sub_classes='';
	
	pcl_require_activation_key()
	
	$settings=array(
	'media_buttons' => false,
);

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
			<th scope="row"><label for="require_activation_key"><?php _e('Require Account Activation', 'pcl'); ?></label></th>
			<td>
				<input name="require_activation_key" type="checkbox" id="require_activation_key" value="1" <?php checked($require_activation_key,1); ?>>
				<p class="description" id="rquire-activation-key-description">If checked, users would receive an email to activate their account before they can login.</p></td>
			</td>
		</tr>
		<tr class="require_activation_key_sub <?php echo $require_activation_key_sub_classes; ?>">
			<th scope="row"><label for="account_activation_email"><?php _e('Account Creation Email', 'pcl'); ?></label></th>
			<td>
				<?php wp_editor(stripslashes(get_option('pcl-account-activation-email',$this->default_email_content('account_activation_email'))),'account_activation_email',$settings); ?>
				<p class="description"><?php _e('When "Require Account Activation" is active.', 'pcl'); ?></p>
			</td>
		</tr>
	</tbody>
</table>