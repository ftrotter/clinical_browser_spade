<?php

	require_once('config.php');

	$sql = "SELECT *  FROM `chrome_urls` WHERE `title` LIKE '%Wikipedia%'
ORDER BY RAND() LIMIT 0, 3";

	$result = mysql_query($sql) or die("nope $sql".mysql_error());
/*
         <ul class="list-group">
            <li class="list-group-item">
              <a href>Diabetes</a>
            </li>
            <li class="list-group-item">
              <a href>Insulin</a>
            </li>
            <li class="list-group-item">
              <a href>Pancreas</a>
            </li>
          </ul>

*/

	$return_me = '<ul class="list-group">';
	while($row = mysql_fetch_assoc($result)){
	
		$url = $row['url'];
		$title = $row['title'];
	
		$return_me .= "	
            <li class='list-group-item'>
              <a href='$url'>$title</a>
            </li>";

	}
	$return_me .= "</ul>";

	echo $return_me;

