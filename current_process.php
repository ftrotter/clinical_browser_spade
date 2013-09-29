<?php
	$br = "\n";

	$file = "History.sqlite";

//	$dbhandle = sqlite_open($file,0666,$error);
//	if(!$dbhandle) die ("could not load the file $br $error $br");

	/*
Gets the search terms and the URLs from chrome
SELECT * FROM keyword_search_terms
JOIN urls on keyword_search_terms.url_id = urls.id

	*/

	//first lets get all of the URLs that are from Wikipedia!!!
try {
    /*** connect to SQLite database ***/
    $dbh = new PDO("sqlite:$file");
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }


//this generally matches the wikipedia url... we use this to filter first...
  $basic_re1='.*?';	# Non-greedy match on filler
  $basic_re2='(en\\.wikipedia\\.org)';	# Fully Qualified Domain Name 1

//this matches against the actual query string on the url...
  $txt='http://en.wikipedia.org/wiki/Diabetes_mellitus';

  $re1='.*?';	# Non-greedy match on filler
  $re2='(?:[a-z][a-z0-9_]*)';	# Uninteresting: var
  $re3='.*?';	# Non-greedy match on filler
  $re4='(?:[a-z][a-z0-9_]*)';	# Uninteresting: var
  $re5='.*?';	# Non-greedy match on filler
  $re6='(?:[a-z][a-z0-9_]*)';	# Uninteresting: var
  $re7='.*?';	# Non-greedy match on filler
  $re8='(?:[a-z][a-z0-9_]*)';	# Uninteresting: var
  $re9='.*?';	# Non-greedy match on filler
  $re10='(?:[a-z][a-z0-9_]*)';	# Uninteresting: var
  $re11='.*?';	# Non-greedy match on filler
  $re12='((?:[a-z][a-z0-9_]*))';	# Variable Name 1



$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_USERAGENT, 'PicAxe/1.0 (http://www.fredtrotter.com/; fred.trotter@gmail.com)');






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

//url = 'http://en.wikipedia.org/w/api.php?action=query&titles=Your_Highness&prop=revisions&rvprop=content&rvsection=0';

	//using the wiki_title we create an api call to wikipedia..
	$api_url = "http://en.wikipedia.org/w/api.php?format=json&action=query&titles=$wiki_title&prop=revisions&rvprop=content&rvsection=0";
	curl_setopt($ch, CURLOPT_URL, $api_url);
	$result = curl_exec($ch);
	if (!$result) {
  		exit('cURL Error: '.curl_error($ch));
	}
	$result = json_decode($result,true);
	var_export($result);
	
   }

  }


}





$dbh = null;

