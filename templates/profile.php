<?php
/**
 * Front end profile template
 *
 * Can be overriden
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

?>

<?php $currentuser = pcl_get_edit_profile_user(); ?>

<div class="pcl-profile">

    <?php if ( ! is_user_logged_in() ) : ?>

        <p class="warning">
            <?php esc_html_e( 'You must be logged in to edit your profile.', 'pcl' ); ?>
        </p><!-- .warning -->

    <?php elseif ( ! $currentuser ) : ?>

        <p class="warning">
            <?php esc_html_e( 'User not found, or you do not have permissions to edit this user.', 'pcl' ); ?>
        </p><!-- .warning -->
        
    
    <?php else : ?>
        <?php
        pcl_updated_profile_message();
        $hf_user = wp_get_current_user();
        $hf_username = $hf_user->user_login;
        ?>
            
        <h3 class="text-center">Update Info for <?php echo isset( $currentuser->first_name ) ? esc_html( $currentuser->first_name ) : ''; ?>  <?php echo isset( $currentuser->last_name ) ? esc_html( $currentuser->last_name ) : ''; ?>
        
        <form method="post" id="adduser" class="pcl-profile-form" action="" method="post">
                       
            <p class="form-username">
                <label for="firstname"><?php esc_html_e( 'First Name', 'pcl' ); ?></label>
                <input class="text-input" name="firstname" type="text" id="firstname" value="<?php the_author_meta( 'first_name', $currentuser->ID ); ?>" />
            </p>
            
            <p class="form-username">
                <label for="lastname"><?php esc_html_e( 'Last Name', 'pcl' ); ?></label>
                <input class="text-input" name="lastname" type="text" id="lastname" value="<?php the_author_meta( 'last_name', $currentuser->ID ); ?>" />
            </p>
            
            <p class="form-display_name">
                <?php
                $public_display = array();
                $public_display['display_nickname']  = $currentuser->nickname;
                $public_display['display_username']  = $currentuser->user_login;

                if ( ! empty( $currentuser->first_name ) ) {
                    $public_display['display_firstname'] = $currentuser->first_name;
                }

                if ( ! empty( $currentuser->last_name ) ) {
                    $public_display['display_lastname'] = $currentuser->last_name;
                }

                if ( ! empty( $currentuser->first_name ) && ! empty( $currentuser->last_name ) ) {
                    $public_display['display_firstlast'] = $currentuser->first_name . ' ' . $currentuser->last_name;
                    $public_display['display_lastfirst'] = $currentuser->last_name . ' ' . $currentuser->first_name;
                }

                if ( ! in_array( $currentuser->display_name, $public_display ) ) { // Only add this if it isn't duplicated elsewhere.
                    $public_display = array( 'display_displayname' => $currentuser->display_name ) + $public_display;
                }

                $public_display = array_map( 'trim', $public_display );
                $public_display = array_unique( $public_display );
                ?>
                       
                <label for="display_name"><?php esc_html_e( 'Display name publicly as', 'pcl' ); ?></label>
        
                <select name="display_name" id="display_name">                    
                    <?php foreach ( $public_display as $item_id => $item ) : ?>
                        <option value="<?php echo esc_attr( $item_id ); ?>"><?php echo esc_html( $item ); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <?php do_action( 'edit_user_profile', $currentuser ); ?>
                        
            <p class="form-email">
                <label for="email"><?php esc_html_e( 'E-mail *', 'pcl' ); ?></label>
                <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'useresc_html_email', $currentuser->ID ); ?>" />
            </p>
            
            <p class="form-url">
                <label for="url"><?php esc_html_e( 'Website', 'pcl' ); ?></label>
                <input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta( 'user_url', $currentuser->ID ); ?>" />
            </p>
            
            <p class="form-password">
                <label for="password"><?php esc_html_e( 'Password *', 'pcl' ); ?> </label>
                <input class="text-input" name="password" type="password" id="password" />
            </p>
            
            <p class="form-password">
                <label for="password_check"><?php esc_html_e( 'Repeat Password *', 'pcl' ); ?></label>
                <input class="text-input" name="password_check" type="password" id="password_check" />
            </p>
            
            <p class="form-textarea">
                <label for="description"><?php esc_html_e( 'Biographical Information', 'pcl' ); ?></label>
                <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $currentuser->ID ); ?></textarea>
            </p>
    
            <?php do_action( 'edit_user_profile', $currentuser ); ?>

            <p class="form-submit">                
                <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php esc_html_e( 'Update', 'pcl' ); ?>" />
                
                <?php wp_nonce_field( 'update-user_' . $currentuser->ID, 'pcl_update_profile', true ); ?>

                <input name="action" type="hidden" id="action" value="update-user" />
            </p>
        
        </form>
        
    <?php endif; ?>

</div>
