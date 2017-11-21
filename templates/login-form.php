<?php
/**
 * The template for displaying the login form
 *
 * This template can be overridden by copying it to yourtheme/pickle-custom-login/login-form.php.
 *
 */    
?>

<div class="pcl-login-form">
	<form id="pcl-login-form" class="pl-login-form" action="" method="post">
		<fieldset>
			<p>
				<label for="custom_user_login">Username</label>
				<input name="custom_user_login" id="custom_user_login" class="required" type="text" />
			</p>
			<p>
				<label for="custom_user_pass">Password</label>
				<input name="custom_user_pass" id="custom_user_pass" class="required" type="password" />
			</p>
			<p>
				<input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce('custom-login-nonce'); ?>" />
				<input id="custom_login_submit" type="submit" value="Login" />
			</p>
		</fieldset>
	</form>
	
	<?php pcl_login_extras(); ?>
	
</div>
