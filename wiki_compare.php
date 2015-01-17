<?php
	require_once('stats.functions.php');	
	require_once('parse_references.functions.php');
	$title = $_GET['title'];

	$diff = $_GET['diff'];
	$oldid = $_GET['oldid'];


	$oldid_url = "http://spadeserver.ft1.us/parse_references.php?title=$title&oldid=$oldid";
	$diff_url = "http://spadeserver.ft1.us/parse_references.php?title=$title&oldid=$diff";

	$oldid_test_url = "http://spadeserver.ft1.us/wiki_test.php?title=$title&oldid=$oldid";
	$diff_test_url = "http://spadeserver.ft1.us/wiki_test.php?title=$title&oldid=$diff";



echo '
<html><head>
<title> Compare '.$title.' between ('.$oldid.') and ('.$diff.') </title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head><body>';

echo '

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Mining '.$title.'</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
<h2> '.$title.' </h2> <h3> between versions '.$oldid.' and '.$diff.' </h3>
<p>
<a href="https://en.wikipedia.org/wiki/'.$title.'"> Original Article</a>
| <a href="https://en.wikipedia.org/w/index.php?title=Amyloidosis&diff='.$diff.'&oldid='.$oldid.'">Wikipedias Diff Tool</a>
| <a href="'.$oldid_url.'">Old ID Data UrL </a>
| <a href="'.$oldid_test_url.'"> OldID Wikipedia Raw API result</a>
| <a href="'.$diff_url.'">Diff Data UrL </a>
| <a href="'.$diff_test_url.'"> Diff Wikipedia Raw API result</a>
	</p>
      </div>
    </div>
<div>
<table width="100%">
<tr>
<th> Data from the Old Page ('.$oldid.') </th>
<th> Data From the Current Page ('.$diff.') </th>
</tr>
<tr>
';

	$reference_json_oldid = parse_these_references($title,$oldid);
	$reference_json_diff = 	parse_these_references($title,$diff);

	$data_oldid = json_decode($reference_json_oldid,true);
	$data_diff = json_decode($reference_json_diff,true);

	echo "<td><ul>";
	$oldid_stats = run_all_stats($data_oldid);
	foreach($oldid_stats as $key => $value){
		echo "<li> $key: <b>$value</b> </li>";
	}

	echo "</ul></td> <td> <ul>";

	$diff_stats = run_all_stats($data_diff);
	foreach($diff_stats as $key => $value){
		echo "<li> $key: <b>$value</b> </li>";
	}
	
	echo "</ul></pre>";




echo '
</tr>
</table>
</div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
';


echo "</body></html>";

?>
