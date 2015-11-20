<?php

	/*
		Frankly I wish that I could just parse the wikitext and get the right answer...
		But there are like 15 different ways to encode references and have them turn out correctly...
		Rather than continuing to fight that uphill battle, this system works on just working from the html that parsoid returns
		Creates a ordered list of references that can be much more cleanly parsed.
		Lets just hope that the html parsers support the <cite> tag. Otherwise we are toast...

	*/


	require_once('wikipedia.functions.php');
	require_once('simple_html_dom.php');
	require_once('parse_references.functions.php');
	require_once('parse_html_references.functions.php');

	
	$title = $_GET['title'];

	
	if(isset($_GET['oldid'])){
		$id_to_get = $_GET['oldid'];
	}else{
		$id_to_get = 0;
	}


	$data_json = parse_html_references($title,$id_to_get);
	header('Content-Type: application/json');
	echo $data_json;



