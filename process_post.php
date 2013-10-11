<?php
require_once('chrome_process_history.php');
require_once('firefox_process_history.php');
require_once('config.php');

session_start();

$target_path = "tmp/";

$random_string = uniqid();

$target_path = 	$target_path . 
		basename( $_FILES['inputFile']['name']) .
		"_$random_string"; 

if(move_uploaded_file($_FILES['inputFile']['tmp_name'], $target_path)) {
//    echo "The file ".  basename( $_FILES['inputFile']['name']). 
 //   " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
	exit();
}

try {
    /*** connect to SQLite database ***/
    $dbh = new PDO("sqlite:$target_path");
    }
catch(PDOException $e)
    {
        echo $e->getMessage();
        echo "Could not open the file for some reason... damn.";
        unlink($target_path);
        exit();
    }



$sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='urls'";
$is_chrome = false;

foreach($dbh->query($sql) as $row){
        $is_chrome = true;
}



//this gets all of the urls...
if($is_chrome){

$big_array = chrome_process_history_file($target_path);
$_SESSION['big_array'] = $big_array;
require_once('header.php');
require_once('chrome_filter.php');

}else{ //well this is firefox... lets 

$big_array = firefox_process_history_file($target_path);
$_SESSION['big_array'] = $big_array;
require_once('header.php');
require_once('firefox_filter.php');

}


require_once('footer.php');

function get_checkbox($id,$label,$value){

	$return_me = "
  <div class='checkbox'>
    <label>
      <input type='hidden' name='url_$id"."["."$value]' value='0'> 
      <input type='checkbox' name='url_$id"."["."$value]' value='$value' checked> $label
    </label>
  </div>
";

	return($return_me);

}

