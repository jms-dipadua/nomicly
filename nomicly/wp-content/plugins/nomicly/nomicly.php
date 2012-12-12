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
	Support-DB Initialization and Deletion w/ dump
	Other nomicly features may include
		Special Graphical Treatment of Interactions
		Social Network/Graph Analysis
*/
?>
<?php
// INITIALIZATION
register_activation_hook(__FILE__, 'nomicly_activation');
// DEACTIVATION
register_deactivation_hook(__FILE__, 'nomicly_deactivation');

/*
//	ACTIVATION
*/
function nomicly_activation() {
// CREATE DBs (IF NOT EXISTS)
// USER IDEA VOTES
		nomicly_user_idea_votes_db();
// USER VOTE CACHE
		nomicly_create_user_vote_cache_db();
// USER TOPICS
 		nomicly_create_user_topics_db();
// IDEA CONSENSUS
		nomicly_create_idea_consensus_db();
// PAIRS
		nomicly_create_hot_not_pairs_db();
// USER TOPICS
 		nomicly_create_user_topics_db();

// CRON SETUP
	create_award_votes_cron();
	add_action('nomicly_vote_award_hourly', 'nomicly_award_votes');
	
	// award_initial_votes();

}//end of nomicly_activiation

function nomicly_user_idea_votes_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_idea_votes = $wpdb->prefix."user_idea_votes";

	$sql = "CREATE TABLE IF NOT EXISTS $table_user_idea_votes (  
	vote_id int NOT NULL AUTO_INCREMENT,
	idea_id int NOT NULL,
	user_id int NOT NULL,
	vote_type ENUM ('0','1','3'),
	created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (vote_id) 
	);";
	dbDelta($sql);

	$sql = "CREATE INDEX idea_index ON $table_user_idea_votes (idea_id);";
 dbDelta($sql);

}// END CREATE USER IDEA VOTES

function nomicly_create_user_vote_cache_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_vote_cache = $wpdb->prefix."user_vote_cache";


	$sql = "CREATE TABLE $table_user_vote_cache (
	user_id int NOT NULL,
	num_votes_avail int NOT NULL DEFAULT '10',
	max_votes int NOT NULL DEFAULT '20',
	created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
	updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',   
	PRIMARY KEY (user_id) 
	);";
 dbDelta($sql);

}// END CREATE USER IDEA VOTES

function nomicly_create_idea_consensus_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_idea_consensus = $wpdb->prefix."idea_consensus";

	$sql = "CREATE TABLE IF NOT EXISTS $table_idea_consensus (
	idea_id int NOT NULL,
	votes_yes int NOT NULL DEFAULT '0',
	votes_no int NOT NULL DEFAULT '0', 
	updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (idea_id) 
	);";
 dbDelta($sql);

}// END CREATE IDEA CONSENSUS

function nomicly_create_hot_not_votes_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_votes = $wpdb->prefix."hot_not_votes";

$sql = "CREATE TABLE IF NOT EXISTS $table_votes (
  vote_id int NOT NULL AUTO_INCREMENT,
  chosen_id int NOT NULL,
  pair_id int NOT NULL,
  user_id int NOT NULL,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY vote_id (vote_id)
);";

dbDelta($sql);
	
} // end nomicly_create_hot_or_not_votes


function nomicly_create_hot_not_pairs_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_pairs = $wpdb->prefix."hot_not_pairs";

$sql = "CREATE TABLE IF NOT EXISTS $table_pairs (
  pair_id int NOT NULL AUTO_INCREMENT,
  idea_pair VARCHAR(100) DEFAULT '' NOT NULL,
  idea_1_count int NOT NULL,
  idea_2_count int NOT NULL,
  updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY pair_id (pair_id)
);";
dbDelta($sql);
	
} // end nomicly_create_hot_not_pairs_db


function nomicly_create_user_topics_db () {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_topics = $wpdb->prefix."user_topics";

$sql = "CREATE TABLE IF NOT EXISTS $table_user_topics (
  user_topic_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  topic_id int NOT NULL,
  created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY user_topic_id (user_topic_id)
);";

dbDelta($sql);

}// END CREATE USER_TOPICS_DB

/*
//AWARD VOTES AND CRON JOB STUFF
// v1.0
*/
function create_award_votes_cron() {
	$time = date('H:i:s');
		wp_schedule_event($time, 'hourly', 'nomicly_vote_award_hourly');
}

function award_initial_votes() {
	// for each user, give them 10 votes and populate them into the user_vote_cache table
}

function nomicly_award_votes() {
	$amount = 10;
}

/* 
	// TO DO
	// START OF LOGIN/REGISTRATION REDIRECT BUG FIX
	helper code to mod the redirect after registration/login
	// adding a redirect to index.php...?
	else {
	$ref = "../index.php";
	return $ref;
	}
	
	to modify:

	function wp_get_referer() {
	$ref = false;
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) )
		$ref = $_REQUEST['_wp_http_referer'];
	else if ( ! empty( $_SERVER['HTTP_REFERER'] ) )
		$ref = $_SERVER['HTTP_REFERER'];

	if ( $ref && $ref !== $_SERVER['REQUEST_URI'] )
		return $ref;
	else {
	$ref = "../index.php";
	return $ref;
	}
}
*/


/*
// DEACTIVATION
*/

function nomicly_deactivation() {
// REMOVE CRON(S)
wp_clear_scheduled_hook('nomicly_vote_award_hourly');

//will need to write a db dump later
// back up may be lame because the pairings will all be fucked.
//for now, just drop the tables
global $wpdb;
// DB: hot or not
$table_votes = $wpdb->prefix."hot_not_votes";
$table_pairs = $wpdb->prefix."hot_not_pairs"; 
$table_user_topics = $wpdb->prefix."user_topics";

//	$wpdb->query("DROP TABLE IF EXISTS $table_votes");
//	$wpdb->query("DROP TABLE IF EXISTS $table_pairs");
//  $wpdb ->query("DROP TABLE IF EXISTS $table_user_topics");

}//END DEACTIVATION 

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