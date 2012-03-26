<?php
session_start();
$_SESSION['current_user'];
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />
<meta description="Idea Factory is devoted to bringing the best ideas to the forefront for discussion and evaluation." />
<title>The Idea Factory, Create, Find and Share Good Ideas.</title>
<?php
	require_once "functions/functions.inc.php";
 	require_once "functions/database.inc.php";
?>
<?php
 if(isset($_POST['create_ideas'])) {
 	$idea_array = $_POST;
	/* 
	// DE-BUGGING STUFF ONLY
	 print_r ($_POST);
	 echo "<br />";
	 print_r ($idea_array); 
	*/

	$idea_title = $_POST['idea_title'];
	$idea_description = $_POST['idea_description'];
	$creation_message = create_idea($idea_array);
	}
?>
</head>
<body>
 <div class="response_message">
<?php
	echo $creation_message; 
?>
 </div>
<h1>Create An Idea</h1>
<div class="main">
<form method="post" action="#">
	<input type="text" name="idea_title" value="" />
	<br />
	<textarea name="idea_description" value="">   </textarea> 
	<br />
	<input type="submit" name="create_ideas" value="Create Idea!" />
</form>
</div>
<div class="existing_ideas">
<h2>Recent Ideas &mdash; What do you think of these recent ideas?</h2>
<?php
	$recent_ideas = get_recent_ideas();

?>

</div>
</body>
<?php
 mysql_close();
 ?>
</html>