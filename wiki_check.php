<?php
	require_once('parse_references.functions.php');

	$title = $_GET['title'];

	if(isset($_GET['oldid'])){
		$oldid = $_GET['oldid'];
		$oldid_url = "&oldid=$oldid";
	}else{
		$oldid = 0;
		$oldid_url = "";
	}
	
	$data_source_url = "http://spadeserver.ft1.us/parse_references.php?title=$title$oldid_url";	

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
<li>
<a href="'.$data_source_url.'"> Data Source URL </a>
</li>
<li>
<a href="/wiki_test.php?title='.$title.$oldid_url.'"> Wikipedia Raw API result</a>
</li>
	</p>
      </div>
    </div>
<table border="thin" style="word-wrap:break-word; table-layout: fixed; width: 1200px">
<tr>
<th> Wiki Text </th>
<th> Wiki HTML </th>
<th> PubMed API Results </th>
</tr>
';

	$reference_json = parse_these_references($title,$oldid);
	$data = json_decode($reference_json,true);
foreach($data as $line_number => $this_line_data){

	
	extract($this_line_data);
	$abstract_html = "";
	foreach($abstracts as $this_abstract_array){

		if($this_abstract_array['is_review']){
			$abstract_html .= "<h4>This following abstract is a review article</h4>";
		}else{
			$abstract_html .= "<h4>This following abstract is NOT a review article</h4>";
		}
	
		$abstract_html .= nl2br($this_abstract_array['abstract']);

	}

	if(strlen($original_wiki_text) > 0){
		print_row($line_number,$original_wiki_text,$html,$abstract_html);
	}
}

function print_row($line_number,$wiki_text,$wiki_html,$abstract_text){

	echo "
<tr>
<td width='20%' valign='top'>
<h1>Line $line_number</h1>
$wiki_text
</td>
<td width='40%' valign='top'>
$wiki_html
</td>
<td width='40%' valign='top'>
$abstract_text
</td>

</tr>
	";
}

echo '
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
