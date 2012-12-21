<?php
 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// THIS GETS THE AJAX URL SET FOR ALL PAGES (BUT LOGIN)
add_action('wp_head','create_ajaxurl');
// THIS GETS THE AJAX URL SET FOR THE LOGIN PAGE
add_action('login_enqueue_scripts', 'create_ajaxurl');
//////
	function create_ajaxurl() {?>
		<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?
	}

// REDIRECT WORK
add_action('admin_enqueue_scripts', 'redirect_non_admins');

function redirect_non_admins () {
	if( !current_user_can('create_users')) {
		$new_location = get_bloginfo( 'wpurl' );
		$stats = 302;
		wp_redirect( $new_location, $status );
		exit;
	} 
	return;
} // END REDIRECT NON ADMINS

/*
// this is for getting custom post types
// and displaying them on the home page
// DEPRECATION CANDIDATE
*/
//add_filter( 'pre_get_posts', 'my_get_posts' );

function my_get_posts( $query ) {
	if ( is_home() && false == $query->query_vars['suppress_filters'] )
		$query->set( 'post_type', array( 'post', 'ideas' ) );
	return $query;
}// END FILTERED LOOP

add_filter('query_vars', 'add_query_vars' );

function add_query_vars( $query_variables){
$query_variables[] = 'idea';
return $query_variables;
}

/*
//	REGISTRATION AUGMENTATION FOR VOTING
//	ADD USERS TO THE USER VOTE CACHE 
// 	&& GIVE NEW VOTES TO NEW USERS AFTER REGISTERING
// 	- ACTUAL FUNCTION DOWN WITH THE REST OF THE CUSTOM VOTING STUFF
*/
add_action( 'user_register', 'grant_new_user_votes' ); 

/*
//	PROFILE EDITING
*/
	// EMAIL
function update_user_email($user, $email) {
	require_once( ABSPATH . WPINC . '/registration.php');
	$user = $user_id;
	$new_email = $email;
	$udpate = wp_update_user( array ('ID' => $user_id, 'user_email' => $new_email) ) ;
	if(!$update) {
		$message = "Failed to Save New Email";
	}
	else {
		$message = "New Email Saved";
	}
	return $message;
} // END UPDATE EMAIL

	// PASSWORD
function update_user_password($user, $password_old, $password_new) {
	require_once( ABSPATH . WPINC . '/registration.php');
	$user_id = $user;
	//$current_password = get_user_password($user_id);
	$old_password = password_encode($password_old);
	// VERIFY PASSWORDS MATCH
	if (!$old_password == $current_password) {
		$message = "Sorry, the password you provided as your current password does not match. Please try again.";
	} // END NO MATCH
	else {
		$new_password = password_encode($password_new);
		
		$udpate = wp_update_user( array ('ID' => $user_id, 'user_password' => $new_password) ) ;
		if(!$update) {
			$message = "Failed to Save New Password";
		}
		else {
			$message = "New Password Saved";
		}
	} // END SUCCESS + passwords match
	return $message;
} // END UPDATE PASSWORD



/*
/// CREATE NEW IDEAS
*/

function nomicly_new_idea () {
	global $wpdb;
//need to get user info to connect the topic/idea to the user 
//potentially redundant but using wp-core functions to reduce impact
	$userID = $_POST['user_id'];	
	$post_date = date('Y-m-d H:i:s');

// POTENTIAL BUG
// hardcoded for main feed
	if (!empty($_POST['post_parent'])) {
		$post_parent = $_POST['post_parent'];
		}
	else {
		$post_parent = 0;
	}
	//make the title safe for mysql
	$post_title = wp_strip_all_tags($_POST['new_idea']);	
	//create the slug
	$post_name = sanitize_title( $post_title );
	//CREATE NEW POST
	$post = array(
	  'comment_status' => 'open',  // 'closed' means no comments.
	  'ping_status'    => 'closed',  // 'closed' means pingbacks or trackbacks turned off
	  'post_author'    => $userID , // user ID of  author.
	  'post_date'      => $post_date,  //The time post was made.
	  'post_date_gmt'  => $post_date , //The time post was made, in GMT. (just using same time)
	  'post_name'      => $post_name, // The name (slug) for your post
	  'post_parent'    => $post_parent, //Sets the parent of the new post. 
	  'post_status'    => 'publish', //Set the status of the new post.
	  'post_title'     =>  $post_title, //The title of your post.
	  'post_type'      => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
//	  'tax_input'      => array( 'term_taxonomy_id' => $category_id ) ]
			);  //END POST ARRAY
	// INSERT POST 
	$new_post_id = wp_insert_post( $post, $wp_error ); 
	// set category terms
	//setup category stuff
	if (!empty($_POST['category_id'])) {
	// get the right categories to put this post into
		$category_id = $_POST['category_id'];
	}
	else {
	// even stuff in "main" needs to have the category assigned
	// category_id = 0 is "uncategorized"
	// 1 = 'main'
		$category_id = 1;
	}
	// UPDATE POST TERMS
	wp_set_post_terms($new_post_id, $category_id, 'category', FALSE);
	
	// PUT IDEA INTO IDEA_CONSENSUS SO USERS WON'T GET 'NULL' ON UN-VOTED-FOR IDEAS 
	initialize_idea_consensus($new_post_id);

		return $new_post_id;
// BUG
// empty post array by redirecting to fresh version of page
//	header('Location: http://www.jamesdipadua.com/experimental/nomicly/index.php');
}// END NEW IDEA

/*
** THE FOLLOWING IS FOR THE HOT OR NOT GAME
*/

