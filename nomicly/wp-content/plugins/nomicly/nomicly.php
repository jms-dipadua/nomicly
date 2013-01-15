<?php
/*
Plugin Name: Nomicly Notifications
Plugin URI: http://jamesdipadua.com/
Description: This plugin provides notifications on activity related to specific people as well as the broader community. Create ideas and build consensus. Change the world.  
Version: 1.0
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
register_activation_hook(__FILE__, 'nomicly_note_activation');
add_action('nomicly_user_report_daily', 'nomicly_reporting');

// DEACTIVATION
register_deactivation_hook(__FILE__, 'nomicly_note_deactivation');

/*
//	ACTIVATION
*/
function nomicly_note_activation() {
// USER NOTIFICATION PREFERENCES 
		nomicly_create_user_note_pref_db();

// CRON SETUP
	// REPORTS
	create_daily_report_cron();

// POPULATE USER NOTIFICATION PREFS
	initialize_user_note_prefs();

// TESTING
	nomicly_reporting();
}//end of nomicly_activiation

function nomicly_create_user_note_pref_db() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;
$table_user_note_prefs = $wpdb->prefix."user_note_prefs";

$sql = "CREATE TABLE IF NOT EXISTS $table_user_note_prefs (
  user_id INT NOT NULL,
  sub_type ENUM ('0','1','2'),
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
function create_daily_report_cron() {
	$time = '08:45:00';
//	$time = date('H:i:s');
		wp_schedule_event($time, 'daily', 'nomicly_user_report_daily');
}  // DAILY REPORTS

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

function nomicly_note_deactivation() {
// REMOVE CRON(S)
	// REPORTS
wp_clear_scheduled_hook('nomicly_user_report_daily');

//will need to write a db dump later
}//END DEACTIVATION 


?>