<?php

/**
 * pcl_update_100_shortcodes function.
 * 
 * @access public
 * @return void
 */
function pcl_update_100_shortcodes() {
	global $wpdb;
	
	$wpdb->query("
		UPDATE $wpdb->posts
		SET post_content = REPLACE(post_content, '[emcl-', '[pcl-')
		WHERE post_content LIKE '%emcl-%'
	");
}

/**
 * pcl_add_edit_profile_page function.
 * 
 * @access public
 * @return void
 */
function pcl_add_edit_profile_page() {
    $pages=get_option('pcl_pages', '');
    
    // Check that the page doesn't exist already
    $query=new WP_Query('pagename=profile');

	if (!$query->have_posts()) :
		// Add the page using the data from the array above
		$post_id=wp_insert_post(
			array(
				'post_content'   => '[pcl-profile]',
				'post_name'      => 'profile',
				'post_title'     => __('Profile', 'pcl'),
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'ping_status'    => 'closed',
				'comment_status' => 'closed',
			)
		);
	else :
		$post_id=$query->queried_object_id;
	endif;

	$pages['profile']=$post_id;

    update_option('pcl_pages', $pages);   
}	
?>