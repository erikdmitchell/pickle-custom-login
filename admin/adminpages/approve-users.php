<h2>Approve Users</h2>

<ul class="subsubsub">
	<li class="unapproved"><a href="#" class="current" aria-current="page">Unapproved <span class="count">(<?php pcl_users_to_be_activated_count(); ?>)</span></a></li>
</ul>

<form id="pcl-approve-users" action="" method="post">
	<?php wp_nonce_field('approve_users', 'pcl_admin_update'); ?>

    <div class="tablenav top">
        <div class="tablenav-pages one-page">
            <span class="displaying-num"><?php pcl_users_to_be_activated_count(); ?> users</span>
        </div>
		<br class="clear">
	</div>
	
	<table class="wp-list-table widefat fixed striped approve-users">

	    <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" id="username" class="manage-column column-username column-primary">Username</th>
                <th scope="col" id="name" class="manage-column column-name">Name</th>
                <th scope="col" id="email" class="manage-column column-email">Email</th>
                <th scope="col" id="role" class="manage-column column-role">Role</th>
            </tr>
    	</thead>
    	
    	<tbody id="the-list" data-wp-lists="list:user">
    
            <?php foreach (pcl_users_to_be_activated() as $user) : ?>
      
                <tr id="user-<?php echo $user->ID; ?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="user_<?php echo $user->ID; ?>">Select <?php echo $user->data->user_login; ?></label>
                        <input type="checkbox" name="pcl_users[]" id="user_<?php echo $user->ID; ?>" class="" value="<?php echo $user->ID; ?>">
                    </th>
                    
                    <td class="username column-username has-row-actions column-primary" data-colname="Username">
                        <?php echo get_avatar($user->ID, 32); ?>
                        <strong><a href="<?php echo get_edit_user_link($user->ID); ?>"><?php echo $user->data->user_login; ?></a></strong>
                    </td>
                        
                    <td class="name column-name" data-colname="Name"><?php echo $user->data->display_name; ?></td>
                    
                    <td class="email column-email" data-colname="Email"><a href="mailto:<?php echo $user->data->user_email; ?>"><?php echo $user->data->user_email; ?></a></td>
                    
                    <td class="role column-role" data-colname="Role">Administrator</td>
                    
                </tr>
            
            <?php endforeach; ?>
            
    	</tbody>

	</table>

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Approve Users', 'pcl'); ?>"></p>
</form>