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

function calc_hours_to_complete() {
}
function save_new_quest(){
}
function set_expiration_date(){
}
function expire_quest(){
}
function is_quest_repeatable(){
}
function is_quest_permanent(){
}
function is_achievement_permanent(){
}
function award_achievement(){
}
function notify_achievement_completion(){
}
function notify_user_ui_quest_complete(){
}
function email_quest_completion(){
}
function get_quest_details(){
}
function is_event_quest(){
}
function record_quest_event(){
}
function is_user_quest_completed(){
}
function get_quest_requirements(){
}
function get_user_quest_requisites(){
}
function sort_max_quest_level(){
}

?>