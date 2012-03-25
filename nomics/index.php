<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />
<meta description="Idea Factory is devoted to bringing the best ideas to the forefront for discussion and evaluation." />
<title>The Idea Factory, Where Not All Ideas Are Good.</title>

<?php
 include "functions/functions.inc.php"
?>
<?php
 if ($_POST['create_ideas']) {
 $idea_varriables = $_POST[''];
 
 create_idea ($idea_varriables);
 
 }
?>

</head>
<body>
<h1>Create An Idea</h1>
<div class="main">
<form method="POST" action="#">
	<input type="text" name="idea_title" value="" /><br />
	<textarea name="idea_description" value="">   </textarea> <br />
	<input type="submit" name="create_idea" value="Create Idea!" />
</form>
</div>
<div class="existing_ideas">


</div>
</body>

</html>