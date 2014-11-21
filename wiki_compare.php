<?php
	require_once('stats.functions.php');
	$title = $_GET['title'];

	$diff = $_GET['diff'];
	$oldid = $_GET['oldid'];


	$oldid_url = "http://spadeserver.ft1.us/parse_references.php?title=$title&oldid=$oldid";
	$diff_url = "http://spadeserver.ft1.us/parse_references.php?title=$title&oldid=$diff";




echo '
<html><head>
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
          <a class="navbar-brand" href="#">Spade Wikipedia Reference Checker</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
	<h1>Mining '.$title.'</h1>
        <h3>Spade Wikipedia to PubMed Reference checker</h3>
	<p> 
This project is mashup of the Wikipedia and PubMed API. Using this, we can see statistics on the types and quality of medical references on a medical wikipedia article.
<ul>
<li>
<a href="https://en.wikipedia.org/wiki/'.$title.'"> Original Article</a>
</li>
<li> <a href="https://en.wikipedia.org/w/index.php?title=Amyloidosis&diff='.$diff.'&oldid='.$oldid.'">Wikipedias Diff Tool</a>
</li>
<li>
<a href="'.$oldid_url.'">Old ID Data UrL </a>
</li>
<li>
<a href="'.$diff_url.'">Diff Data UrL </a>
</li>
</ul>
	</p>
      </div>
    </div>
<div>
';

	$reference_json_oldid = file_get_contents($oldid_url);
	$reference_json_diff = file_get_contents($diff_url);

	$data_oldid = json_decode($reference_json_oldid,true);
	$data_diff = json_decode($reference_json_diff,true);

	echo "<pre>";
	$oldid_stats = run_all_stats($data_oldid);
	var_export($oldid_stats);

	$diff_stats = run_all_stats($data_diff);
	var_export($diff_stats);
	
	echo "</pre>";




echo '
</div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
';


echo "</body></html>";

?>
