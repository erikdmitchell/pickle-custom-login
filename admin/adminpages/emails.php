<h2>Emails</h2>

<form method="post" action="" method="post">
	<?php wp_nonce_field('update_emails', 'pcl_admin_update'); ?>
	
	<table class="form-table customize-emails">
		<tbody>
			<tr>
				<th scope="row"><label for="retrieve_password_email"><?php _e('Reset Password Email', 'pcl'); ?></label></th>
				<td>
					<?php pickle_custom_login()->admin->email_editor('pcl-retrieve-password-email', 'retrieve_password_email'); ?>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="account_creation_email"><?php _e('Retrieve Password Email', 'pcl'); ?></label></th>
				<td>
					<?php pickle_custom_login()->admin->email_editor('pcl-account-creation-email', 'account_creation_email'); ?>
				</td>
			</tr>
	
			<tr>
				<th scope="row"><label for="account_activation_email"><?php _e('Account Creation Email', 'pcl'); ?></label></th>
				<td>
					<?php pickle_custom_login()->admin->email_editor('pcl-account-activation-email', 'account_activation_email'); ?>
					<p class="description"><?php _e('When "Require Account Activation" is active.', 'pcl'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'pcl'); ?>"></p>
</form>