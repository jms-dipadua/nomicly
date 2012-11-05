<?php
if (isset($_POST)) {
	$vote = $_POST['answer'];
	$vote = explode($vote);

	$idea_id = $vote[1];
	$opinion = $vote[0]; // where 1 = agree & 0 = disagree
	
	$user_vote = "INSERT into votes (idea_id, type, status) VALUES ('$idea_id','$opinion', '1')";
	
	$user_vote_query = mysql_query($user_vote);
	if (!$user_vote_query) {
		echo mysql_error();
		$message = "Failed to insert:  $insert";
		log_errors($message);
		}
}
?>