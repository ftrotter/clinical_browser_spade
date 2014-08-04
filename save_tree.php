<?php
	require_once('config.php');
/*
//The SQL for the logging table...

CREATE TABLE IF NOT EXISTS `json_trees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree_json` mediumtext NOT NULL,
  `user_token` varchar(300) NOT NULL,
  `upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



*/
	

	if(isset($_POST['url_tree'])){

		$url_tree = $_POST['url_tree'];

		if(!isJson($url_tree){
			echo json_encode(array('result' => 'error', 'problem' => 'url_tree is not vaild json');
			exit();

		}


		$url_tree_obj = json_decode($url_tree);
		$url_tree_json_dump = var_export($url_tree_obj,true);
		$url_tree_dump = var_export($url_tree,true);

		$url_string = "
url_tree: $url_tree
url_tree_dump: $url_tree_dump
url_tree_json_dump: $url_tree_json_dump
";

		$url_string = mysql_real_escape_string($url_string); //use this for testing...
		
		$url_string = mysql_real_escape_string($url_tree);

		$user_token = mysql_real_escape_string($_POST['user_token']);
		
	$sql = "
INSERT INTO  `browser_spade`.`json_trees` (
`id` ,
`tree_json`,
`user_token`,
`upload_time`
)
VALUES (
NULL ,  
'$url_string', '$user_token',
NULL
);
";


		mysql_query($sql) or die("could not save sql $sql\n".mysql_error());

		if(isset($_POST['echo'])){
			if($_POST['echo']){			
						
				$url_tree_obj->result = 'saved';
				echo json_encode($url_tree_obj);				

			}else{
				echo json_encode(array('result' => 'saved'));
			}
		}else{
			echo json_encode(array('result' => 'saved'));
		}
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
Echo back JSON in reply? <input type='checkbox' name='echo' value='true'>
<br>
<input type='submit'>
</form>


";



	}


//thank you stack overflow...
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}






?>
