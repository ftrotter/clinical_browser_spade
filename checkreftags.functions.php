<?php

	require_once('wikipedia.functions.php');
	require_once('simple_html_dom.php');
	require_once('parse_references.functions.php');



	


	
	function fix_ref_tag($wiki_line){

        $re1='.*?';     # Non-greedy match on filler
        $re2='(<ref[^>]+>)';    # Tag 1

        if ($c=preg_match_all ("/".$re1.$re2."/is", $this_line, $matches))
        {       
                $return_me[] =$matches[1][0];
        }



	}



	
	function checkreftags($title,$id_to_get){


	$force = false;

	//we also cache the wiki file... but download_wiki_result knows how to do that!!
	$json = download_wiki_result($title,$id_to_get);

	if(strlen($json) == 0){
		echo "parse_these_references ERROR: download_wiki_result returning blank";
		exit();
	}

	$wiki_text = get_wikitext_from_json($json);


	//these remove the newlines from within the templates, which makes new lines parseable later...
	$new_wiki_text = compress_wikitext('{{','}}',$wiki_text);
	$new_wiki_text = compress_wikitext('{|','|}',$new_wiki_text);

	
	//here we are going to convert all of the <ref links to normal wiki references...
	//so that we can properly associate them with the various lines...

	$new_wiki_array = explode("\n",$new_wiki_text);

	$return_me = [];

foreach($new_wiki_array as $this_line){
  	$re1='.*?';	# Non-greedy match on filler
  	$re2='(<ref[^>]+>)';	# Tag 1

  	if ($c=preg_match_all ("/".$re1.$re2."/is", $this_line, $matches))
  	{
      		$return_me[] =$matches[1][0];
	  }
}

	return($return_me);


}//end refcheck function

?>