function nomicly_record_vote() {
 global $wpdb;

$table_votes = $wpdb->prefix."hot_not_votes";
$table_pairs = $wpdb->prefix."hot_not_pairs"; 
// I want to make sure I don't have duplicate entries for the same pair of ideas
// SO i'm going to sort the ideas (lowest, highest)
// this will allow me to always operate on a standardized pair format
 $idea_1 = $_POST['idea0'];
 $idea_2 = $_POST['idea1'];
 $chosen_idea = $_POST['chosen_idea'];
//Finess the ideas for db stuff 
 $idea_array = array ($idea_1, $idea_2);
 // THE SORT
 sort($idea_array, SORT_NUMERIC);
 // STANDARDIZATION OF FORMAT
 $idea_pair = implode(",", $idea_array);
 
//get the pair_id to process the insert correctly 
	$pair_id = $wpdb->get_var("SELECT pair_id FROM $table_pairs WHERE idea_pair = '$idea_pair'");
// then we're going to either insert a new pair, vote and vote-count 
// or we're going to just insert a vote record and a vote-count update
	if (empty($pair_id)) {
		$new_pair_id = insert_new_pair ($idea_pair); 
			$pair_id = $new_pair_id;
		$vote_id = insert_vote($new_pair_id, $chosen_idea);
		update_pairs($new_pair_id, $idea_array, $chosen_idea);
	 }
	else {
		$vote_id = insert_vote($pair_id, $chosen_idea);
		update_pairs($pair_id, $idea_array, $chosen_idea);	
	}
	$pair_stats = array (
		'pair_id' => $pair_id,
		'idea_1' => $idea_array[0],
		'idea_2' => $idea_array[1]
		);
	return $pair_stats;
}// END NOMICLY_RECORD_VOTE()

function insert_new_pair($pair) {
	global $wpdb;
	$idea_pair = $pair;
	$table_pairs = $wpdb->prefix."hot_not_pairs"; 
	$date = date('Y-m-d H:i:s');
	$pair_data = array (
			'idea_pair' => $idea_pair,
			'idea_1_count' => 0,
			'idea_2_count' => 0,
			'updated_at' => $date	
	);
	$wpdb->insert( $table_pairs, $pair_data );
	//now get the ID for the newly inserted pair
	$pair_id = $wpdb->insert_id;
	return $pair_id;
}// END INSERT_NEW_PAIR

function insert_vote($pair, $chosen) {
	global $wpdb;
	$user_id = get_current_user_id();
	$date = date('Y-m-d H:i:s');
	$pair_id = $pair;
	$chosen_idea = $chosen;
	$table_votes = $wpdb -> prefix.'hot_not_votes';

//prep data for insert
	$vote_data = array (
		'chosen_id' => $chosen_idea,
		'pair_id' => $pair_id,
		'user_id' => $user_id,
		'time' => $date
	);
	// insert vote data into db
	$wpdb->insert( $table_votes, $vote_data );
	// get vote_id and return for update_pairs function
	$vote_id = $wpdb -> insert_id;
	return $vote_id;
	
} //END INSERT_VOTE

function update_pairs($pair, $ideas, $chosen_idea) {
	global $wpdb;
	$pair_id = $pair;
	$idea_array = $ideas;
	$winning_idea = $chosen_idea;
	$table_pairs = $wpdb->prefix."hot_not_pairs"; 
	$date = date('Y-m-d H:i:s');

 $winner = determine_winner ($idea_array, $winning_idea);
 $winner = intval($winner);
 //NOTE THIS SHOULD  be using wp query
 // BUT IT WASN'T LETTING ME DO THE UPDATE THE WAY I WANTED
 // THAT IS, WITH COL = COL+1... 
 // so just wrote the query i wanted to use
	if ($winner == 1) {
		$query = "UPDATE nomicly_hot_not_pairs 
			SET idea_1_count = idea_1_count+1, updated_at = '$date' 
			WHERE pair_id = '$pair_id'";
		$update_query = mysql_query($query);
			if (!$update_query ) {
				echo mysql_error();
				}	
			/*	 $wpdb->query( 
			$wpdb->prepare( 
				"UPDATE $table_pairs 
         		SET (
        		idea_1_count = idea_1_count+1,
 	       		updated_at = '$date'
 	       		)
		  		WHERE pair_id = '$pair_id'"
			) 
		);  */
	} //END IDEA 1 COUNT
	else if ($winner == 2) {
			$query = "UPDATE nomicly_hot_not_pairs 
			SET idea_2_count = idea_2_count+1, updated_at = '$date' 
			WHERE pair_id = '$pair_id'";
		$update_query = mysql_query($query);
// NEED BETTER ERROR HANDLING THAN THIS...
			if (!$update_query ) {
				echo mysql_error();
				}
		}//END IDEA 2 COUNT
}// END UPDATE PAIRS

/*
// DETERMINE WINNER OF HOT/NOT CHOICE
//figure out winning choice and then do the insert specific to that winner
// can make it more elegant later...
*/
function determine_winner ($ideas, $chosen_idea) {
	$idea_array = $ideas;
	$winner = $chosen_idea;
	$winner = intval($winner);

// going to loop through this. 
// better ways to do this but i was futzing before
// and don't want to optimize now...	
	$counter = 0;	
	$count = count($idea_array);
	while ($counter < $count) {
		if ($idea_array[$counter] == $winner) {
			$winning_idea = $counter;
		}
	$counter++;
	} // END WHILE
	// fix the count on winning idea (add 1)
	// it's an easier human number to work with (at least for me)
	$winning_idea++;	
	return $winning_idea;
}// END DETERMINE_WINNER

