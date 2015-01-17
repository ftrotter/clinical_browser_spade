<?php

	require_once('wikipedia.functions.php');
	require_once('simple_html_dom.php');
	require_once('parse_references.functions.php');

	
	$title = $_GET['title'];

	
	if(isset($_GET['oldid'])){
		$id_to_get = $_GET['oldid'];
	}else{
		$id_to_get = 0;
	}


	$data_json = parse_these_references($title,$id_to_get);
	header('Content-Type: application/json');
	echo $data_json;



