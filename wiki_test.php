<?php
	require_once('wikipedia.functions.php');

	$title = $_GET['title'];

	if(isset($_GET['oldid'])){
		$oldid = $_GET['oldid'];
	}else{
		$oldid = null;
	}

	$url = get_wiki_api_url($title,$oldid);
	$json = download_wiki_result($title,$oldid);
	$data = json_decode($json,true);
	echo "<h1> API Results </h1>";
	echo "<h5> $url </h5>";
	echo "<pre>";
	var_export($data);
	echo"</pre>";	



?>	
