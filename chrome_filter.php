<?php

echo '
<h1>Automatic filtering complete</h1>
<h3>Choose which urls you would like to submit</h3>
<form role="form" action="chrome_save.php" method="POST">
';

foreach($_POST as $key => $value){
	echo "<input name='$key' id='$key' type='hidden' value='$value'>\n";
}

echo '
<ul class="list-group">
';

foreach($big_array as $big_url_id => $smaller_array){

	$threads = $smaller_array['threads'];
	$urls = $smaller_array['urls'];

	$big_url = $urls[$big_url_id]['url'];

	echo "<li class='list-group-item'>\n";
	echo "<h3> From $big_url </h3>\n";
	echo '<ul class="list-group">';
	foreach($threads as $thread_id => $this_thread){
		echo "<li class='list-group-item'>\n";
		$url_id = $this_thread['url'];
		$url_label = $urls[$url_id]['url'];
		$url_title = $urls[$url_id]['title'];
		$my_label = "<abbr title='$url_label'><a href='$url_label'>$url_title</a> </abbr>";
		echo get_checkbox($big_url_id,$my_label,$url_id);		
		
		echo "</li>";
	}

	echo "</ul></li>\n";
}

echo '
</ul>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
';


