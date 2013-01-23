<?php
/*
Plugin Name: Nomicly Gamification
Plugin URI: http://jamesdipadua.com/
Description: Primary gamification plugin for Nomicly 
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
<?
// INITIALIZATION
register_activation_hook(__FILE__, 'nomicly_gamification_activation');


// DEACTIVATION
register_deactivation_hook(__FILE__, 'nomicly_gamification_deactivation');

/*
//	ACTIVATION
*/
function nomicly_gamification_activation() {
// CREATE DBs (IF NOT EXISTS)
// USER NOTIFICATION PREFERENCES 
		nomicly_create_gamification_dbs();
}

/*
// 	DATABASES
*/
function nomicly_create_gamification_dbs() {
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;

//	1.  quests
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_quests (
		  quest_id INT NOT NULL AUTO_INCREMENT,
		  quest_name TEXT DEFAULT '  ',
		  status BOOLEAN NOT NULL DEFAULT '1',
		  PRIMARY KEY (quest_id) 
		);";

	dbDelta($sql);

//	2.  quest_meta
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_quest_meta (
		  quest_id INT NOT NULL,
		  quest_description TEXT DEFAULT 'No Description Provided',
		  qualifications VARCHAR DEFAULT '0',
		  permanency ENUM ('0','1'),
		  may_requalify BOOLEAN NOT NULL DEFAULT '1',
		  max_repeat INT NOT NULL DEFAULT '10000',
		  event_type ENUM ('0','1','2','3','4','5'),
		  timeframe INT NOT NULL DEFAULT '10000',
		  number_events INT NOT NULL DEFAULT '1',
		  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  expires_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (quest_id) 
		  );";

	dbDelta($sql);
	
// 	3. 	achievements
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_achievements (
		  achievement_id INT NOT NULL AUTO_INCREMENT,
		  achievement_name TEXT DEFAULT '  ',
		  status BOOLEAN NOT NULL DEFAULT '1',
		  PRIMARY KEY (achievement_id) 
		);";

	dbDelta($sql);

// 	4. 	achievement_meta
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_achievement_meta (
		  achievement_id INT NOT NULL,
		  achievement_description TEXT DEFAULT 'No Description Provided',
		  max_level INT DEFAULT '1',
		  badge_img_url VARCHAR,
		  permanency ENUM ('0','1'),
		  may_requalify BOOLEAN NOT NULL DEFAULT '1',
		  max_repeat INT NOT NULL DEFAULT '10000',
		  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (achievement_id) 
		  );";

	dbDelta($sql);

// 	5. 	user_quests
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_user_quests (
		  user_id INT NOT NULL,
		  quest_id INT NOT NULL,
		  attained_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  status BOOLEAN NOT NULL DEFAULT '1',
		  PRIMARY KEY (user_id) 
		  );";

	dbDelta($sql);
	
	$sql = "CREATE INDEX user_quest_index ON nomicly_user_quests (quest_id);";
	dbDelta($sql);

// 	6. 	user_achievements
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_user_achievements (
		  user_id INT NOT NULL,
		  achievement_id INT NOT NULL,
		  level_attained INT NOT NULL DEFAULT '1',
		  attained_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  status BOOLEAN NOT NULL DEFAULT '1',
		  PRIMARY KEY (user_id) 
		  );";

	dbDelta($sql);
	
	$sql = "CREATE INDEX user_achievement_index ON nomicly_user_quests (achievement_id);";
	dbDelta($sql);

// 	7. 	user_quest_qualifications
	$sql = "CREATE TABLE IF NOT EXISTS nomicly_user_quest_qualifications (
		  user_id INT NOT NULL,
		  achievement_id INT NOT NULL,
		  qualification_count INT NOT NULL DEFAULT '0',
		  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (user_id) 
		  );";

	dbDelta($sql);

}

/*
// 	DEACTIVATION
*/
function nomicly_gamification_deactivation() {

}

/* 
// 		MAIN FUNCTIONS
*/

// CALCULATE HOURS TO COMPLETE QUEST
	// this is a rang in hours
	// so if person can complete the quest if they create "3 ideas within 7 days"
	// then the hours to complete == 7 * 24 = 168
	// etc
function calc_hours_to_complete() {

	return $num_hours;
}

// SETS EXPIRATION DATE FOR QUESTS (OR ACHIEVEMENTS?)
function set_expiration_date(){

	return $response;
}

// TOGGLES A QUEST FROM ACTIVE TO INACTIVE
function expire_quest($quest_id){

	return $response;
} // END EXPIRE QUEST

// TOGGLES A QUEST FROM INACTIVE TO ACTIVE
function enable_quest($quest_id){

	return $response;
}// END ENABLE QUEST

// IS QUEST REPEATABLE
function is_quest_repeatable($quest_id){

	return $repeat_status;
} // END QUEST REPEATABLE

// IS QUEST PERMANENT
function is_quest_permanent(){

	return $premanent_status;
} // END QUEST PERMANENCY

// ACHIEVEMENT PERMANENCY
function is_achievement_permanent(){

	return $premanent_status;
}// END ACHIEVEMENT PERMANENCY

function award_achievement($user_id, $achievement_id){

	$response =	notify_achievement_completion($achievement_data);
		if ($response = 0) {
		 // SEND EMAIL TO JMS
		}
	$response = notify_user_ui_quest_complete($achievement_data);
		if ($response = 0) {
		 // SEND EMAIL TO JMS
		}
	$response = email_quest_completion($user_id, $achievement_data);
		if ($response = 0) {
		 // SEND EMAIL TO JMS
		}

	return $response;
}

function notify_achievement_completion($achievement_data){

	return $response;
}

function notify_user_ui_quest_complete($achievement_data){

	return $response;
}

function email_quest_completion($user_id, $achievement_data){

	return $response;
}

// GET QUEST DETAILS
function get_quest_details($quest_id){

	return $quest_data;
}// END QUEST DETAILS

// IS EVENT QUEST
// 	queries the quest_meta to see if the event type is qualifying for any quests
//  if yes, returns the quest_id. otherwise, returns null
function is_event_quest($event_type){

	return $quest_id;
}

// RECORD QUEST EVENT
	// increases qualification_count by 1
function record_quest_event($user_id, $quest_id){

	return $record_quest_response;
}// END RECORD QUEST EVENT

// CHECK USER QUEST COMPLETION
function is_user_quest_completed($user_id, $quest_id){

	return $completion_status;

} // END CHECK USER QUEST COMPLETION


function get_quest_requirements($quest_id){

	return $quest_data;
}

// GET FULFILLED QUEST REQUIREMENTS 
	// this function gets the counts associated with a particular quest and a particular user
function get_user_quest_requisites($user_id, $quest_id){

	return $quest_requisite_count;
}


/* V2
	function save_new_quest(){
	}
*/

?>