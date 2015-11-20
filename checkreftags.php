<?php

	require_once('checkreftags.functions.php');

	
	$json_file = "./tmp/medicine_top.json";	

	$json = file_get_contents($json_file);

	$data = json_decode($json,true);

	foreach($data as $this_article){
		
		$name = rawurlencode($this_article['name']);
		echo "<h1>working on $name</h1>";

		$this_article_data = checkreftags($name,0);

		echo "<ul>";
		foreach($this_article_data as $this_ref){
		
			$this_ref_html = htmlentities($this_ref);
			echo "<li>$this_ref_html</li>";

		}


		echo "</ul>";
	

	}

	



?>
