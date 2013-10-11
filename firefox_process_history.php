<?php


function firefox_process_history_file($filename){

        //first lets get all of the URLs that are from Wikipedia!!!
try {
    /*** connect to SQLite database ***/
    $dbh = new PDO("sqlite:$filename");
    }
catch(PDOException $e)
    {
    	echo $e->getMessage();
	echo "Could not open the file for some reason... damn.";
	unlink($target_path);
	exit();
    }


//this generally matches the wikipedia url... we use this to filter first...
  $basic_re1='.*?';     # Non-greedy match on filler
  $basic_re2='(en\\.wikipedia\\.org)';  # Fully Qualified Domain Name 1

//this matches against the actual query string on the url...
  $txt='http://en.wikipedia.org/wiki/Diabetes_mellitus';

  $re1='.*?';   # Non-greedy match on filler
  $re2='(?:[a-z][a-z0-9_]*)';   # Uninteresting: var
  $re3='.*?';   # Non-greedy match on filler
  $re4='(?:[a-z][a-z0-9_]*)';   # Uninteresting: var
  $re5='.*?';   # Non-greedy match on filler
  $re6='(?:[a-z][a-z0-9_]*)';   # Uninteresting: var
  $re7='.*?';   # Non-greedy match on filler
  $re8='(?:[a-z][a-z0-9_]*)';   # Uninteresting: var
  $re9='.*?';   # Non-greedy match on filler
  $re10='(?:[a-z][a-z0-9_]*)';  # Uninteresting: var
  $re11='.*?';  # Non-greedy match on filler
  $re12='((?:[a-z][a-z0-9_]*))';        # Variable Name 1


 $sql = "SELECT * FROM moz_places";

$the_stuff_we_keep = array();

foreach ($dbh->query($sql) as $row){


  $url = $row['url'];
  if ($c=preg_match_all ("/".$basic_re1.$basic_re2."/is", $url, $matches))
  {
        $url_id = $row['id'];
        //this matches en.wikipedia.org
        //echo "$url matches $url_id \n";

  if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7.$re8.$re9.$re10.$re11.$re12."/is", $url, $matches))
  {
      $wiki_title=$matches[1][0];
     // print "isolated title: ($wiki_title) \n";

	$is_clinical_array = json_decode(
			file_get_contents(
				"http://spade.ft1.us/is_title_clinical.php?title=$wiki_title"),
			true);

	//var_export($is_clinical_array);


	$is_clinical = $is_clinical_array['is_clinical'];	

	if($is_clinical){
		//note: there is no equivlent to the "keywords" concept in the firefox history structure... 
		$threads = firefox_get_threads($dbh,$row);
		$urls = firefox_get_urls_from_threads($dbh,$threads);
		//this is what we put in our bug array!!
		$the_stuff_we_keep[$row['id']] = array(
			'threads' => $threads,
			'urls' => $urls,
			);
	}else{
	//	echo "$wiki_title not clinical... ignoring..\n";
	}
	
   } // is_clinical is sorted for this url...

  }else{
	//this is not a wikipedia url...
  }

}//url loop done...
	return($the_stuff_we_keep);
} //end process function...

function firefox_get_urls_from_threads($dbh,$threads){

	$return_urls = array();
	foreach($threads as $a_thread){
		$place_id = mysql_real_escape_string($a_thread['place_id']);
	
		$sql = "SELECT * FROM moz_places WHERE moz_places.id = $place_id";
		foreach($dbh->query($sql) as $url_row){ //there should be only one...
			$return_urls[$url_row['id']] = $url_row;
		}
	}

	return($return_urls);

}


function firefox_get_threads($dbh, $url_row){

	$id = $url_row['id'];
	$sql = "SELECT * FROM moz_historyvisits 
WHERE moz_historyvisits.place_id = $id";
	
	$all_starting_threads = array(); //the starting visit for every thread with this url..
	foreach($dbh->query($sql) as $visit_row){

		$from_visit_id = $visit_row['from_visit'];
		if($from_visit_id != 0){
			$starting_visit = firefox_climb_thread_up($dbh,$from_visit_id);
		}else{
			$starting_visit = $visit_row;
		}
		
		$all_starting_threads[] = $starting_visit;
	}

	$all_relevant_visits = array();
	foreach($all_starting_threads as $a_starting_visit){
		$all_relevant_visits[$a_starting_visit['id']] = $a_starting_visit;
		$fan_results = firefox_fan_thread_down($dbh,$a_starting_visit['id']);
		if(count($fan_results) > 0){
			$all_relevant_visits = array_replace_recursive(
					$all_relevant_visits,
					$fan_results);
		}
		
	}	


	return($all_relevant_visits);

}

function firefox_fan_thread_down($dbh,$me){
        $me = mysql_real_escape_string($me);
        $sql = "SELECT * FROM moz_historyvisits WHERE moz_historyvisits.from_visit = $me";
	$return_array = array();
        foreach($dbh->query($sql) as $visit_row){ //there should be only one...
                $id = $visit_row['id'];
		// echo "+ $id ";
		$return_array[$id] = $visit_row;
		$fan_results = firefox_fan_thread_down($dbh,$id);
		if(count($fan_results) > 0){
                	$return_array = array_replace_recursive(
					$return_array,
					$fan_results);
		}
        }

	return($return_array);
}




function firefox_climb_thread_up($dbh,$me){
	$me = mysql_real_escape_string($me);
	$sql = "SELECT * FROM moz_historyvisits WHERE moz_historyvisits.id = $me";
	foreach($dbh->query($sql) as $visit_row){ //there should be only one...
		$from_visit = $visit_row['from_visit'];
		if($from_visit != 0){
		//	echo "rec->$from_visit   ";
			return(firefox_climb_thread_up($dbh,$from_visit));
		}else{
		//	echo "<- done \n";
			return($visit_row);
		}
	}
}

