<?php
/*
Plugin Name: Nomicly Report Notifications
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
add_action( 'user_register', 'set_new_user_note_prefs' ); 


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
	//nomicly_reporting();
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
		// 0 = NO CONTACT, 1 = DAILY, 2 = WEEKLY
			$initial_user_data = array (
				'user_id' => $user_id,
				'sub_type' => '2',
				'updated_at' => $date
				);
			$wpdb->insert( $table_user_note_prefs, $initial_user_data );
		}// END FOR EACH
	}// USERS EXIST
}// END USER NOTE PREF SETUP

// add new users to user notifications
function set_new_user_note_prefs($user_id) {
// 0 = NO CONTACT, 1 = DAILY, 2 = WEEKLY
	global $wpdb;
	$table = $wpdb ->prefix."user_note_prefs";
	$date = date('Y-m-d H:i:s');

	$initial_user_data = array (
		'user_id' => $user_id,
		'sub_type' => '2',
		'updated_at' => $date
		);
	$wpdb->insert( $table, $initial_user_data );
} // END SETUP NEW USER NOTE PREFS

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

// GET LIST
function get_user_note_list($sub_type) {
// USE PERIOD TYPE TO DETERMINE WHAT USERS TO RETURN
// 0 = NO CONTACT, 1 = DAILY, 2 = WEEKLY
	global $wpdb;
	$table_note_prefs = $wpdb->prefix."user_note_prefs";
	$user_note_list = $wpdb->get_col("SELECT user_id FROM $table_note_prefs WHERE sub_type = '$sub_type'");
	/*	if (!$user_note_list) {
				$user_note_list = array (
					'user_note_response' => "Error"
					);
			} 
		*/
		return $user_note_list; // ARRAY OF USERS (AS IDs)
} // END GET LIST

