<?php
	require_once('config.php');
	session_start();	

	require_once('header.php');

	$big_array = $_SESSION['big_array'];
	$got_something = false;
	$stuff_to_save = array();
	foreach($big_array as $big_url_id => $smaller_array){

		if(isset($_POST['url_'.$big_url_id])){
			$checks = $_POST['url_'.$big_url_id];
		}else{
			$checks = array();
		}
		$ok = true;
		foreach($checks as $key => $value){
			if($value == 0){
				$ok =false;	//if any chain is denied the whole 
						//chain is rejected...
			}
		}

		if($ok){
			$got_something = true;
			$stuff_to_save[$big_url_id] = $big_url_id; //this chain will saved..
		}
		
	}

	if($got_something){

		$user_type = mysql_real_escape_string($_POST['user_type']);
		$user_email = mysql_real_escape_string($_POST['user_email']);
		$new_psuedo_user_sql = "
INSERT INTO  `browser_spade`.`psuedo_users` (
`id` ,
`type_id`,
`email`
)
VALUES (
NULL ,  '$user_type', '$user_email'
);
";

		mysql_query($new_psuedo_user_sql) or 
			die("No pseudo user for you with $new_psuedo_user_sql".mysql_error());

		$puser_id = mysql_insert_id();

		echo "<h1> All data connected to the following urls has been filtered </h1>\n";
		echo '<ul class="list-group">';
		$facts_added = 0;
		$facts_filtered = 0;
		$urls_added = 0;
		$urls_filtered = 0;
		foreach($big_array as $big_url_id => $smaller_array){
	
			$threads = $smaller_array['threads'];
        		$urls = $smaller_array['urls'];
		
			if(!isset($stuff_to_save[$big_url_id])){
				$thread_count = count($threads);
				$url_count = count($urls);
				$total_facts_filtered_this_time = $thread_count + $url_count;
				$facts_filtered += $total_facts_filtered_this_time;
				$urls_filtered += $url_count;
				echo "<li class='list-group-item'>\n";
				echo "$url_count urls  related to <br>".$urls[$big_url_id]['url'] . "<br> has been filtered. Not Saved."; 
				echo "</li>";
				continue; // we do not save if the user cleaned the data...
			}
			//ok then we are saving this thread!!

			foreach($threads as $thread_id => $this_thread){
				$facts_added++;
				$visit_id = $this_thread['id'];
				$from_visit = $this_thread['from_visit'];
				$place_id = $this_thread['place_id'];
				$visit_date = $this_thread['visit_date'];
				$visit_type = $this_thread['visit_type'];
				$session = $this_thread['session'];

				$thread_sql = "
INSERT INTO  `browser_spade`.`firefox_moz_historyvisits` (
`id` ,
`puser_id` ,
`visit_id` ,
`from_visit` ,
`place_id` ,
`visit_date` ,
`visit_type` ,
`session` 
)
VALUES (
NULL ,  '$puser_id',  '$visit_id',  '$from_visit', '$place_id',  '$visit_date',  
	'$visit_type', '$session'
);
";
				mysql_query($thread_sql) or die("Could not add thread with $thread_sql <br>".mysql_error());
	
			}

                        foreach($urls as $url_id => $this_url){

				$facts_added++;
				$urls_added++;
                                $place_id = $this_url['id'];
                                $url = mysql_real_escape_string($this_url['url']);
                                $title = mysql_real_escape_string($this_url['title']);
                                $rev_host = mysql_real_escape_string($this_url['rev_host']);
                                $visit_count = $this_url['visit_count'];
                                $hidden = $this_url['hidden'];
                                $typed = $this_url['typed'];
                                $favicon_id = $this_url['favicon_id'];
                                $frecency = $this_url['frecency'];
                                $last_visit_date = $this_url['last_visit_date'];
                                $guid = $this_url['guid'];

                                $url_sql = "
INSERT INTO  `browser_spade`.`firefox_moz_places` (
`id` ,
`puser_id` ,
`place_id` ,
`url` ,
`title` ,
`rev_host` ,
`visit_count` ,
`hidden` ,
`typed` ,
`favicon_id` ,
`frecency` ,
`last_visit_date` ,
`guid`
)
VALUES (
NULL,  	'$puser_id',  '$place_id',  '$url',  '$title',  
	'$rev_host', '$visit_count',  '$hidden', '$typed',  '$favicon_id', 
	'$frecency', '$last_visit_date', '$guid' 
);
";
                                mysql_query($url_sql) or die("Could not add place with $url_sql <br>".mysql_error());

                        }

				

	}
}

echo "</ul>";
echo "<h1>Thank you!!</h1>\n";
echo "<h3> A total of $urls_added discrete URLs where added 
while $urls_filtered URLs where filtered as the result of your donation.</h3>
	";

echo "<p> We are still building the tool that allows you to browse our data. 
	Be sure to email me at fred.trotter at (that email service google makes)
	dot com to get your access. 
	We have no way of knowing if you actually submitted data to us (we keep no records at all) 
	so we will just take your word for it...
	We have special privileges coming for medical students, so be sure to mention if you are currently in medical school!! </p>
";

require_once('footer.php');
