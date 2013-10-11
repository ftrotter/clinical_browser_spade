<?php
require_once('process_history.php');
require_once('config.php');

session_start();

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

require_once('header.php');

echo '
<ul class="list-group">
  <li class="list-group-item">Cras justo odio</li>
  <li class="list-group-item">Dapibus ac facilisis in</li>
  <li class="list-group-item">Morbi leo risus</li>
  <li class="list-group-item">Porta ac consectetur ac</li>
  <li class="list-group-item">Vestibulum at eros</li>
</ul>
';

require_once('footer.php');
/*
$SESSION['big_array'] = $big_array;
$for_json = array();
$my_id = 1;
foreach($big_array as $inside_array){
	$my_id++;
	$threads = $inside_array['threads'];
	$urls = $inside_array['urls'];

	$children = recurse_for_json($urls,$threads);
	$tmp_obj = object();
	$tmp_obj->id = $my_id;
	$tmp_obj->label = $my_id;
	$tmp_obj->checkbox = true;
	$tmp_obj->radio = false;
	$tmp_obj->isFolder = true;
	$tmp_obj->childs = $children;
	$for_json[$my_id] = $tmp_obj;

}

$json = json_encode($for_json);
echo "$json";

unlink($target_path);

function recurse_for_json($all_urls, $threads){
	
	$return_array = array();
	foreach($threads as $id => $this_thread){
		
		$tmp_obj = object();
		$tmp_obj->id = $id;
		$tmp_obj->label = $all_urls[$this_thread['url']];
		$tmp_obj->checkbox = true;
		$tmp_obj->radio = false;
		$return_array[] = $tmp_obj;
	}
	return($return_array);
}
*/
