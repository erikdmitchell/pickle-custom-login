<?php
/**
 * Reset password template
 *
 * Can be overriden
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

?>

<div id="password-reset-form" class="password-reset-form">
    <h3><?php esc_html_e( 'Pick a New Password', 'pcl' ); ?></h3>

    <form name="resetpassform" id="resetpassform" action="<?php echo esc_html( site_url( 'wp-login.php?action=resetpass' ) ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( isset( $_REQUEST['login'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['login'] ) ) : '' ); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( isset( $_REQUEST['key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ) : '' ); ?>" />

        <p>
            <label for="pass1"><?php esc_html_e( 'New password', 'pcl' ); ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2"><?php esc_html_e( 'Repeat new password', 'pcl' ); ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p>

        <p class="description"><?php echo esc_html( wp_get_password_hint() ); ?></p>

        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button" class="button" value="<?php esc_html_e( 'Reset Password', 'pcl' ); ?>" />
        </p>
    </form>
</div>
