<?php
/**
 * Admin approve users page
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

?>

<h2>Approve Users</h2>

<ul class="subsubsub">
    <li class="unapproved"><a href="#" class="current" aria-current="page">Unapproved <span class="count">(<?php pcl_users_to_be_activated_count(); ?>)</span></a></li>
</ul>

<form id="pcl-approve-users" action="" method="post">
    <?php wp_nonce_field( 'approve_users', 'pcl_admin_update' ); ?>

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
                
                <?php foreach ( pickle_custom_login()->admin->approve_user_cols() as $slug => $label ) : ?>
                    <th scope="col" id="<?php echo $slug; ?>" class="manage-column column-<?php echo $slug; ?>"><?php echo $label; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        
        <tbody id="the-list" data-wp-lists="list:user">
    
            <?php foreach ( pcl_users_to_be_activated() as $user ) : ?>
      
                <tr id="user-<?php echo $user->ID; ?>">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="user_<?php echo $user->ID; ?>">Select <?php echo $user->data->user_login; ?></label>
                        <input type="checkbox" name="pcl_users[]" id="user_<?php echo $user->ID; ?>" class="" value="<?php echo $user->ID; ?>">
                    </th>
                    
                    <?php foreach ( pickle_custom_login()->admin->approve_user_cols() as $slug => $label ) : ?>
                        <?php pickle_custom_login()->admin->approve_user_cols_values( $slug, $label, $user ); ?>
                    <?php endforeach; ?>
                    
                </tr>
            
            <?php endforeach; ?>
            
        </tbody>

    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Approve Users', 'pcl' ); ?>"></p>
</form>
