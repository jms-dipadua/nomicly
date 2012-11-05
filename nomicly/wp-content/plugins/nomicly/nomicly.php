<?php
/*
Plugin Name: Nomicly
Plugin URI: http://jamesdipadua.com/
Description: Create ideas and build consensus. Change the world.
Version: 0.1
Author: James DiPadua
Author URI: http://jamesdipadua.com/
License: GPLv2
Detailed Overview:
	This plugin will help create the consensus building functions.
	That will largely translate to a handful of cron jobs
	Also makes Reputation adjustments, Consensus Counts and Consensus Resolution Possible	
	Other nomicly features may include
		Special Graphical Treatment of Interactions
		Social Network/Graph Analysis
*/
?>
<?php

//register_activation_hook(__FILE__, 'jadalm_nomicly_activation');
add_action( 'init', 'jadalm_nomicly_activation' );

//register_deactivation_hook(__FILE__, 'jadalm_nomicly_deactivation');

function jadalm_nomicly_activation () {

}


function jadalm_nomicly_deactivation() {
#nothing for now. 
#delete custom post type?
#convert all nomicly custom post types to normal posts?
}

/* 
// what i was using when i was making a custom post-type
function jadalm_nomicly_activation () {
#create the custom-post-type
  $labels = array(
              'labels' => array(
	                'name' => 'Ideas',
	                'singular_name' => 'Idea',
	                'add_new' => 'Add New',
	                'add_new_item' => 'Add New Idea',
	                'edit' => 'Edit',
	                'edit_item' => 'Edit Idea',
	                'new_item' => 'New Idea',
	                'view' => 'View',
					'view_item' => 'View Idea', 
	                'search_items' => 'Search Ideas',
	                'not_found' => 'No Ideas Found',
	                'not_found_in_trash' => 'No Ideas Found in Trash',
	                'parent' => 'Parent Idea'
    	        )//end labels array
            );
    $arguments = array(
    		'labels' => $labels,
            'public' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'author', 'comments'),
//            'supports' => array( 'title', 'author', 'comments', 'custom-fields' ),
//            'supports' => array( 'title', 'author', 'comments', 'editor', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/icon.jpg', __FILE__ ),
            'has_archive' => true
        );
   register_post_type( 'ideas', $arguments);
}

*/


?>