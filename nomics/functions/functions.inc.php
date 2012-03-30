<?php 
/* ERROR FUNCTIONS */
function log_errors ($message) {
//ERROR LOGS DO'NT SEEM TO BE WORKING FOR SELECTS...(get_recent_ideas for example)
    $error_message = $message;
    $log = "INSERT INTO error_logs (message) VALUES ('$error_message')";
    $log_query = mysql_query ($log);
    
    if (!$log_query) {
        echo mysql_error();
    }
}


/*IDEA FUNCTIONS */
function create_idea($idea_array, $parent) {
	global $idea_title;
		$idea_title = $idea_array[idea_title];
		$idea_title = mysql_real_escape_string($idea_title);
	global $idea_description;
		$idea_description=$idea_array[idea_description];
		$idea_description = mysql_real_escape_string($idea_description);
	    //we have to determine what the parent idea of the created idea is.
	    //all ideas (right now) originate from the original idea: create an idea
	    //but we're not passing anything in through $parent, so we'll set it to 1 (the original idea)
	global $parent_idea;
	    if(strlen($parent)<=0) {
	    $parent_idea = 1;
	    }
	    else {
	    $parent_idea = $parent;
		}
		
	//MAY need to further genralize the insert_queries such that there is only one insert but a type of insert that is passed along and used to determine the table to insert into. But that may be a bit overkill on the modularization. It does setup better for ObjOrient since "ideas" can only insert into the ideas table, etc. Needs more thought...

	$insert = generate_insert_ideas_query($idea_title, $idea_description, $parent_idea);
	$insert_query = mysql_query($insert);

		if(!$insert_query) {
			echo mysql_error();
			$message = "Failed to insert:  $insert";
			log_errors($message);
			}
		else { 
		//DEVELOPMENT NOTE
		//we'll want to return some indication to the user that the post worked. probably a display of the idea itself.
		//this is also where we'll trigger the "share this idea" on twitter/FB
			$message = "Idea Successfully Saved!";
		//	$idea_URl = generate_idea_url($idea);
		}
		return $message;
}

/* DATABASE QUERIES */
//INSERT IDEAS
function generate_insert_ideas_query($title, $description, $parent_idea) {
	$current_idea_title = $title;
		$current_idea_title = strip_tags($current_idea_title);
	$current_idea_description = $description;
		$current_idea_description = strip_tags($current_idea_description);
//	$ref_code = generate_ref_code($idea_some_var);
    $current_idea_parent = $parent_idea;
	
	$insert_query = "INSERT INTO ideas (title, details, parent_idea_ids) VALUES (";
	$insert_query .= "'$current_idea_title', '$current_idea_description', '$parent_idea')";
	return $insert_query;
}

// Recent IDEAS
function get_recent_ideas() {
    $tomorrow = generate_tomorrow_date();
    $yesterday = generate_yesterday_date();

    $select_recent_ideas = "SELECT * from ideas WHERE created_at BETWEEN '$yesterday' AND '$tomorrow' ORDER BY idea_id DESC LIMIT 10";
    $recent_ideas_query = mysql_query($select_recent_ideas);
        if (!$recent_ideas_query){
        	echo mysql_error();
			$message = "Failed to query:  $recent_ideas";
			log_errors($message);
            }
    while ($query_results= mysql_fetch_array($recent_ideas_query, MYSQL_ASSOC)) {
//get all the deets from the query (title, details, id's, etc)
	$recent_idea_title = $query_results['title'];
	$recent_idea_details = $query_results['details'];
	$recent_idea_id = $query_results['idea_id'];
//setup the feed of ideas (title, details, vote capablities)
	$recent_ideas .="<h3>$recent_idea_title</h3>"."<p>$recent_idea_details</p>";
	$recent_ideas .="<p><a href='#' id='$recent_idea_id' onClick='vote()';>Agree</a> &nbsp;<a href='#' id='$recent_idea_id' onClick='vote()'>Disagree</a></p>";
	}
    return $recent_ideas;
}
/*URL Functions */

function generate_idea_url ($idea) {
// THIS IS LIKE BIT.LY. We create a short URL (based off the idea's ref-code)
// This means we need to create a page/script that can listen for ../{ref-code} and know how to present the page. Maybe it's built into the index page???

return ($url);
}

/*REF CODE FUNCTIONS */

function generate_ref_code ($some_id) {
	$element_to_create_ref = $some_element;

	// will want to use a full time stamp + the "element" and then md5() or sha()
	//need to figure out how shortten to just 8 elements AND know this won't cause collisions
		
	$ref_code = generate_date_time();
	$ref_code .= " ".$element_to_create_ref;
	$ref_code = md5($ref_code);
	return $ref_code;
}


/*TIME FUNCTIONS (DATE, TIME, ETC) */

function generate_date() {
	$date = date('Y-m-d');
	return $date;
}

function generate_time() {
	$time = date('H:i:s');
	return $time;
}

function generate_yesterday_date() {
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    return $yesterday;
}

function generate_tomorrow_date() {
    $tomorrow = date("Y-m-d", strtotime("+1 day"));
    return $tomorrow;
}

?>