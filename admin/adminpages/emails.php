<?php
/**
 * Admin emails page
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

?>

<h2>Emails</h2>

<form method="post" action="" method="post">
    <?php wp_nonce_field( 'updateesc_html_emails', 'pcl_admin_update' ); ?>
    
    <table class="form-table customize-emails">
        <tbody>
            <tr>
                <th scope="row"><label for="retrieve_passwordesc_html_email"><?php esc_html_e( 'Reset Password Email', 'pcl' ); ?></label></th>
                <td>
                    <?php pickle_custom_login()->admin->email_editor( 'pcl-retrieve-password-email', 'retrieve_passwordesc_html_email' ); ?>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="account_creationesc_html_email"><?php esc_html_e( 'Retrieve Password Email', 'pcl' ); ?></label></th>
                <td>
                    <?php pickle_custom_login()->admin->email_editor( 'pcl-account-creation-email', 'account_creationesc_html_email' ); ?>
                </td>
            </tr>
    
            <tr>
                <th scope="row"><label for="account_activationesc_html_email"><?php esc_html_e( 'Account Creation Email', 'pcl' ); ?></label></th>
                <td>
                    <?php pickle_custom_login()->admin->email_editor( 'pcl-account-activation-email', 'account_activationesc_html_email' ); ?>
                    <p class="description"><?php esc_html_e( 'When "Require Account Activation" is active.', 'pcl' ); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="admin_activationesc_html_email"><?php esc_html_e( 'Admin Activation Email', 'pcl' ); ?></label></th>
                <td>
                    <?php pickle_custom_login()->admin->email_editor( 'pcl-admin-activation-email', 'admin_activationesc_html_email' ); ?>
                    <p class="description"><?php esc_html_e( 'When "Require Admin Account Activation" is active.', 'pcl' ); ?></p>
                </td>
            </tr>           
        </tbody>
    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'pcl' ); ?>"></p>
</form>
