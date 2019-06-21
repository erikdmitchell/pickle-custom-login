<?php
/**
 * Forgot password template
 *
 * Can be overriden
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

?>

<div id="custom-lost-password-form" class="custom-lost-password-form">
    <h3><?php esc_html_e( 'Forgot Your Password?', 'pcl' ); ?></h3>

    <p><?php esc_html_e( "Enter your email address and we'll send you a link you can use to pick a new password.", 'pcl' ); ?></p>

    <form id="lostpasswordform" action="<?php echo esc_html( wp_lostpassword_url() ); ?>" method="post">
        <p class="form-row">
            <label for="user_login"><?php esc_html_e( 'Email', 'pcl' ); ?>
            <input type="text" name="user_login" id="user_login">
        </p>
        
        <?php if ( get_option( 'pcl-enable-recaptcha', false ) ) : ?>
            <p class="form-row">
                <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option( 'pcl-recaptcha-site-key', '' ) ); ?>"></div>
            </p>
        <?php endif; ?>

        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button" value="<?php esc_html_e( 'Reset Password', 'pcl' ); ?>" />
        </p>
    </form>
</div>
