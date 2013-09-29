<?php

require_once('process_history.php');

var_export($_FILES);

$target_path = "tmp/";

$random_string = uniqid();

$target_path = 	$target_path . 
		basename( $_FILES['inputFile']['name']) .
		"_$random_string"; 

if(move_uploaded_file($_FILES['inputFile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['inputFile']['name']). 
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}

$big_array = process_history_file($target_path);

echo "<pre>";
var_export($big_array);
echo "</pre>";

unlink($target_path);