/* 
//  HOT OR NOT 
//  PAIR STATISTICS 
//  1. GET THE PAIR IN QUESTION
//  2. FIND OUT THE TOTAL NUMBER OF VOTES ON THE PAIR
//  3. FIND OUT THE PERCENTAGE WON FOR EAHC OF THE PAIRS
//  	a. DOESN'T EVEN NEED TO BE "20% OF PEOPLE CHOSE THE SAME AS YOU"
// 		b. JUST FIND THE PERCENTAGE SELECTED FOR BOTH AND SHOW THAT (kiss)
*/
function get_hot_not_stats($pair) {
	global $wpdb;
	$pair_id = $pair;
	$table_pairs = $wpdb -> prefix.'hot_not_pairs';

	$pair_data = $wpdb->get_row("SELECT * FROM $table_pairs WHERE pair_id = '$pair_id'");

	$idea_1_count = $pair_data -> idea_1_count;
	$idea_2_count = $pair_data -> idea_2_count;
	$total_votes = $idea_1_count + $idea_2_count;
	// we don't want to return infinite results\
	// IDEA 1
	if ($idea_1_count > 0) { 
		$idea_1_consensus = ($idea_1_count / $total_votes) * 100;
	}
	else {
		$idea_1_consensus = 0;
		}
	// IDEA 2
	if ($idea_2_count > 0 ) { 
		$idea_2_consensus = ($idea_2_count / $total_votes) * 100;
	}
	else {
		$idea_2_consensus = 0;
		}	
		// get a string version
		$idea_1_consensus_percentage = $idea_1_consensus."%";
		$idea_2_consensus_percentage = $idea_2_consensus."%";	
	
	$pair_vote_history = array (
		'pair_id' => $pair_id,
		'idea_1_count' => $idea_1_count,
		'idea_2_count' => $idea_2_count,
		'total_votes' => $total_votes,
		'idea_1_consensus' => $idea_1_consensus,
		'idea_1_consensus_percentage' => $idea_1_consensus_percentage,
		'idea_2_consensus' => $idea_2_consensus,
		'idea_2_consensus_percentage' => $idea_2_consensus_percentage,
	);
	return $pair_vote_history;
}

/* 
//  CREATE NEW TOPICS FROM WITHIN NOMICLY
//	on the topics page, allow people create new topics automatically
*/

function create_new_topic() {
	global $wpdb;
	$user_id = get_current_user_id();
	$date = date('Y-m-d H:i:s');
	$new_topic_name = wp_strip_all_tags($_POST['new_topic_name']);	
	$new_topic = wp_strip_all_tags($_POST['new_topic']);	
	//create the slug
	$new_topic_slug = sanitize_title( $new_topic );
	
	$new_topic_data = array (
	'cat_name' => $new_topic_name, 
	'category_description' => $new_topic, 
	'category_nicename' => $new_topic_slug,
	'category_parent' => '',
	 'taxonomy' => 'category'
	);
	$new_term_id = wp_insert_category($new_topic_data);
	
	$table_user_topics = $wpdb -> prefix.'user_topics';
// CONNECT TOPIC TO USER WHO CREATED IT
	$user_topic_data = array (
		'user_id' => $user_id,
		'topic_id' => $new_term_id,
		'created_at' => $date
	);
	$wpdb->insert( $table_user_topics, $user_topic_data);
	
	// NOTE TO MAKE IT APPEAR WITHOUT A BUG, YOU HAVE TO CREATE A DUMMY IDEA TOO 
	// FUCKING RETARDED...
	// quick copy paste to reduce my already high annoyance with this bug
	$post_parent = 0;
	$post_title = "This is an example idea for ".$new_topic_name;
	// create slug
	$post_name = sanitize_title( $post_title );
	//CREATE NEW POST
	$post = array(
	  'comment_status' => 'open',  // 'closed' means no comments.
	  'ping_status'    => 'closed',  // 'closed' means pingbacks or trackbacks turned off
	  'post_author'    => 3 , // 3 == USER 'nomicly' ON "nomic.ly"
	  'post_date'      => $date,  //The time post was made.
	  'post_date_gmt'  => $date , //The time post was made, in GMT. (just using same time)
	  'post_name'      => $post_name, // The name (slug) for your post
	  'post_parent'    => $post_parent, //Sets the parent of the new post. 
	  'post_status'    => 'publish', //Set the status of the new post.
	  'post_title'     =>  $post_title, //The title of your post.
	  'post_type'      => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
//	  'tax_input'      => array( 'term_taxonomy_id' => $category_id ) ]
			);  //END POST ARRAY
	// INSERT POST 
	$new_post_id = wp_insert_post( $post, $wp_error ); 
	// CONNECT THE POST TO THE CATEGORY SO THE FUCKING ARRAY WILL WORK PROPERLY
	// UPDATE POST TERMS
	wp_set_post_terms($new_post_id, $new_term_id, 'category', FALSE);
	
}// END CREATE NEW TOPIC

