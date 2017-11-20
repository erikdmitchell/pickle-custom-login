<div class="pcl-profile">

    <?php if (!is_user_logged_in()) : ?>

        <p class="warning">
            <?php _e('You must be logged in to edit your profile.', 'pcl'); ?>
        </p><!-- .warning -->
            
    <?php else : ?>
        
        <?php $current_user = wp_get_current_user(); ?>
            
        <h3>Update Information for <?php echo $current_user->user_login ?></h3>
        
        <form method="post" id="adduser" class="pcl-profile-form" action="" method="post">
                       
            <p class="form-username">
                <label for="first-name"><?php _e('First Name', 'profile'); ?></label>
                <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
            </p>
            
            <p class="form-username">
                <label for="last-name"><?php _e('Last Name', 'profile'); ?></label>
                <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
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
            			<option <?php selected( $current_user->display_name, $item ); ?>><?php echo $item; ?></option>
                    <?php endforeach; ?>
                    
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
                <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
                <input class="text-input" name="pass1" type="password" id="pass1" />
            </p>
            
            <p class="form-password">
                <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
                <input class="text-input" name="pass2" type="password" id="pass2" />
            </p>
            
            <p class="form-textarea">
                <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
                <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
            </p>
    
            <?php do_action('edit_user_profile', $current_user); ?>

            <p class="form-submit">                
                <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                
                <?php wp_nonce_field( 'update-user_'. $current_user->ID ) ?>
                
                <input name="action" type="hidden" id="action" value="update-user" />
            </p>
        
        </form>
        
    <?php endif; ?>

</div>