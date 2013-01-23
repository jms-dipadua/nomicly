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
/*
	** High-Level Workflow (from spec) ** 
	a) user comes to site
	b) user votes on an idea (or creates an idea or modifies an idea)
	c) frontend gets notified of successful vote (or creation, etc)
		- gets an event_type response (in addition to any other response it normally gets)
	d) frontend makes new request to backend for whether the event is worth monitoring
		1) if yes, then backend records the event with the applicable quest
		2) after recording the event, backend makes a check for whether an achievement needs to be awarded or not
			- if yes, then backend awards achievement && notifies frontend (and sends an achievement email)
		3) if NO, then nothing happens on frontend
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
		  qualifications VARCHAR DEFAULT '0',
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
		  quest_id INT NOT NULL,
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
	// this is a range in hours
	// so if person can complete the quest if they create "3 ideas within 7 days"
	// then the hours to complete == 7 * 24 = 168
	// etc
function calc_hours_to_complete($number, $period) {
	// number = integer 
	// period = day, weeks, etc

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

// GET ACTIVE QUESTS 
	// input is event type
	// output is all active events (ARRAY)
		// --> INVESTIGATE:  
		//		 if you also have user_id, can you filter active quests relevant to the user?
		// 		 cut out a trip or two to the db for status check...
function get_active_quests($event_type) {
	global $wpdb;
	$table_quests = $wpdb->prefix."quests";
	$table_quest_meta = $wpdb->prefix."quest_meta";
	
	$active_quests = $wpdb -> get_col(
		"SELECT quest_id 
		FROM $table
		INNER JOIN $table_quest_meta 
		ON $table_quests.quest_id = $table_quest_meta.quest_id
		WHERE status = 1
		AND event_type = '$event_type'",
		ARRAY_N
		);
	
	if(empty($active_quests)) {
		$active_quests = array ();
	}
	return $active_quests;
} // END GET ACTIVE QUESTS


// GET ACHIEVEMENT ID
	// GETS IT BASED ON THE QUEST ID
function get_achievement_id($quest_id) {

	return $achievement_id;
}
// get achievement data
	// returns array of data, id, name, description, etc.
function get_achievement_details($achievement_id) {

	return $achievement_data;
}

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
//  RETURNS AS ARRAY OF DATA, SO MAY BE MORE THAN ONE QUEST ID
function is_event_quest($event_type){

	return $quests;
}

// RECORD QUEST EVENT
	// increases qualification_count by 1
function record_event($user_id, $quest_id){

	return $record_response;
}// END RECORD QUEST EVENT

// CHECK USER QUEST COMPLETION
	// checks to see if a user has already completed a specific quest
function is_user_quest_completed($user_id, $quest_id){

	return $completion_status;

} // END CHECK USER QUEST COMPLETION

// IS QUEST COMPLETED (NOW)
	// checks to see if a user has now completed a quest
	// i.e. an event was recorded, the quest wasn't previously completed and now we want to see if is now a completed quest 

function is_quest_completed($user_id, $quest_id) {

	// get the quest requirements
	$quest_requirements = get_quest_requirements($quest_id);
	// set the timeframe
		$timeframe = $quest_requirements['timeframe'];
	// get the quest events relevant to the user and the timeframe
	$relevant_quest_events = get_user_quest_events($user_id, $quest_id, $timeframe);
	
	// see if user has completed the quest
		// compare the count between needed events and what the user has
		// if ==, then response = 1 (award)
		// if not, then response = 0 (no-award, no-action)
	return $completion_status;
} // END IS QUEST COMPLETED

function get_quest_requirements($quest_id){

	return $quest_data;
}

// GET QUEST EVENTS 
	// this function gets the counts associated with a particular quest and a particular user
	// quests have timeframes in which they need to be completed, so the count has to be constrained by the timeframe passed in
	// note that timeframe is in hours
function get_user_quest_events($user_id, $quest_id, $timeframe){
	$end_date = date('Y-m-d H:i:s');
	$start_date = date('Y-m-d H:m:s', strtotime('+'.$timeframe.' hours')); 
	
	global $wpdb;
	$table = $wpdb -> prefix."user_quest_qualifications";
	
	$quest_event_count = $wpdb -> get_col( 
		"SELECT qualification_count 
		FROM $table
		WHERE user_id = '$user_id'
		AND quest_id = '$quest_id'"
		);
	
	if(empty($quest_event_count)) {
		$quest_event_count = array();
		}
	
	return $quest_event_count;
}

// GET EVENT LIST
// used by frontend to get event types to listen for
// returns an array of event types
function get_event_list() {
	global $wpdb;
	$table_quests = $wpdb -> prefix."quests";
	$table_quest_meta = $wpdb -> prefix."quest_meta";
	
	$event_list = $wpdb -> get_col(
		"SELECT DISTINCT event_type 
		FROM $table_quests
		INNER JOIN $table_meta
			ON $table_quests.quest_id = $table_meta.quest_id
		WHERE status = 1"
		);
		
	if(empty($event_list)) {
		$event_list = array();
	}
	
	return $event_list;
} // END GET EVENT LIST

/*
// 	AJAX HANDLER FUNCTIONS
*/

