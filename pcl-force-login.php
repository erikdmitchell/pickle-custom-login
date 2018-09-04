<?php

/**
 * pcl_force_login_redirect function.
 *
 * @access public
 * @return void
 */
function pcl_force_login_redirect() {
    // Exceptions for AJAX, Cron, or WP-CLI requests
    if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        return;
    }

    if ( isset( $_GET['redirect_to'] ) ) {
        return;
    }

    // Redirect unauthorized visitors
    if ( ! is_user_logged_in() && pcl_force_Login() ) :
        // set vars.
        $is_regex = false;
        
        // get url //
        $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
        $url .= '://' . $_SERVER['HTTP_HOST'];
        $url .= $_SERVER['REQUEST_URI'];

        // Apply filters //
        $bypass = apply_filters( 'pcl_force_login_bypass', false );
        $whitelist = apply_filters( 'pcl_force_login_whitelist', array() );
        $whitelist = array_map( 'pcl_force_login_trim_url', $whitelist ); // remove trailing slash.
        $redirect_url = apply_filters( 'pcl_force_login_redirect', $url );
        $regex = apply_filters( 'pcl_force_login_regex', array() );

        // setup raw urls for cleaner comparison //
        $redirect_url_clean = rtrim( preg_replace( '/\?.*/', '', $redirect_url ), '/' );
        $login_url_clean = preg_replace( '/\?.*/', '', wp_login_url() );
        $url_clean = preg_replace( '/\?.*/', '', $url );

        // setup regex if need be.
        if (!empty($regex)) :
            foreach ($regex as $reg) :
                if (preg_match($reg, pcl_force_login_trim_url( $url_clean ))) :
                    $is_regex = true;
                    break;
                endif;
            endforeach;
        endif;

        // check and redirect //
        if ( $redirect_url_clean != $login_url_clean && ! in_array( pcl_force_login_trim_url( $url_clean ), $whitelist ) && ! $bypass && !$is_regex) :
            wp_safe_redirect( wp_login_url(), 302 );
            exit();
        endif;
    endif;
}
add_action( 'template_redirect', 'pcl_force_login_redirect' );

/**
 * Removes the trailing slash from a url.
 *
 * @access public
 * @param string $url (default: '')
 * @return void
 */
function pcl_force_login_trim_url( $url = '' ) {
    return rtrim( $url, '/' );
}
