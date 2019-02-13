<div id="custom-lost-password-form" class="custom-lost-password-form">
    <h3><?php _e( 'Forgot Your Password?', 'pcl' ); ?></h3>

    <p><?php _e( "Enter your email address and we'll send you a link you can use to pick a new password.", 'pcl' ); ?></p>

    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="form-row">
            <label for="user_login"><?php _e( 'Email', 'pcl' ); ?>
            <input type="text" name="user_login" id="user_login">
        </p>
        
        <?php if ( get_option( 'pcl-enable-recaptcha', false ) ) : ?>
            <p class="form-row">
                <div class="g-recaptcha" data-sitekey="<?php echo get_option( 'pcl-recaptcha-site-key', '' ); ?>"></div>
            </p>
        <?php endif; ?>

        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button" value="<?php _e( 'Reset Password', 'pcl' ); ?>" />
        </p>
    </form>
</div>
