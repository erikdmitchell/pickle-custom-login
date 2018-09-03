<?php $current_user = pcl_get_edit_profile_user(); ?>

<div class="pcl-profile">

    <?php if (!is_user_logged_in()) : ?>

        <p class="warning">
            <?php _e('You must be logged in to edit your profile.', 'pcl'); ?>
        </p><!-- .warning -->

    <?php elseif (!$current_user) : ?>

        <p class="warning">
            <?php _e('User not found, or you do not have permissions to edit this user.', 'pcl'); ?>
        </p><!-- .warning -->
        
    
    <?php else : ?>
        <?php pcl_updated_profile_message();
        $hf_user = wp_get_current_user();
        $hf_username = $hf_user->user_login;
        //echo "here: " . wp_get_referer() . "<br>";
        if (wp_get_referer()=="http://dev-partnerhub.pantheonsite.io/wp-admin/users.php") {
            $page = '/wp-admin/users.php';
        } else {
            $page = '/wp-admin/options-general.php?page=pickle_custom_login&tab=approve_users';
        }
        ?>
            
        <h3 class="text-center">Update Info for <?php echo $current_user->first_name ?>  <?php echo $current_user->last_name ?></h3>
        <h6 class="text-center"><a href="<?php echo $page; ?>"><< Back to Previous Page</a></h6>
        
        <form method="post" id="adduser" class="pcl-profile-form" action="" method="post">
                       
            <p class="form-username">
                <label for="firstname"><?php _e('First Name', 'profile'); ?></label>
                <input class="text-input" name="firstname" type="text" id="firstname" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
            </p>
            
            <p class="form-username">
                <label for="lastname"><?php _e('Last Name', 'profile'); ?></label>
                <input class="text-input" name="lastname" type="text" id="lastname" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
            </p>
            
            <p class="form-display_name">
                <label for="display_name"><?php _e('Display name publicly as') ?></label>
    	
        		<select name="display_name" id="display_name">
        		    
        		    <?php
                    $public_display = array();
        			$public_display['display_nickname']  = $current_user->nickname;
        			$public_display['display_username']  = $current_user->user_login;
        
        			if ( !empty($current_user->first_name) )
        				$public_display['display_firstname'] = $current_user->first_name;
        
        			if ( !empty($current_user->last_name) )
        				$public_display['display_lastname'] = $current_user->last_name;
        
        			if ( !empty($current_user->first_name) && !empty($current_user->last_name) ) {
        				$public_display['display_firstlast'] = $current_user->first_name . ' ' . $current_user->last_name;
        				$public_display['display_lastfirst'] = $current_user->last_name . ' ' . $current_user->first_name;
        			}
        
        			if ( ! in_array( $current_user->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
        				$public_display = array( 'display_displayname' => $current_user->display_name ) + $public_display;
        
        			$public_display = array_map( 'trim', $public_display );
        			$public_display = array_unique( $public_display );
        
        			foreach ( $public_display as $id => $item ) : ?>
            <?php do_action('edit_user_profile', $current_user); ?>

            <p>&nbsp;</p>
        		</select>
    		
    		</p>
                        
            <p class="form-email">
                <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
            </p>
            
            <p class="form-url">
                <label for="url"><?php _e('Website', 'profile'); ?></label>
                <input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta( 'user_url', $current_user->ID ); ?>" />
            </p>
            
            <p class="form-password">
                <label for="password"><?php _e('Password *', 'profile'); ?> </label>
                <input class="text-input" name="password" type="password" id="password" />
            </p>
            
            <p class="form-password">
                <label for="password_check"><?php _e('Repeat Password *', 'profile'); ?></label>
                <input class="text-input" name="password_check" type="password" id="password_check" />
            </p>
            
            <p class="form-textarea">
                <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
                <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
            </p>
    
            <?php do_action('edit_user_profile', $current_user); ?>

            <p class="form-submit">                
                <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                
                <?php wp_nonce_field('update-user_'.$current_user->ID, 'pcl_update_profile', true); ?>

                <input name="action" type="hidden" id="action" value="update-user" />
            </p>
        
        </form>
        
    <?php endif; ?>

</div>