/*
// GET TOPICS CREATED BY A SPECIFIC USER
*/
function get_user_topics($user_id) {
	global $wpdb;
	$table_user_topics = $wpdb->prefix."user_topics";
	$user = $user_id;
	$topic_query_results = $wpdb->get_col(
		"SELECT topic_id 
		FROM $table_user_topics 
		WHERE user_id = '$user_id'",
		ARRAY_N); 
	// collapse the results for the next query
if (!empty($topic_query_results)) {
	$user_topics = $topic_query_results[0];
	$user_topics = implode(",", $user_topics);
	$args=array(
	  'orderby' => 'name',
	  'order' => 'ASC',
	  'hide_empty' => 0,
	  'include' => $user_topics
	);
	$categories=get_categories($args);

	foreach($categories as $category) { 
		echo '<p><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </p>'; }
		//echo '<p> Description:'. $category->description . '</p>';  

		}// END USER TOPIC DISPLAY
	else {
		echo "<p>You haven't created any topics. <br /> 
			  See how Nomicly can help solve problems by creating a discussion topic.</p>";
		}// END NO TOPICS BY USER
} // END GET USER TOPICS
/*
// COUNT USER CREATED TOPICS
*/
function count_user_topics ($user_id) {
	global $wpdb;
	$user = $user_id;
	
	$topic_count_query = $wpdb->get_results(
		"SELECT count(topic_id) 
		FROM nomicly_user_topics 
		WHERE user_id = '$user'", ARRAY_N);
	$topic_count = $topic_count_query[0][0];
	return $topic_count;
}

/*
// MODIFY IDEAS
// to modify an idea, you just have to create a post
// the create idea function handles all ancestry and category stuff
	// note that term_relationships (tbl) has an 'object_id' 
	// object_id corresponds to POST-ID!! (wtf?)
	// see: http://www.dagondesign.com/articles/wordpress-23-database-structure-for-categories/
	
	// design thinking:
	// populate a textarea w/ said post
	// allow user to edit text
	// submit the text
	// do a quick regex to verify uniqueness (NOT DONE)
	// insert the idea into the posts 
	// if in category/topic, then add to that category/topic
	
	// then want to redirect the user to that new idea (NOT DONE)
	// will use a header redirect
	// get the new slug from the post
	// do a query to the db for a slug == to new slug
	// wam-bam, thank you, ma'am.
	// will want to confirm to the user that the new idea was created
	// use a basic alert for now and make better-er later
	// 
*/

function nomicly_modify_idea ()  {
	$new_post_id = nomicly_new_idea();
	return $new_post_id;
}  // END MODIFY IDEAS


/* 
// UPDATE TERM_RELATONSHIPS

// i *thinK* this function can be completely deleted
// it essentially mimics the behavior of wp_set_post_terms()
// but i'm not sure if this is being used anywhere right now
// bug for addressing later.
*/
function nomicly_update_term_relationships ($object_id, $cat_id) {
	global $wpdb;
	$new_post_id = $object_id;
	$category_id = $cat_id;
	$table_term_relationships = $wpdb->prefix."term_relationships"; 

	$term_data = array (
		'object_id' => $new_post_id,
		'term_taxonomy_id' => $category_id,
		term_order => 0
		);
	$wpdb->insert( $table_term_relationships, $term_data );
}// END UPDATE_TERM_ RELATIONSHIPS


/*
* GET ANCESTRY INFORMATION
// 	NOTE: THIS IS JUST BASIC, 1-LEVEL ANCESTRY
// 	- NEED TO WRITE SO IT CAN RECURSIVELY COLLECT A FULL ANCESTRY TREE.
*/
function get_idea_ancestry($idea) {
	$idea_id = $idea;
	$idea_data = get_post($idea_id); 	
	$ancestor_id = $idea_data -> post_parent;
	if (empty($ancestor_id)) {
		$ancestry_data = array (
			'ancestor_status' => 0 // FALSE, NULL, NONE, NADDA
			);
		}
	else {
		$ancestry_data = array (
			'ancestor_status' => 1, // GOT 'EM
			'url' => get_bloginfo( 'wpurl' ).$idea_data -> post_name,
			'title' >= $idea_data -> post_title
			);	
		}
	return $ancestry_data;
}// END GET ANCESTORS


/* 
// CUSTOM VOTING SECTION
*/

/*
// GRANT NEWLY REGISTERED USERS SOME VOTES
*/
function grant_new_user_votes ($user_id) {
	global $wpdb;
	$user = $user_id;
	$table_user_cache = $wpdb->prefix."user_vote_cache";
	$award_amount = 10;
	
	//  POPULATE INTO USER_VOTE_CACHE
	//  && GIVE THEM VOTES
		$initial_user_data = array (
		'user_id' => $user,
		'num_votes_avail' => $award_amount,
		'created_at' => $date,
		'updated_at' => $date
		);
		$wpdb->insert( $table_user_cache, $initial_user_data );
} // END NEW USER VOTE GRANT

/*
* RECORD_IDEA_VOTE - records the person's choice on an idea
*/
function record_idea_vote() {
	global $wpdb;
	$table_user_idea_votes = $wpdb->prefix."user_idea_votes"; 
	$user_id = get_current_user_id();
	$idea_id = $_POST['idea_id'];
	$vote_type = $_POST['vote_type'];
	$date = date('Y-m-d H:i:s');

// 1. Record the actual vote
	$vote_data = array (
		'idea_id' => $idea_id,
		'user_id' => $user_id,
		'vote_type' => $vote_type,
		'created_at' => $date,
		'updated_at' => $date
		);
	$wpdb->insert( $table_user_idea_votes, $vote_data );
	// $vote_id = $wpdb -> insert_id;
	
// 2. Take away a vote from user cache
	decrease_available_votes($user_id);

// 3. Update the idea_consensus
	$new_consensus = update_idea_consensus($idea_id, $vote_type);
	return $new_consensus;	
} // END RECORD IDEA VOTE

/*
// GET CURRENT CONSENSUS
*/
function get_current_consensus($idea_id) {
	global $wpdb;
	//$table = $wpdb -> prefix."idea_consensus";
	$idea = $idea_id;
	$idea_data = $wpdb -> get_row("SELECT * FROM nomicly_idea_consensus WHERE idea_id = '$idea'");
	$votes_yes = $idea_data -> votes_yes;
	$votes_no = $idea_data -> votes_no;
	$updated_at = $idea_data -> updated_at;
	
	$idea_stats = array (
		'idea_id' => $idea,
		'votes_yes' => $votes_yes,
		'votes_no' => $votes_no,
		'updated_at' => $updated_at
		);
	return $idea_stats;
}  // END GET CURRENT CONSENSUS

/*
// INSERT IDEA INTO IDEA CONSENSUS TABLE
*/
function initialize_idea_consensus($idea_id) {
	global $wpdb;
	$table_idea_consensus = $wpdb -> prefix."idea_consensus";
	$idea = $idea_id;
	$date = date('Y-m-d H:i:s');
	$initial_idea_data = array (
		'idea_id' => $idea_id,
		'updated_at' => $date
		);
	$wpdb->insert( $table_idea_consensus, $initial_idea_data );
	return;
} // END

 /*
 // UPDATE_IDEA_CONSENSUS - update the consensus for an idea based on a vote
*/
function update_idea_consensus($idea_id, $vote_type) {
	global $wpdb;
	$idea = $idea_id;
	$type = intval($vote_type);
	$date = date('Y-m-d H:i:s');

// before you can update a consensus, it has to exist.
// check for existence of idea in idea_consensus
// if not, insert it.
	$idea_id = $wpdb->get_var("SELECT idea_id FROM nomicly_idea_consensus WHERE idea_id = '$idea'");
// then we're going to either insert a new pair, vote and vote-count 
// or we're going to just insert a vote record and a vote-count update
	if (empty($idea_id)) {
		initialize_idea_consensus($idea);		
	 }

// 1. increase YES/NO depending on type
//	  yes = 1, no = 0
	// a. YES
	if ($type == 1) {
	$query = "UPDATE nomicly_idea_consensus
			SET votes_yes = votes_yes+1, updated_at = '$date' 
			WHERE idea_id = '$idea'";
	$update_query = mysql_query($query);
			if (!$update_query ) {
				echo mysql_error();
				}	
	}
	// b. NO
	elseif ($type == 0) {
	$query = "UPDATE nomicly_idea_consensus
			SET votes_no = votes_no+1, updated_at = '$date' 
			WHERE idea_id = '$idea'";
	$update_query = mysql_query($query);
			if (!$update_query ) {
				echo mysql_error();
				}		
	}

// 2. get the current consensus to return it
	$current_idea_stats = get_current_consensus($idea);	
	return $current_idea_stats;
} // END UPDATE CONSENSUS

/*
// GET_VOTE_RECORD - determine whether a person has voted on a specific idea or not
// TRUE = HAS VOTED = 1
// FALSE = NOT VOTED = 0
		// also returns the vote data (yes/no, date, etc) if TRUE
*/
function get_vote_record($user_id, $idea_id) {
	global $wpdb;
	$table = $wpdb ->prefix."user_idea_votes";
	$user = $user_id;
	$idea = $idea_id;
// 1. see if the user has voted
	$vote_data = $wpdb -> get_row("SELECT * FROM nomicly_user_idea_votes WHERE idea_id = '$idea' and user_id = '$user'");
	if (empty($vote_data)) {
		$vote_status = 0;
		$voter_record = array (
			'vote_status' => $vote_status,
			);
		return $voter_record;
		}
	else {
		$vote_status = 1;		
		$vote_id = $vote_data -> vote_id;
		$vote_type = $vote_data -> vote_type;
		$created_at = $vote_data -> created_at;
		$updated_at = $vote_data -> updated_at;
		
		$voter_record = array (
			'vote_status' => $vote_status,
			'vote_id' => $vote_id,
			'user_id' => $user,
			'idea_id' => $idea,
			'vote_type' => $vote_type,
			'created_at' => $created_at,
			'updated_at' => $updated_at
			);
		return $voter_record;
	}
} // END GET VOTE RECORD

/* 
// GET AVAILABLE VOTES
*/
function get_available_votes($user_id) {
	global $wpdb;
	$user = $user_id;
	$cache_count = $wpdb->get_var("SELECT num_votes_avail FROM nomicly_user_vote_cache  WHERE user_id = '$user'");
	return $cache_count;
} // END GET AVAILABLE VOTES

 /*
 // INCREASE AVAILABLE VOTES
 */
function increase_available_votes($user_id, $award_amount) {
	global $wpdb;
	$table = $wpdb -> prefix."user_vote_cache";
	$user = $user_id;
	$amount = $award_amount;
	$date = date('Y-m-d H:i:s');

// 1. check if user already at max_votes
	$avail_votes = get_available_votes($user);
	$max_votes = get_user_max_votes($user);
// 2. if so, end
	if ($avail_votes == $max_votes) {
		return;
	}
// 3. if NOT, award votes to user
	//	a. calc max award
	// 	b. if award_amount > max_votes, set new_vote_total = max_votes
	//		-- OTHERWISE, set new_vote_total = award_amount + old_amount
	//	c. then award
	//		-- NOTE, Quirky BUG:
				// have to calc the 'new total' 
				// because: num_votes_avail = num_votes_avail+'$amount' isn't supported
	$max_award_amount = $max_votes - $avail_votes;
		if ($amount > $max_award_amount) {
			$new_vote_total = $max_award_amount + $avail_votes;
			}	
		else {
			$new_vote_total = $amount + $avail_votes;
		}
	$query = "UPDATE nomicly_user_vote_cache
			SET num_votes_avail = '$new_vote_total', updated_at = '$date' 
			WHERE user_id = '$user'";
	$update_query = mysql_query($query);
			if (!$update_query ) {
				 $wpdb->show_errors(); 
				}	
} // END INCREASE AVAIL VOTES

 /*
 // DECREASE AVAILABLE VOTES
 */
function decrease_available_votes($user_id) {
	global $wpdb;
	$user = $user_id;
	$table_user_vote_cache = $wpdb->prefix."user_vote_cache"; 
	$date = date('Y-m-d H:i:s');

	// not using WP-QUERY due to lack of support for num = num -1 
	$query = "UPDATE nomicly_user_vote_cache 
			SET num_votes_avail = num_votes_avail-1, updated_at = '$date' 
			WHERE user_id = '$user'";
	$update_query = mysql_query($query);
			if (!$update_query ) {
				echo mysql_error();
				}	
// then get the count of avail. 
	$cache_count = get_available_votes($user);
//	if less than 0, set to 0
	if ($cache_count < 0) {
			$query = "UPDATE nomicly_user_vote_cache 
				SET num_votes_avail = 0, updated_at = '$date' 
				WHERE user_id = '$user'";
		$update_query = mysql_query($query);
				if (!$update_query ) {
					echo mysql_error();
					}	
	}// end check cache less than 0
} // END DECREASE AVAILABLE VOTES

	/*
	// GET MAX VOTES - find out how max num of votes a user can have
	*/
function get_user_max_votes($user_id) {
	global $wpdb;
	$table_user_vote_cache = $wpdb -> prefix.'user_vote_cache';
	$user = $user_id;
	$max_votes = $wpdb->get_var("SELECT max_votes FROM nomicly_user_vote_cache WHERE user_id = '$user'"); 
	return $max_votes;
} // END GET MAX VOTES

/*
	// CHANGE VOTE - for when people change their minds
	// NOT DONE
*/
function change_vote($user_id, $idea_id) {
	global $wpdb;
	$user = $user_id;
	$idea = $idea_id;
	$table_nomicly_user_idea_vote = $wpdb -> prefix."user_idea_vote";
	
	/*
	$query = "UPDATE '$table_nomicly_user_idea_vote'
			WHERE user_id = '$user' AND idea_id = '$idea'";
	$update_query = mysql_query($query);
			if (!$update_query ) {
				echo mysql_error();
				}	
				*/
} // END CHANGE VOTE


/* 
// AJAX
// this section of code encompasses the ajax requests and handling. 
*/
// embed the javascript file that makes the AJAX request
// but it doesn't fucking work. 
// wrote a hack into this functions.php file (first few lines)

function add_nomicly_js(){  
	wp_enqueue_script( 'nomicly.js', get_bloginfo('template_directory') . "/js/nomicly.js", array( 'jquery' ) );  
	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	// this little bit of code is SUPPOSE to make a global ajax object
	// BUT IT DOESN'T
	wp_localize_script( 'add_nomicly_js', 'nomiclyAJAX', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
// add the nomicly_js file to the initialization
add_action( 'init', 'add_nomicly_js' );  

function create_new_idea () {
	$new_idea_id = nomicly_new_idea();
	//get the content for that idea using get_post();
	$new_idea = get_post($new_idea_id);
	
	$new_idea_data = array (
		"new_idea_data" => $new_idea,
		);
	
	// CONVERT  TO JSON	
	$response_data = json_encode($new_idea_data);
	// response output
	die($response_data);	
}

add_action('wp_ajax_create_new_idea', 'create_new_idea');
// non-logged in user
add_action('wp_ajax_nopriv_create_new_idea', 'create_new_idea' );


function modify_existing_idea () {
	$new_idea_id = nomicly_modify_idea();
	//get the content for that idea using get_post();
	$new_idea = get_post($new_idea_id);
	
	$new_idea_data = array (
		"new_idea_data" => $new_idea
		);
	
	// CONVERT  TO JSON	
	$response_data = json_encode($new_idea_data);
	// response output
	die($response_data);	
}

add_action('wp_ajax_modify_existing_idea', 'modify_existing_idea');
// non-logged in user
add_action('wp_ajax_nopriv_modify_existing_idea', 'modify_existing_idea' );

/*
// DETERMINE VOTER STATUS
// - identifies whether a person has voted on an idea or not
*/

function determine_voter_idea_status () {
	$idea_id = $_GET['idea_id'];
	$user_id = get_current_user_id();
// NOT LOGGED IN, HAS TO REGISTER/LOGIN 	
	// SHOULD THIS MOVE TO ITS OWN FUNCTION/SPECIAL-CASE ?
	if (empty($user_id)) {
		$response = array(
			'voter_status_data' => "NULL"
			);
			$response_data = json_encode($response);
		}
	else {
// LOGGED IN USER
	$voter_status_data = get_vote_record($user_id, $idea_id);
	
	$voter_status_data = array (
		"voter_status_data" => $voter_status_data
		);
	// CONVERT  TO JSON	
	$response_data = json_encode($voter_status_data);
	}
	// response output
	die($response_data);	
} // END VOTER STATUS

add_action('wp_ajax_determine_voter_idea_status', 'determine_voter_idea_status');
// non-logged in user
add_action('wp_ajax_nopriv_determine_voter_idea_status', 'determine_voter_idea_status' );


function fetch_idea_consensus() {
	$idea_id = $_GET['idea_id'];
	$current_consensus = get_current_consensus($idea_id);
	$consensus_data = array (
		"consensus_data" => $current_consensus
		);
	// CONVERT  TO JSON	
	$response_data = json_encode($consensus_data);
	// response output
	die($response_data);	
}  // END FETCH IDEA CONSENSUS

add_action('wp_ajax_fetch_idea_consensus', 'fetch_idea_consensus');
// non-logged in user
add_action('wp_ajax_nopriv_fetch_idea_consensus', 'fetch_idea_consensus' );

function fetch_idea_ancestry() {
	$idea_id = $_GET['idea_id'];
	$idea_ancestry = get_idea_ancestry($idea_id);
	$ancestry_data = array (
		"ancestry_data" => $idea_ancestry
		);
	// CONVERT  TO JSON	
	$response_data = json_encode($ancestry_data);
	// response output
	die($response_data);	
}  // END FETCH IDEA CONSENSUS

add_action('wp_ajax_fetch_idea_ancestry', 'fetch_idea_ancestry');
// non-logged in user
add_action('wp_ajax_nopriv_fetch_idea_ancestry', 'fetch_idea_ancestry' );


function determine_user_available_votes() {
	if (isset($_POST['user_id'])) {
		$user_id = $_POST['user_id'];
	}
	else {
		$user_id = get_current_user_id();
	}	
	// JUST TO MAKE SURE THIS DOENS'T ERROR OUT DUE TO LACK OF A UESR
	// PUTTING IN A IS_LOGGED IN CHECK FIRST
	// RETURNING NULL IS HOW I'VE HANDLED THIS IN THE PAST
	// SO CONTINUING THAT HERE.
	if ( empty($user_id) ) { 
		$available_votes = "NULL";
		}		
	else {
		$available_votes = get_available_votes($user_id);
	}
	
	$available_votes_data = array (
		"available_votes_data" => $available_votes
		);
	
	// CONVERT  TO JSON	
	$response_data = json_encode($available_votes_data);
	// response output
	die($response_data);	
} // END DETERMINE USER AVAILABLE VOTES

add_action('wp_ajax_determine_user_available_votes', 'determine_user_available_votes');
// non-logged in user
add_action('wp_ajax_nopriv_determine_user_available_votes', 'determine_user_available_votes' );

function fetch_user_ideas_topics_count() {
// 1. CHECK FOR ACTUAL USER AND GET USER_ID
	$user_id = get_current_user_id();
	if (empty($user_id)) {
		$num_ideas_topic_data = array (
			'num_ideas_topic_data' => "NULL",
			);
		}
// 2. ELSE IS A USER
	else {
// 3. GET NUM IDEAS & NUM TOPICS
			$num_ideas = count_user_posts($user_id);
			$num_topics = count_user_topics($user_id);
	$num_ideas_topic_data = array (
		'num_ideas' => $num_ideas,
		'num_topics' => $num_topics
		);	
	}
// 4. START RETURN DATA	
	// CONVERT  TO JSON	
	$response_data = json_encode($num_ideas_topic_data);
	// response output
	die($response_data);	
} // END GET USER IDEA & TOPIC COUNT

add_action('wp_ajax_fetch_user_ideas_topics_count', 'fetch_user_ideas_topics_count');
// non-logged in user
add_action('wp_ajax_nopriv_fetch_user_ideas_topics_count', 'fetch_user_ideas_topics_count' );


/*
 // PROCESS USER VOTE
*/
function process_user_vote () {
	// make sure the user has votes
	// AND that it's a user!! 
	// if not a user we present the "please login to vote message"
	$user_id = get_current_user_id();
	// NOT LOGGED IN
	// SHOULD THIS MOVE TO ITS OWN FUNCTION/SPECIAL-CASE ? (it's duplicate code)
	if (empty($user_id)) {
		$no_vote_message = 'Please <a href="'.get_bloginfo( 'wpurl' ).'/wp-login.php">Login</a> or <a href="'.get_bloginfo( 'wpurl' ).'/wp-login.php?action=register" class="reg-link">Register</a> to Vote.';
		$vote_response_data = array(
				"vote_response_data" => "no-vote",
				"vote_message" => $no_vote_message
			);
		}  // END NOT LOGGED IN
	else {
		$avail_votes = get_available_votes($user_id);	
		// IF HAS VOTES, PROCESS VOTE
		 if ($avail_votes > 0) {
			$vote_response = record_idea_vote();	
			$vote_response_data = array (
				"vote_response_data" => $vote_response
				);
			}
		// NO VOTES AVAILABLE
		else {
			$no_vote_message = "Sorry, you don't have any more votes available. Nomicly awards more votes once per hour. You can also earn more votes by contributing your own new ideas.";
			$vote_response_data = array (
				"vote_response_data" => "no-vote",
				"vote_message" => $no_vote_message
				);
			} // END NO VOTES AVAILABLE
		}// END LOGGED IN USER
		// CONVERT  TO JSON	
	$response_data = json_encode($vote_response_data);
	die($response_data);	
} // end process_user_vote

add_action('wp_ajax_process_user_vote', 'process_user_vote');
// non-logged in user
add_action('wp_ajax_nopriv_process_user_vote', 'process_user_vote' );

function process_hot_not_vote() {
	$temp_pair_stats = nomicly_record_vote();
	$pair_id = $temp_pair_stats['pair_id'];
	$pair_stats = get_hot_not_stats($pair_id);
	// SEND BACK THE IDEAS SO WE CAN PROPERLY MATCH STATS ON FRONTEND
	$pair_stats['idea_1'] = $temp_pair_stats['idea_1'];
	$pair_stats['idea_2'] = $temp_pair_stats['idea_2'];
	// ADD GET NEXT IDEAS BUTTON TO RESPONSE
	$pair_stats['get_next_ideas'] = '<a href="" id="get_next_ideas" name="get_next_ideas">Get Next Ideas</a>';
// CONVERT ARRAY TO JSON
	$pair_stats = json_encode($pair_stats);	
// response output
	die($pair_stats);
 }// END PROCESS HOT NOT VOTE

// this handles the callback for hot/not votes
// logged in user
add_action('wp_ajax_process_hot_not_vote', 'process_hot_not_vote');
// non-logged in user
add_action('wp_ajax_nopriv_process_hot_not_vote', 'process_hot_not_vote' );

/*
// GET NEXT IDEAS
// FOR HOT OR NOT
// POST VOTE
*/
function get_next_ideas() {
// using get_posts 
// returns an array of data
// then create an easier to work with array
// json-ify the new array
// then return new JSON as the response
	$post_args = array (
	'posts_per_page' => 2,
	'orderby' => 'rand'
	);
	$new_posts = get_posts( $post_args);
	$response_data = array (
		"idea_1_data" => $new_posts[0],
		"idea_2_data" => $new_posts[1],
		);	
	// CONVERT  TO JSON
	$response_data = json_encode($response_data);
	// response output
	die($response_data);
				
}// END GET NEW IDEAS

// AJAX callback for getting new ideas after submitting a vote (and reviewing stats)
// logged in user
add_action('wp_ajax_get_next_ideas', 'get_next_ideas');
// non-logged in user
add_action('wp_ajax_nopriv_get_next_ideas', 'get_next_ideas' );


/*
// buggy ajax shit that just didn't work
// suppose to make it so the ajax processing url doesn't need to be hardcoded
*/
//add_action( 'wp_head', array( &$this, 'add_ajax_library' ) );
//function add_ajax_library() {
//    $html = '<script type="text/javascript">';
//        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
//    $html .= '</script>';
//    echo $html;
//} // end add_ajax_library

/*
 // END AJAX section
*/



/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Tell WordPress to run twentyeleven_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'twentyeleven_setup' );

if ( ! function_exists( 'twentyeleven_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyeleven_setup() in a child theme, add your own twentyeleven_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, custom headers
 * 	and backgrounds, and post formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_setup() {

	/* Make Twenty Eleven available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Eleven, use a find and replace
	 * to change 'twentyeleven' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentyeleven', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );

	// Grab Twenty Eleven's Ephemera widget.
	require( get_template_directory() . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twentyeleven' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	$theme_options = twentyeleven_get_theme_options();
	if ( 'dark' == $theme_options['color_scheme'] )
		$default_background_color = '1d1d1d';
	else
		$default_background_color = 'f1f1f1';

	// Add support for custom backgrounds.
	add_theme_support( 'custom-background', array(
		// Let WordPress know what our default background color is.
		// This is dependent on our current color scheme.
		'default-color' => $default_background_color,
	) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// Add support for custom headers.
	$custom_header_support = array(
		// The default header text color.
		'default-text-color' => '000',
		// The height and width of our custom header.
		'width' => apply_filters( 'twentyeleven_header_image_width', 1000 ),
		'height' => apply_filters( 'twentyeleven_header_image_height', 288 ),
		// Support flexible heights.
		'flex-height' => true,
		// Random image rotation by default.
		'random-default' => true,
		// Callback for styling the header.
		'wp-head-callback' => 'twentyeleven_header_style',
		// Callback for styling the header preview in the admin.
		'admin-head-callback' => 'twentyeleven_admin_header_style',
		// Callback used to display the header preview in the admin.
		'admin-preview-callback' => 'twentyeleven_admin_header_image',
	);
	
	add_theme_support( 'custom-header', $custom_header_support );

	if ( ! function_exists( 'get_custom_header' ) ) {
		// This is all for compatibility with versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR', $custom_header_support['default-text-color'] );
		define( 'HEADER_IMAGE', '' );
		define( 'HEADER_IMAGE_WIDTH', $custom_header_support['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $custom_header_support['height'] );
		add_custom_image_header( $custom_header_support['wp-head-callback'], $custom_header_support['admin-head-callback'], $custom_header_support['admin-preview-callback'] );
		add_custom_background();
	}

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( $custom_header_support['width'], $custom_header_support['height'], true );

	// Add Twenty Eleven's custom image sizes.
	// Used for large feature (header) images.
	add_image_size( 'large-feature', $custom_header_support['width'], $custom_header_support['height'], true );
	// Used for featured posts if a large-feature doesn't exist.
	add_image_size( 'small-feature', 500, 300 );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'wheel' => array(
			'url' => '%s/images/headers/wheel.jpg',
			'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Wheel', 'twentyeleven' )
		),
		'shore' => array(
			'url' => '%s/images/headers/shore.jpg',
			'thumbnail_url' => '%s/images/headers/shore-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Shore', 'twentyeleven' )
		),
		'trolley' => array(
			'url' => '%s/images/headers/trolley.jpg',
			'thumbnail_url' => '%s/images/headers/trolley-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Trolley', 'twentyeleven' )
		),
		'pine-cone' => array(
			'url' => '%s/images/headers/pine-cone.jpg',
			'thumbnail_url' => '%s/images/headers/pine-cone-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Pine Cone', 'twentyeleven' )
		),
		'chessboard' => array(
			'url' => '%s/images/headers/chessboard.jpg',
			'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Chessboard', 'twentyeleven' )
		),
		'lanterns' => array(
			'url' => '%s/images/headers/lanterns.jpg',
			'thumbnail_url' => '%s/images/headers/lanterns-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Lanterns', 'twentyeleven' )
		),
		'willow' => array(
			'url' => '%s/images/headers/willow.jpg',
			'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Willow', 'twentyeleven' )
		),
		'hanoi' => array(
			'url' => '%s/images/headers/hanoi.jpg',
			'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Hanoi Plant', 'twentyeleven' )
		)
	) );
}
endif; // twentyeleven_setup

if ( ! function_exists( 'twentyeleven_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_header_style() {
	$text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail.
	if ( $text_color == HEADER_TEXTCOLOR )
		return;
		
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $text_color ) :
	?>
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo $text_color; ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // twentyeleven_header_style

if ( ! function_exists( 'twentyeleven_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in twentyeleven_setup().
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 32px;
		line-height: 36px;
		text-decoration: none;
	}
	#desc {
		font-size: 14px;
		line-height: 23px;
		padding: 0 0 3em;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 1000px;
		height: auto;
		width: 100%;
	}
	</style>
<?php
}
endif; // twentyeleven_admin_header_style

if ( ! function_exists( 'twentyeleven_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in twentyeleven_setup().
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_admin_header_image() { ?>
	<div id="headimg">
		<?php
		$color = get_header_textcolor();
		$image = get_header_image();
		if ( $color && $color != 'blank' )
			$style = ' style="color:#' . $color . '"';
		else
			$style = ' style="display:none"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // twentyeleven_admin_header_image

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function twentyeleven_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function twentyeleven_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function twentyeleven_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyeleven_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyeleven_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function twentyeleven_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyeleven_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyeleven_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function twentyeleven_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyeleven_page_menu_args' );

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_widgets_init() {

	register_widget( 'Twenty_Eleven_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Showcase Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-2',
		'description' => __( 'The sidebar for the optional Showcase Template', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'twentyeleven' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'twentyeleven' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'twentyeleven' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentyeleven_widgets_init' );

if ( ! function_exists( 'twentyeleven_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function twentyeleven_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}
endif; // twentyeleven_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty Eleven 1.0
 * @return string|bool URL or false when no link is present.
 */
function twentyeleven_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function twentyeleven_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'twentyeleven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyeleven_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'twentyeleven' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'twentyeleven' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twentyeleven_comment()

if ( ! function_exists( 'twentyeleven_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own twentyeleven_posted_on to override in a child theme
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_body_classes( $classes ) {

	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'twentyeleven_body_classes' );