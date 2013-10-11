<?php

echo '
<h1>Automatic filtering complete</h1>
<h3>Choose which urls you would like to submit</h3>
<form role="form" action="firefox_save.php" method="POST">
';

foreach($_POST as $key => $value){
	echo "<input name='$key' id='$key' type='hidden' value='$value'>\n";
}

echo '
<ul class="list-group">
';


foreach($big_array as $big_url_id => $smaller_array){

	$threads = $smaller_array['threads'];
	$places = $smaller_array['urls'];

	$big_url = $places[$big_url_id]['url'];

	echo "<li class='list-group-item'>\n";
	echo "<h3> From $big_url </h3>\n";
	echo '<ul class="list-group">';
	foreach($threads as $thread_id => $this_thread){
		echo "<li class='list-group-item'>\n";
		$place_id = $this_thread['place_id'];
		$place_label = $places[$place_id]['url'];
		$place_title = $places[$place_id]['title'];
		if(strlen($place_title) < 5){
			$place_title .= ' '.$place_label;
		}
		$my_label = "<abbr title='$place_label'><a href='$place_label'>$place_title</a> </abbr>";
		echo get_checkbox($big_url_id,$my_label,$place_id);		
		
		echo "</li>";
	}

	echo "</ul></li>\n";
}

echo '
</ul>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
';


