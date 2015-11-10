<div class="mdw-user-activation">
	<?php if (emcl_activate_user()) : ?>
		<div class="success">Account activated. Please click <a href="<?php echo home_url('/login/'); ?>">here</a> to login.
	<?php else : ?>
		<div class="mdw-user-activation error">You account could not be activated. Please try again or contact <a href="mailto:<?php echo get_option('admin_email'); ?>"><?php echo get_option('admin_email'); ?></a>
	<?php endif; ?>
</div>