function fetch_event_list() {

	$event_list = get_event_list();
		//check for existing list
		if(empty($event_list)) {
			$event_list = array (
				'event_list' => "null"
				);
			}	
	$response_data = json_encode($event_list);
	die ($response_data);
}

add_action('wp_ajax_fetch_event_list', 'fetch_event_list');
// non-logged in user
add_action('wp_ajax_nopriv_fetch_event_list', 'fetch_event_list' );


/*
//	PROCESS INTERACTION EVENT
//		responsible for getting incoming event signal from frontend
//		determining relevant quest, if any,
//		seeing if user should get new achievement
//		if so, returns the achievement data
*/
function process_interaction_event() {
	$event_type = $_POST['event_type'];
	$user_id = get_current_user_id();
	
	// 1. UPDATE THE quest qualifications TABLE -- RECORD_STATS()
		// QUESTS CAN ONLY HAVE ONE TYPE
		// so, don't need to pass in event_type 
	$record_status = record_event($user_id, $quest_id);

	// 2. Get Active Quests (Relevant to Event)
	$active_quests = get_active_quests($event_type);
		// VERIFY ACTIVE QUESTS, THEN LOOP THROUGH EACH TO VERIFY USER'S QUEST STATUS
		if(!empty($active_quests)) {
			foreach($active_quests as $quest_id) {
					// i. see if user has completed it 
				$quest_status = is_user_quest_completed($user_id, $quest_id);
					// ii. if not, see if user should have it now
					if ($quest_status == 0) {
						$new_status = is_quest_completed($user_id, $quest_id);
							// if status = 1, then award; if = 0, then do nothing
							if ($new_status = 1) {
								$achievement_id = get_achievement_id($quest_id);
								$achievment_data = award_achievement($user_id, $achievement_id);
								// IF DATA, THEN START CREATING RESPONSE DATA
								//	response data will then be an array of an array...
							if(!empty($achievement_data)) {
								$response_data['achievement_id'] = $achievement_data;
									}
					}
			} // END LOOP THROUGH ACTIVE QUESTS
		
		} // END HAS ACTIVE QUESTS
	// 2. UPDATE THE USERS QUEST RECORDS (I.E. SEE IF THERE ARE ANY NEW ACHIEVEMENTS TO AWARD)
			// a. get_active_quests
			// b. loop through each (foreach)
				
		// 4. IF SO, AWARD NEW ACHIEVEMENT
		// 0 = false, 1 = true (quest completed)
				if($quest_status == 1) {
					
					}
				}
			} // END FOR EACH
		} // NO QUESTS ASSOCIATED WITH EVENT

// CONVERT ARRAY TO JSON
	$response_data = json_encode($response_data);	
// response output
	die($response_data);
 }// END PROCESS HOT NOT VOTE

add_action('wp_ajax_is_quest', 'process_interaction_event');
// non-logged in user
add_action('wp_ajax_nopriv_is_quest', 'process_interaction_event' );

/* V2
	function save_new_quest(){
	}
*/

?>