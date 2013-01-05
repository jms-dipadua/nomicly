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
/*
// BUG/WHERE I LEFT OFF
// - cron created successfully
// - creates tables
// - populates users
// - awards votes 	
	// NOTE:
		// THE CRON JOB WILL RUN WHEN THIS IS INITIALIZED
		// BUT IT RUNS *AFTER* THE SETUP FUNCTIONS COMPLETE 
		// 		(note 1 sec diff between created_at and updated_at...)
		// DON'T KNOW WHY...maybe because the "time stamp" for running still matches?
		// TO AVOID GRANTING TOO MANY VOTES::
		// - THE INITIALIZATION GRANTS USERS 0 VOTES
		// - THE CRON THEN RUNS AND GRANTS USERS 10 VOTES. 
*/


// INITIALIZATION
register_activation_hook(__FILE__, 'nomicly_activation');
add_action('nomicly_vote_award_hourly', 'nomicly_award_votes');
add_action('nomicly_user_report_daily', 'nomicly_reporting');

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
// USER NOTIFICATION PREFERENCES 
		nomicly_create_user_note_pref_db();

// CRON SETUP
	// AWARD VOTES
	create_award_votes_cron();
	// REPORTS
	create_daily_report_cron();
// POPULATE USER_VOTE_CACHE	
	initialize_user_vote_cache();
// POPULATE USER NOTIFICATION PREFS
	initialize_user_note_prefs();
// TESTING
	nomicly_reporting();
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
//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_vote_cache = $wpdb->prefix."user_vote_cache";

	$sql = "CREATE TABLE IF NOT EXISTS $table_user_vote_cache (
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

function nomicly_create_user_note_pref_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_note_prefs = $wpdb->prefix."user_note_prefs";

$sql = "CREATE TABLE IF NOT EXISTS $table_user_note_prefs (
  user_id INT NOT NULL,
  sub_type ENUM ('0','1','3'),
  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  last_emailed DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY user_id (user_id)
);";

dbDelta($sql);
} // END CREATE NOTIFICATION PREFERENCES

/*
//	CRON JOB STUFF
// v1.0
*/
function create_award_votes_cron() {
	$time = date('H:i:s');
		wp_schedule_event($time, 'hourly', 'nomicly_vote_award_hourly');
}  // HOURLY VOTES

function create_daily_report_cron() {
//	$time = '08:45:00';
	$time = date('H:i:s');
		wp_schedule_event($time, 'daily', 'nomicly_user_report_daily');
}  // DAILY REPORTS

/*
//	AWARD VOTES 
// v1.0
*/

function nomicly_award_votes() {
	// 1. get all users
	// 2. give them 10 votes each
		// 	-- later versions may need to deal w/ status
		//	-- status is *not* really supported in WP at this time...
	global $wpdb;
	$table = $wpdb ->prefix."user_vote_cache";
	$award_amount = 10;
	$user_ids = $wpdb->get_col("SELECT user_id FROM $table");
	if ( $user_ids ) {
		foreach ( $user_ids as $user_id ) { 	
		// GIVE THEM VOTES
			increase_available_votes($user_id, $award_amount);
		}
	}
} // END AWARD VOTES

/*
//	USER NOTIFICATIONS 
// 	v1.0
*/
function nomicly_reporting (){
	$send_type = date('w');
 	$send_type = intval($send_type);
	// NOT ON SAT / SUN
	/* if ($send_type == ( 0 xor 6 )) { 
		return;
	}
	else */ if ($send_type == 5) {
	 // 5 = FRIDAY
	 // SEND TO WEEKLY
		$sub_type = 2;
		$sub_list = get_user_note_list($sub_type);
		$period = 7;
		generate_notification($sub_list, $period);
	// SEND TO DAILY
		$sub_type = 1;
		$sub_list = get_user_note_list($sub_type);
		$period = 1;
		generate_notification($sub_list, $period);
	}
	else { //SEND TO DAILY SUBSCRIBERS
			$sub_type = 1;
			$sub_list = get_user_note_list($sub_type);
			$period = 1;
			generate_notification($sub_list, $period);
		}
} // END DAILY


/*
// TABLE INITIALIZATION
	// putting data into tables
*/
	// USER VOTE CACHE
	// PUTTING USERS INTO TABLE + GIVING VOTES
function initialize_user_vote_cache() {
	// 1. get all users
	// 2. populate them into the user_vote_cache table w/ 10 votes each
	global $wpdb;
	$table_users = $wpdb ->prefix."users";
	$table_user_cache = $wpdb ->prefix."user_vote_cache";
	$award_amount = 0;
	$date = date('Y-m-d H:i:s');

	$user_ids = $wpdb->get_col("SELECT ID FROM nomicly_users");
	if ( $user_ids ) {
		foreach ( $user_ids as $user_id ) { 	
		//POPULATE INTO USER_VOTE_CACHE
			$initial_user_data = array (
				'user_id' => $user_id,
				'num_votes_avail' => $award_amount,
				'created_at' => $date,
				'updated_at' => $date
				);
			$wpdb->insert( $table_user_cache, $initial_user_data );
		}// END FOR EACH
	}// USERS EXIST
}// END USER VOTE CACHE

// COMPLETE USER_NOTIFICATION_DB SETUP
function initialize_user_note_prefs() {
	// 1. get all users
	// 2. populate them into the user_note_prefs table w/ daily email subscription
	global $wpdb;
	$table_user_note_prefs = $wpdb ->prefix."user_note_prefs";
	$date = date('Y-m-d H:i:s');

	$user_ids = $wpdb->get_col("SELECT ID FROM nomicly_users");
	if ( $user_ids ) {
		foreach ( $user_ids as $user_id ) { 	
		//POPULATE INTO USER_VOTE_CACHE
			$initial_user_data = array (
				'user_id' => $user_id,
				'sub_type' => '1',
				'updated_at' => $date
				);
			$wpdb->insert( $table_user_note_prefs, $initial_user_data );
		}// END FOR EACH
	}// USERS EXIST
}// END USER NOTE PREF SETUP

/*
// DEACTIVATION
*/

function nomicly_deactivation() {
// REMOVE CRON(S)
	// VOTES
wp_clear_scheduled_hook('nomicly_vote_award_hourly');

	// REPORTS
wp_clear_scheduled_hook('nomicly_user_report_daily');

//will need to write a db dump later
// back up may be lame because the pairings will all be fucked.
//for now, just drop the tables (but not really...)
global $wpdb;
// DB: hot or not
$table_votes = $wpdb->prefix."hot_not_votes";
$table_pairs = $wpdb->prefix."hot_not_pairs"; 
$table_user_topics = $wpdb->prefix."user_topics";

//	$wpdb->query("DROP TABLE IF EXISTS $table_votes");
//	$wpdb->query("DROP TABLE IF EXISTS $table_pairs");
//  $wpdb ->query("DROP TABLE IF EXISTS $table_user_topics");

}//END DEACTIVATION 


?>