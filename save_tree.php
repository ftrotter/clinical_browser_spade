<?php
	require_once('config.php');

	if(isset($_POST['url_tree'])){

		$url_tree = mysql_real_escape_string($_POST['url_tree']);
		$user_token = mysql_real_escape_string($_POST['user_token']);
		
	$sql = "
INSERT INTO  `browser_spade`.`json_trees` (
`id` ,
`tree_json`,
`user_token`
)
VALUES (
NULL ,  '$url_tree', '$user_token'
);
";


		mysql_query($sql) or die("could not save sql $sql\n".mysql_error());

		echo json_encode(array('result' => 'saved'));

	}else{

		echo "
<html><head><title>treepost test form</title></head><body>

<h1> Tree Post test form </h1>
<form method='POST' action='save_tree.php'>
url_tree:<br>
<textarea id='url_tree' name='url_tree' rows='10' cols='50'></textarea>
<br>
user_token:<br>
<input name='user_token' type='text'><br>

<input type='submit'>
</form>


";



	}





?>
