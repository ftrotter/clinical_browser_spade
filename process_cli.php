<?php

require_once('process_history.php');

if(isset($argv[1])){
	$target_path = $argv[1];
}else{
	echo "need a file...\n";
	exit();
}

$big_array = process_history_file($target_path);

$big_threads = array();
foreach($big_array as $inside_array){//lets merge the threads and the url and make a flat file!!

	$threads = $inside_array['threads'];
	$urls = $inside_array['urls'];
	$keywords = $inside_array['keywords'];
	foreach($threads as $thread_id => $this_thread){
		$new_thread = array();//its late and I forget how to filter numeric vs alpha keys...
		foreach($this_thread as $key => $value){
			if(!is_numeric($key)){
				$new_thread[$key] = $value;
			}
		}	
		$my_url = $urls[$this_thread['url']];
		foreach($my_url as $key => $value){
			if(!is_numeric($key)){
				$new_thread[$key] = $value;
			}
		}
		if(isset($keywords[$this_thread['url']])){
			$my_keywords = $keywords[$this_thread['url']];	
			foreach($my_keywords as $key => $value){
				if(!is_numeric($key)){
					$new_thread[$key] = $value;
				}
			}	
		}
		$big_threads[$thread_id] = $new_thread;

	}
}
$outstream = fopen("php://output", "a");

toCSV($big_threads,$outstream);

function toCSV($data, $outstream) {
    if (count($data)) {
        // get header from keys
	list($the_end) = array_slice($data,-1);	
        fputcsv($outstream, array_keys($the_end));
        // 
        foreach ($data as $row) {
            fputcsv($outstream, $row);
        }
    }
}

