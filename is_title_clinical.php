<?php
	require_once('config.php');
	require_once('clinical_detect_function.php');
	
	if(isset($_GET['title'])){
		$title = mysql_real_escape_string($_GET['title']);
	}else{
		echo "no title. fail.";
		exit();
	}

	//first we check to see if we have a cache...

	$search_sql = "SELECT *
FROM `is_clinical_url`
WHERE `wiki_title` LIKE '$title'";

	$result = mysql_query($search_sql) or die("Problem doing $search_sql <br>".mysql_error());

	if($row = mysql_fetch_assoc($result)){
		$return_me = $row['is_clinical'];
		
		if($return_me){//because we are converting to json...
	//		echo "its db clinical";
			$return_me = true;
		}else{
	//		echo "its db not";
			$return_me = false;
		}

	}else{
		//we have not seen this url.. we need to use the wikipedia API
		//to see if this is a clinical url...
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, 
			'PicAxe/1.0 (http://www.fredtrotter.com/; fred.trotter@gmail.com)');

		$api_url = "http://en.wikipedia.org/w/api.php?format=json&action=query&titles=$title";
		$api_url .= "&prop=revisions&rvprop=content";
        	curl_setopt($ch, CURLOPT_URL, $api_url);
        	$result = curl_exec($ch);
        	if (!$result) {
                	exit('cURL Error: '.curl_error($ch));
        	}
		$clinical_detect = clinical_detect($result);
		if($clinical_detect['is_clinical']){
			$because = $clinical_detect['because'];
			$clinical = 1; //for the db
			$return_me = true; //for json
		}else{
			$because = '';
			$clinical = 0;
			$return_me = false;
		}
	

	$save_sql = "INSERT INTO `browser_spade`.`is_clinical_url` (
`id` ,
`wiki_title` ,
`is_clinical`,
`because`
)
VALUES (
NULL , '$title', '$clinical','$because'
);";

		mysql_query($save_sql) or die("Could not save with $save_sql".mysql_error());

	}

	if(isset($clinical_detect)){
		$final_result = $clinical_detect;
	}else{
		$final_result = array('is_clinical' => $return_me);
	}
	$json_result = json_encode($final_result);

	echo $json_result;
	exit();

