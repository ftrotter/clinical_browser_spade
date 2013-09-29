<?php


function process_history_file($filename){

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

//this gets all of the urls...
  $sql = "SELECT * FROM urls";


foreach ($dbh->query($sql) as $row){


  $url = $row['url'];
  if ($c=preg_match_all ("/".$basic_re1.$basic_re2."/is", $url, $matches))
  {
        $url_id = $row['id'];
        //this matches en.wikipedia.org
        echo "$url matches $url_id \n";

  if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7.$re8.$re9.$re10.$re11.$re12."/is", $url, $matches))
  {
      $wiki_title=$matches[1][0];
      print "isolated title: ($wiki_title) \n";

	$is_clinical_array = json_decode(
			file_get_contents(
				"http://spade.ft1.us/is_title_clinical.php?title=$wiki_title"),
			true);

	var_export($is_clinical_array);


	$is_clinical = $is_clinical_array['is_clinical'];	

	if($is_clinical){
		echo "$wiki_title is clinical, initiating deeper process...\n";
	}else{
		echo "$wiki_title not clinical... ignoring..\n";
	}
	
   } // is_clinical is sorted for this url...

  }else{
	//this is not a wikipedia url...
	

  }


}//url loop done...

} //end process function...


function get_threads($url_row){

	$sql = "SELECT * FROM visits 
WHERE visits.url = ";
	

}



