<?php

function pcl_update_100_shortcodes() {
	global $wpdb;
	
	$wpdb->query("
		UPDATE $wpdb->posts
		SET post_content = REPLACE(post_content, '[emcl-', '[pcl-')
		WHERE post_content LIKE '%emcl-%'
	");
}	
?>