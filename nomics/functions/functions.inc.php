<?php 
function log_errors ($message) {
    $error_message = $message;
    $log = "INSERT INTO error_logs (message) VALUES ('$message')";
    $log_query = mysql_query ($log);
    
    if (!$log_query) {
        echo mysql_error();
    }
}

function create_idea ($idea) {
	$idea_current = $idea;
	$insert = generate_insert($idea_current);
	
	$insert_query = mysql_query($insert);
		if(!$insert_query) {
			echo mysql_error();
			$message = "Failed to insert:  $insert_query";
			log_errors($message);
			}
		else { 
		/*
		//DEVELOPMENT NOTE
		//we'll want to return some indication to the user that the post worked. probably a display of the idea itself.
		//this is also where we'll trigger the "share this idea" on twitter/FB
		*/
		
		$idea_URl = generate_idea_url($idea);
		
		}
}

function generate_insert_query($idea) {
	$idea_current = $idea;
	
	/*do some parsing shit to the array */
	$ref_code = generate_ref_code($idea_some_var);
	return $insert_query;
}

function generate_idea_url ($idea) {
/*
// THIS IS LIKE BIT.LY. We create a short URL (based off the idea's ref-code)
// This means we need to create a page/script that can listen for ../{ref-code} and know how to present the page. Maybe it's built into the index page???
*/
	
	return ($url);

}

function generate_ref_code ($some_id) {
	$element_to_create_ref = $some_element;

	/* 
	// will want to use a full time stamp + the "element" and then md5() or sha()
	//need to figure out how shortten to just 8 elements AND know this won't cause collisions
	*/
	
	$ref_code = get_date_time();
	$ref_code .= " ".$element_to_create_ref;
	$ref_code = sha($ref_code);
	return $ref_code;
}

function get_date_time() {
	$date_time = date('Y'-'m'-'d');
	$date_time .= " ".date('H':'i':'s');
	return $date_time;
}


?>