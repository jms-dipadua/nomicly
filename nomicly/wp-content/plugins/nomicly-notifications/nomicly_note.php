<?php
/*
Plugin Name: Nomicly Notifications
Plugin URI: http://jamesdipadua.com/
Description: Primary notifications plugin for Nomicly 
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
/*
// BUG/WHERE I LEFT OFF
// 
*/


// INITIALIZATION
register_activation_hook(__FILE__, 'nomicly_note_activation');
add_action('nomicly_user_report_daily', 'nomicly_reporting');

// DEACTIVATION
register_deactivation_hook(__FILE__, 'nomicly_note_deactivation');

/*
//	ACTIVATION
*/
function nomicly_note_activation() {
// CREATE DBs (IF NOT EXISTS)
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

/*
	// MAIN BULK FUNCTIONS
*/
// GENERATE NOTIFICATION
function generate_notification ($user_list, $period) {
// IF THERE ARE PEOPLE TO EMAIL
	if ($user_list) {
		$report_date_range = get_report_date_range($period);

// loop through each user in the list
	foreach ($user_list as $user_id) {
		$user_data = get_userdata($user_id);
		$user_email = $user_data -> user_email;
		$user_name = $user_data -> user_nicename;
		$content_formatted = "<p><strong>Dear $user_name,</strong></p> <p>Other people find your ideas interesting! Details below.</p>";
	// GET NEW IDEAS
		$ideas = get_ideas_created($user_id, $report_date_range);
		if(empty($ideas)) {
			 $ideas_formatted[0] = "<h2>Activity Summary for Your Ideas</h2>";		
			 $ideas_formatted[1] = "<p>No ideas created for this time period.</p>";		
		} // NO NEW IDEAS
		else {
			$ideas_formatted[0] = "<h2>Your Ideas Activity Summary</h2>";		
		//	$idea_count = count($ideas);
		//	$ideas_formmatted[0] .= "<p>Total New Ideas: $idea_count</p>";
			// get activity for each of these
			$counter = 1;
			foreach ($ideas as $idea) {
				$idea_id = $idea -> ID;				
				$idea_title = $idea -> post_title;
			// get consensus stuff
				$idea_consensus = get_current_consensus($idea_id);
				$yes_votes = $idea_consensus['votes_yes'];
				$no_votes = $idea_consensus['votes_no'];
				$total_votes = $yes_votes + $no_votes;
			// format it too				
				$ideas_formatted[$counter] = "<p><strong>$idea_title</strong>: <br /> Total Votes: $total_votes <br /> Yes Votes: $yes_votes   No Votes: $no_votes</p>";
				$counter++;		
				}  // END IDEAS LOOP
			} // END NEW IDEAS CREATED

		/*
		// TOPICS SECTION
		*/
/*
	$topics_formatted[0] = "<h2>Topics Activity Summary</h2>";
	$topics = get_topics_created($user_id, $report_date_range);
		if(empty($topics)) {
			$topics_formatted[1] = "<p>No new topics created for this time period.</p>";		
			} // NO TOPICS CREATED FOR TIME PERIOD
		else {
			$counter = 1;  // RESET FOR REUSE 
			foreach ($topics as $topic) {
				$topic_description = $topic->description;
				// get # topics
				// get # ideas created for those topics
				// format it too
				$topics_formatted[$counter] .= "<p>$topic_description</p>";
					$counter++;		
					} // END TOPICS LOOP
				} // TOPICS EXIST
*/
		// START FORMATTING EMAIL CONTENT
			$content_formatted .= implode('<br />', $ideas_formatted);
//			$content_formatted .= implode('<br />', $topics_formatted);
	
			$notification_data = array (
				'user_id' => $user_id,
				'user_name' => $user_name,
				'user_email' => $user_email,
				'content' => $content_formatted,
				'emailed_at' => date('Y-m-d H:i:s')
				);		
			send_notification($notification_data); 
			// because it's a loop, need to unset this for use w/ other users
			unset($ideas_formatted);
//			unset($topics_formatted);
		} // END LOOP THROUGH EACH USER TO BE EMAILED
	}// END USER NOTE LIST EXISTS
} // END GENERATE NOTIFICATION

// SEND NOTIFICATION
function send_notification($notification_data) {
	$to = $notification_data['user_email'];
	$subject = "Nomicly Activity Report on Your Ideas";
	$from = "support@nomic.ly";
	$content = $notification_data['content'];
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: ".$from." \r\n";
	//$headers .= "From: ".$from." \r\n"."BCC: james@nomic.ly \r\n";

	$send = wp_mail($to, $subject, $content, $headers);
	if (!$send) {
		$response = 0;
		}
	else {
		$response = 1;
		// UPDATE THE USER_NOTE_TABLE WITH MOST RECENT LAST_EMAILED_AT...
		$update = update_user_note_record($notification_data['user_id'], $notification_data['emailed_at']);		
			if ($update == 0) {
				$response = 3;
			}
		}
	return $response;
} // END SEND NOTIFICATION

function update_user_note_record($user, $date) {
	global $wpdb;
	$table = $wpdb->prefix."user_note_prefs";
	
	$update_data = array (
		'last_emailed' => $date
		);
	$where = array (
		'user_id' => $user
		);
	$update = $wpdb->update( $table, $update_data, $where, $format = null, $where_format = null );
	if (!$update) {
		$response = 0; // ERROR, SOMETHING BROKE
		}
	else {
		$response = 1; // SUCCESSFUL
		}
	return $response;
}

/*
// GET REPORT DATE RANGE
*/
function get_report_date_range ($period) {
	$reporting_date = date('Y-m-d');  // what we share w/ the user
	$start_date = date('Y-m-d H:m:s', strtotime('+8 hours')); // account for GMT... :(
		if ($period == 1) {	
			$end_date = date('Y-m-d', strtotime('-1 day'));
			}
		else {
			$end_date = date('Y-m-d', strtotime('-7 days'));
			}	
	$time_period = array (
		'start_date' => $start_date,
		'end_date' => $end_date,
		'reporting_date' => $reporting_date
		);
	return $time_period;
} // END DATE RANGE

// GET IDEAS CREATED
function get_ideas_created ($user, $time_period) {
	global $wpdb;
	$start_date = $time_period['start_date'];
	$end_date = $time_period['end_date'];
// GETTING THE RANGE ONLY
// poor support in get_posts() so making two roundtrips to server...
	$relevant_ideas = $wpdb->get_col(
		"SELECT ID FROM nomicly_posts 
		WHERE post_date 
		BETWEEN '$end_date' AND '$start_date' 
		AND post_author = $user");
	if (!empty($relevant_ideas)) {	
		// GET THE NEW IDEAS IN POST OBJECT FORMAT
		$relevant_ideas = implode(',', $relevant_ideas);			
			$post_args = array (
				'author' => $user,
				'orderby' => 'post_date',
				'order'   => 'DESC',
				'include' => $relevant_ideas
			); 
			$ideas = get_posts( $post_args );
		} // HAS NEW IDEAS FOR PERIOD
	return $ideas;	
} // END GET IDEAS CREATED

function get_topics_created ($user,$report_date_range) {
	global $wpdb;
	$start_date = $report_date_range['start_date'];
	$end_date = $report_date_range['end_date'];
	$table_user_topics = $wpdb -> prefix."user_topics";
	$table_topics = $wpdb -> prefix."term_taxonomy";
// LOOKS LIKE JOINS WERE COMPLICATING THE DATA OUTPUT
// SO GOING OT MAKE TWO TRIPS TO DB... :(
	$topics = $wpdb -> get_col(
		"SELECT topic_id 
		FROM $table_user_topics 
		WHERE user_id = $user
		AND created_at BETWEEN '$end_date' AND '$start_date'"
		);
		// PREP DATA
			// for now using get_category
			// future should be to explore get_term
			// last attempt here worked but was outputting header errors (typically a sign something is wrong)
			/*
					$counter = 0;
		$taxonomy = 'category';
		foreach ($topics as $topic) {
			$term_id = $topic[0];
			$term_data = get_term($term_id, $taxonomy);
			$topic_data[$counter] = $term_data;
			$counter++;
			$last_topic = $term_data -> description;
		}	// END OF TOPIC DATA PREP
		*/
	if ($topics) {
		// going to loop through each and create an array of the topic data 
		$counter = 0;
//		$taxonomy = 'category';
		foreach ($topics as $topic) {
			$term_id = $topic[0];
			$term_data = get_category($term_id);
			$topic_data[$counter] = $term_data;
			$counter++;
			$last_topic = $term_data -> description;
		}	// END OF TOPIC DATA PREP
	} // END TOPICS EXIST
	$to = "james.dipadua@gmail.com";
	$subject = "debug email - getting topics";
	$from = "support@nomic.ly";
	$content = "this is an email from inside get_topics_created. start date = $start_date. end date = $end_date.   term id = $term_id.   if topic_data, = 1 -->  ".print_r($topic_data)."<br /> last topic description = $last_topic";
	$headers = "From: ".$from." \r\n";
	
	$send = wp_mail($to, $subject, $content, $headers);


	return $topic_data;
} // END GET TOPICS CREATED

/*
// GET MOST LIKED IDEAS
	// takes in idea_array
	// loops through each and gets the vote type
	// collates all votes for a specific type (like getting the consensus for an idea but relative only to that time period)
	// --> will want "votes this week" and then total votes" --> will make a call to current_consensus()
*/
function get_most_liked_ideas ($idea_array) {

} // END GET MOST LIKED IDEAS

// MOST DISLIKED IDEAS
function get_most_disliked_ideas ($user) {

} // END MOST DISLIKED IDEAS

// NUM AGREED
function get_num_agreed ($idea) {

} // END NUM AGREED

// get_num_disagreed
function get_num_disagreed ($idea) {

} // END get_num_disagreed

// get_new_topic_ideas
function get_new_topic_ideas ($topic) {

} // END get_new_topic_ideas

// get similar people
function get_similar_people ($user) {

} // END get_similar_people 

// get_contrarians
function get_contrarians ($user) {

} // END get_contrarians

?>