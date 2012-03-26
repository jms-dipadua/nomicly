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
	include "/functions/functions.inc.php";
?>
<?php
 if(isset($_POST['create_idea'])) {
	$idea_array = $_POST;
	// DE-BUGGING STUFF ONLY
	 print_r ($_POST);
	 echo "<br />";
	 print_r ($idea_array); 

	$idea_title = $_POST['idea_title'];
	$idea_description = $_POST['idea_description'];
	create_idea();
	}
?>
</head>
<body>

<h1>Create An Idea</h1>
<div class="main">
<form method="post" action="#">
	<input type="text" name="idea_title" value="" />
	<br />
	<textarea name="idea_description" value="">   </textarea> 
	<br />
	<input type="submit" name="create_idea" value="Create Idea!" />
</form>
</div>
<div class="existing_ideas">


</div>
</body>
<?php
 mysql_close();
 ?>
</html>