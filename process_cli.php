<?php

require_once('process_history.php');

if(isset($argv[1])){
	$target_path = $argv[1];
}else{
	echo "need a file...\n";
	exit();
}

$big_array = process_history_file($target_path);

echo "<pre>";
var_export($big_array);
echo "</pre>";