// GENERATE NOTIFICATION
function generate_notification ($user_list, $period) {
	$blog_url= get_bloginfo('wpurl');
// IF THERE ARE PEOPLE TO EMAIL
	if ($user_list) {
		$report_date_range = get_report_date_range($period);

	/*
	// GENERAL NOMICLY ACTIVITY SECTION
		// this gets stuff like the actively participated ideas
		// (that's all it is for v1)
		// COMMON TO ALL USERS
		// SO DOING IT HERE (before we dig into individual users).
	*/
	$active_ideas = get_active_ideas($report_date_range);
	if (!empty($active_ideas)) {
		$active_ideas_formatted[0] = "<h3 style='padding: 10px; margin:25px 0;background:#eeeeee'>Trending Ideas on Nomicly</h3>";
			foreach($active_ideas as $idea) {
				$idea_id = $idea[0];
			$idea_data = get_post($idea_id, ARRAY_A);
			$active_ideas_formatted[$idea_id] .= "<a href=".$blog_url.$idea_data['post_name'].">".$idea_data['post_title']."</a><br />";			
		}
	}

// loop through each user in the list
	foreach ($user_list as $user_id) {
		$user_data = get_userdata($user_id);
		$user_email = $user_data -> user_email;
		$user_name = $user_data -> user_nicename;
		$content_formatted = "<p><strong>Dear $user_name,</strong></p> <p>Other people find your ideas interesting!<br/><a href='".$blog_url."/user-profile/'>Share</a> your ideas with others to get even more votes.</p>";
	// IDEA RELATED REPORTING
		$ideas_formatted[1] .= get_users_ideas_activity($user_id, $report_date_range);

/*
// TOPICS SECTION
*/
	// TOPICS IS A BIT WONKY ATM SO GOING TO DEPRECATE FOR TIME BEING
	// IT WILL BE NEEDED IN THE FUTURE THROUGH!
/*
	$topics_formatted[0] = "<h2>Activity Summary for Your Topics</h2>";
	$topics = count_topics_created($user_id, $report_date_range);
		if($topics == 0) {
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
	/*
	// THEN GET ANY NEW IDEAS OR HIGHLY ACTIVE IDEAS FOR A TOPIC THE USER CREATED
		// eventually, this will include activity for topics the user is "following"
		// see : get_user_topics ()
	*/
	$content_end = "<p style='font-size:11px;margin-top:25px;'>You can change or unsubscribe from email notifications on your <a href='".$blog_url."/user-profile/'>Nomicly account page.</a></p>";

		// START FORMATTING EMAIL CONTENT
		//the salutation is above so it needs to concatinate
			$content_formatted .= implode('<br />', $ideas_formatted);
	//		$content_formatted .= implode('<br />', $topics_formatted);
			$content_formatted .= implode('<br />', $active_ideas_formatted);
			$content_formatted .= $content_end;
			
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

// UPDATE USER NOTIFICATION RECORD
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
}// END UPDATE USER NOTE RECORD

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

// COUNT IDEAS CREATED
function count_ideas_created ($user, $time_period) {
	global $wpdb;
	$start_date = $time_period['start_date'];
	$end_date = $time_period['end_date'];
	$idea_count = $wpdb->get_var(
		"SELECT count(ID) FROM nomicly_posts 
		WHERE post_date 
		BETWEEN '$end_date' AND '$start_date' 
		AND post_author = $user");
	return $idea_count;	
} // END GET IDEAS CREATED

function count_topics_created ($user,$report_date_range) {
	global $wpdb;
	$start_date = $report_date_range['start_date'];
	$end_date = $report_date_range['end_date'];
	$table_user_topics = $wpdb -> prefix."user_topics";
	$topic_count = $wpdb -> get_var(
		"SELECT count(topic_id) 
		FROM $table_user_topics 
		WHERE user_id = $user
		AND created_at BETWEEN '$end_date' AND '$start_date'"
		);

	return $topic_count;
} // END GET TOPICS CREATED

/* 
//	GET ACTIVITY FOR IDEAS CREATED BY USER FOR SPECIFIC TIME PERIOD
*/
function get_users_ideas_activity ($user, $date_range) {
	global $wpdb;
	$query_args = array(
		'author' => $user,
		);
	query_posts( $query_args ); 	
		while ( have_posts() ) : the_post();  
			$idea_id = get_the_ID();
			$activity_count = get_idea_activity($idea_id, $date_range);
				if ($activity_count > 0) {
				$ideas_activity['intro'] = "<h3 style='padding: 10px; margin:25px 0;background:#eeeeee'>Activity On Your Ideas</h3>";
					$idea_consensus = get_current_consensus($idea_id);
					$yes_votes = $idea_consensus['votes_yes'];
					$no_votes = $idea_consensus['votes_no'];
					$total_votes = $yes_votes + $no_votes;
					// format stuff
						// -- SHOULD BE A TABLE
						//	  HEADINGS: idea name (as link) Recent votes, Positive Votes, Negative, Total
						// 		if it's formatted so that each idea is a row then we can collapse them all into one object to return rather than an array
						//		that'd be preferred.
					 $ideas_activity[$idea_id] = "<p><a href=".get_permalink().">".get_the_title()."</a></p>";
					 $ideas_activity[$idea_id] .= "<table width='600' cellsspacing='0' cellpadding='5'><tr><th><b>Recent Votes:</b></th><th><b>Total Positive Votes:</b></th><th><b>Total Negative Votes:</b></th></tr>";
					 $ideas_activity[$idea_id] .= "<tr><td>$activity_count</td>";
					 $ideas_activity[$idea_id] .= "<td>$yes_votes</td>";
					 $ideas_activity[$idea_id] .= "<td>$no_votes</td></tr></table>";
				}
	endwhile;
	 	if ($ideas_activity) {
	 		$aggregate_idea_activity = implode('<br />', $ideas_activity);
	 		}
	 return $aggregate_idea_activity;
} // END GET ACTIVITY FOR A *USERS* IDEAS

function get_idea_activity ($idea, $date_range) {
 global $wpdb;
 $table = $wpdb -> prefix."user_idea_votes";
 $start_date = $date_range['start_date'];
 $end_date = $date_range['end_date'];
 
 $recent_activity_count = $wpdb -> get_var(
 	"SELECT count(vote_id) 
 	FROM $table 
 	WHERE idea_id = $idea 
 	AND created_at BETWEEN '$end_date' AND '$start_date'"
 	);

 return $recent_activity_count;
} // END GET *IDEA* ACTIVITY

/*
//	ACTIVE IDEAS SECTION
	// stuff like most voted-for ideas (for a given reporting period)
*/
// GET ACTIVE IDEAS
function get_active_ideas ($date_range) {
 global $wpdb;
 $table = $wpdb -> prefix."user_idea_votes";
 $start = $date_range['start_date'];
 $end = $date_range['end_date'];
 
	$active_ideas = $wpdb -> get_results (
		"SELECT idea_id
		FROM $table 
		WHERE created_at BETWEEN '$end' AND '$start'
		GROUP BY idea_id
		ORDER BY count(vote_id) DESC
		LIMIT 10", ARRAY_N );
	
	return $active_ideas;
} // END GET ACTIVE IDEAS
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