<?php
/**
 * The template for displaying the login form
 *
 * This template can be overridden by copying it to yourtheme/pickle-custom-login/login-form.php.
 */
?>

<div class="pcl-login-form">

    <form id="pcl-login-form" class="pl-login-form" action="" method="post">
        <fieldset>
            <h3>Login</h3>
            <p>
                <label for="custom_user_login">Username</label>
                <input name="custom_user_login" id="custom_user_login" class="required" type="text" />
            </p>
            <p>
                <label for="custom_user_pass">Password</label>
                <input name="custom_user_pass" id="custom_user_pass" class="required" type="password" />
            </p>
            <p>
                <input type="hidden" name="custom_login_nonce" value="<?php echo wp_create_nonce( 'custom-login-nonce' ); ?>" />
                <input id="custom_login_submit" type="submit" value="Login" />
            </p>
            
            <p>
                <label for="rememberme"><input name="rememberme" type="checkbox" id="pcl-rememberme" value="1" /> Remember Me</label>
                <p><a href="/wp-login.php?action=lostpassword" title="Lost Password?">Lost Password?</a></p>
            
                <div class="required-text">
                    <a href="https://boomi.com/privacy/" target="_blank">Privacy Policy</a>
                </div>
        </fieldset>
    </form>

        <div class="wp-register text-center mt-5">
        <h5 class="text-center">Don't have an account? Register below for access.</h5>
    <a href="/register/" class="btn btn-primary">Register</a>
    </div>
    
    <?php pcl_login_extras(); ?>
    
</div>
