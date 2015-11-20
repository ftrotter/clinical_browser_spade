<?php

	require_once('wikipedia.functions.php');
	require_once('simple_html_dom.php');



	function test_fix_ref_tag(){


		$variations = [
		"<ref>",
		"<ref name='with-dashes'>",
		"<ref name=with-dashes>",
		"<ref name=something >",
		"<ref name=something\>",
		"<ref name=else \>",
		"<ref name=else >",
                '<ref name="something" >',
                '<ref name="something"\>',
                '<ref name="else" \>',
                '<ref name="else" >',
		'</ref>',

		];

		$random_text = [
		"ipso lorem",
		"weraf ra erg aas",
		];

		$test_us = [];
		foreach($variations as $top_variation){
			foreach($variations as $botton_variation){
				foreach($random_text as $rt){
					$test_us[] = "$rt $top_variation $rt $bottom_variation $rt";		
				
				}	
			}
		}

		foreach($test_us as $test_this){
			$result = fix_ref_tag($test_this);
			echo "changes \n\t$test_this to \n\t $result\n";
		}


	}


	function fix_ref_tag($wiki_line){


        	$re1='.*?';     # Non-greedy match on filler
        	$re2='(<ref[^>]+>)';    # Tag 1

        	if ($c=preg_match_all ("/".$re1.$re2."/is", $this_line, $matches))
        	{
                	$this_match =$matches[1][0];
        	}

		


	}

	


	
	function parse_these_references($title,$id_to_get){


	$force = false;

	$summary_tmp_file = "./tmp/$title.$id_to_get.summary.json";

	if(!isset($_GET['force']) && !$force){
		if(file_exists($summary_tmp_file)){ //lets use the cache...
			$title_json = file_get_contents($summary_tmp_file);		
	//		header('Content-Type: application/json');
			return($title_json);
		}
	}

	//we also cache the wiki file... but download_wiki_result knows how to do that!!
	$json = download_wiki_result($title,$id_to_get);

	if(strlen($json) == 0){
		echo "parse_these_references ERROR: download_wiki_result returning blank";
		exit();
	}

	$wiki_text = get_wikitext_from_json($json);

	$preview = false;
	if($preview){
		echo "<pre>";
		echo $wiki_text;
		echo "</pre>";
		echo "<h1> End Wikitext </h1>";
	}


	//these remove the newlines from within the templates, which makes new lines parseable later...
	$new_wiki_text = compress_wikitext('{{','}}',$wiki_text);
	$new_wiki_text = compress_wikitext('{|','|}',$new_wiki_text);

	
	//here we are going to convert all of the <ref links to normal wiki references...
	//so that we can properly associate them with the various lines...
/*
	echo "<pre>";
	echo htmlentities($new_wiki_text);
	echo "</pre><br><br>";
*/
	$html = str_get_html($new_wiki_text);

	$ref_array = [];
	$ref_count = [];
	foreach($html->find('ref') as $this_ref){	
		if(strlen($this_ref->plaintext) > 0){
			//then this is destination...
			$ref_array[$this_ref->name] = $this_ref->plaintext;
		}else{
			if(isset($ref_count[$this_ref->name])){
				$ref_count[$this_ref->name]++;
			}else{
				$ref_count[$this_ref->name] = 1;
			}	
		}
	}	

//	echo "<pre>";
//	var_export($ref_array);
//	var_export($ref_count);


//	exit();



//	echo "<br><pre>$new_wiki_text</pre>";


	//the staring default...
	$last_section = "Introduction";

	$wiki_lines = explode("\n",$new_wiki_text);
	//First lets understand the structure of the document by searching for heading tags..

	$all_templates = array();
	$all_citations = array();
	$all_links = array();
	$section_map = array();
	$processed_wiki_text = array();
	$wiki_html = array();
	$all_metadata = array();	

	foreach($wiki_lines as $line_number => $this_wiki_line){

		//lets empty the metadata...
		$all_metadata[$line_number] = array(
			'link_count' => 0,
			'link_label_count' => 0,
			"reference_count_journal" => 0,
            		"reference_count_journal_pmid" => 0,
            		"reference_count_journal_review" => 0,
            		"reference_count_book" => 0,
            		"reference_count_web" => 0,
			"section" => ''
			);


		//first lets get parsoid to tell is what html this would have all by itself
	
		//this is pretty slow...
		$mine_wiki = true;
		if($mine_wiki){
			$parsoid_html = get_html_from_parsoid($this_wiki_line); 
			$wiki_html[$line_number] = $parsoid_html;
		}
		$is_heading = is_heading_line($this_wiki_line);

		if($is_heading){
			$last_section = $is_heading;
			//echo "$line_number is a heading :";
			//echo "$this_wiki_line<br>";
		}else{

			if(is_special_line($this_wiki_line)){
		//		echo "$last_section: $line_number is special:  ";
		//		echo "$this_wiki_line<br>";
			}else{

				//echo "$last_section: $line_number is normal: $this_wiki_line <br>";
				$regex = "/\{\{(.*?)\}\}/"; //should catch everything inside double curly braces..
				preg_match_all($regex,$this_wiki_line,$matches);
				if(count($matches[0]) != 0){	
				//	echo "Whole Line: <br>$this_wiki_line";
				//	echo "<h1>Template matches </h1>";
				//	echo "<pre>";
				//	var_export($matches[1]);	
				//	echo "</pre>";
		
					$all_templates[$line_number] = $matches[1];				
			
					//Now we replace the templates with A Token string
					$this_wiki_line = preg_replace($regex," |||TEMPLATE||| ",$this_wiki_line);

				}
	
				$regex = "/\[\[(.*?)\]\]/"; //should catch everything inside double square braces..
				
				preg_match_all($regex,$this_wiki_line,$matches);
                                if(count($matches[0]) != 0){    
				//	echo "Whole Line: <br>$this_wiki_line";
				//	echo "<h1> Link Matches </h1>";
                                //      echo "<pre>";
                                //      var_export($matches[1]);
                                //     	echo "</pre>";

                                        $all_links[$line_number] = $matches[1];

                                        //Now we replace the templates with A Token string
                                        $this_wiki_line = preg_replace($regex," |||LINK||| ",$this_wiki_line);

                                }

				$processed_wiki_text[$line_number] = $this_wiki_line;

			}//end not a special line
		}//end not a heading
	

		$section_map[$line_number] = $last_section; //remember what section every line number is.

	}//end foreach wiki_line

// Before data mining on wikipedia lets do some basic data about the links
foreach($all_links as $line_number => $links){

	$link_count = count($links);
	$labeled_links = 0;

	foreach($links as $linktext){
		if(strpos($linktext,'|') !== false){
			//then this link has a label!!
			$labeled_links++;
		}
	}

	$all_metadata[$line_number]['link_count'] = $link_count;
	$all_metadata[$line_number]['link_labeled_count'] = $labeled_links;

}



$pubmed_abstracts = array();
$pubmed_review_status = array();
//use parsoidapi to get the html5 from the original wikitext...
$total_citations = 0;
$total_journal_citations = 0;
$total_web_citations = 0;
$total_book_citations = 0;

$abstract_cache = array();
$summary_cache = array();

$abstract_base_url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=text&rettype=abstract&id=";
$summary_base_url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&retmode=json&rettype=abstract&id=";


//echo "<pre>";
foreach($all_templates as $line_number => $this_template_array){

	$line_book_count = 0;
	$line_journal_count = 0;
	$line_web_count = 0;
	$line_journal_pmid_count = 0;
	$line_journal_review_count = 0;

	foreach($this_template_array as $template_number => $a_template){
		//but it is a journal citation?
		$is_journal = false;
		$is_web = false;
		$is_book = false;

		$is_citation = strpos(strtolower($a_template),'cite ');
		if($is_citation !== false){ //because it will often be '0'
			$all_citations[$line_number][] = $a_template;
			$total_citations++;
			$is_citation = true;
			$is_journal = strpos(strtolower($a_template),'journal');
			$is_web = strpos(strtolower($a_template),'web');
			$is_book = strpos(strtolower($a_template),'book');
		}

	
		if($is_journal){
		//	echo "This is a journal $a_template<br>";
			$citation_array = explode('|',$a_template);
			foreach($citation_array as $citation_block){
				if(strpos(strtolower($citation_block),'pmid') !== false){
					//then this is the pmid = XXXX block...
					$citation_block = str_replace(' ','',$citation_block);//remove all whitespace
					list($trash,$pmid) = explode('=',$citation_block);
		//				echo "The PMID is $pmid. Whos is a badass?<br>";
	
					if(is_numeric($pmid)){
						$line_journal_pmid_count++;
						//The code to fetch the abstracts from PubMed.
						if(isset($abstract_cache[$pmid])){
							$pubmed_abstracts[$line_number][$template_number]['abstract'] = $abstract_cache[$pmid];
						}else{
							$abstract_tmp_file = "./tmp/$pmid.abstract.txt";
							if(file_exists($abstract_tmp_file)){
								$this_abstract = file_get_contents($abstract_tmp_file);
							}else{
								$this_abstract = file_get_contents("$abstract_base_url$pmid");
								file_put_contents($abstract_tmp_file,$this_abstract);
							}
							//echo "$this_abstract<br>";
							$abstract_cache[$pmid] = $this_abstract;
							$pubmed_abstracts[$line_number][$template_number]['abstract'] = $this_abstract;		
						}

						//The code to fetch the review status of the article...
                                        	if(isset($summary_cache[$pmid])){
                                                	$pubmed_abstracts[$line_number][$template_number]['is_review'] = $summary_cache[$pmid];
                                        	}else{
							$summary_tmp_file = "./tmp/$pmid.summary.json";
							if(file_exists($summary_tmp_file)){
								$this_summary_json = file_get_contents($summary_tmp_file);	
							}else{
								$this_summary_json = file_get_contents("$summary_base_url$pmid");
								file_put_contents($summary_tmp_file,$this_summary_json);
							}
							//echo "$this_summary_json<br>";
							$this_summary = json_decode($this_summary_json,true);
							if(isset($this_summary['result'][$pmid]['pubtype']['Review'])){
								$line_journal_review_count++;
								$is_review = true;
							}else{
								$is_review = false;
							}

                                                	$summary_cache[$pmid] = $is_review;
                                                	$pubmed_abstracts[$line_number][$template_number]['is_review'] = $is_review;             
                                        	}			
					
					}else{
						//this is a shitty pmid... I have no idea what to make of it...
					}

				}
			}

		}else{
		//	echo "This is not a journal $a_template<br>";
		}

		if($is_book){
			$line_book_count++;
			$total_book_citations++;
		}

		if($is_web){
			$line_web_count++;
			$total_web_citations++;
		}

		if($is_journal){
			$line_journal_count++;
			$total_journal_citations++;
		}


	}//all templates on this line...


	$all_metadata[$line_number]['reference_count_journal'] = $line_journal_count;
	$all_metadata[$line_number]['reference_count_journal_pmid'] = $line_journal_pmid_count;
	$all_metadata[$line_number]['reference_count_journal_review'] = $line_journal_review_count;
	$all_metadata[$line_number]['reference_count_book'] = $line_book_count;
	$all_metadata[$line_number]['reference_count_web'] = $line_web_count;


}// all lines
//echo "</pre>";



//here we have wiki_lines, all_templates, all_links and section map... all of which are keyed by line number...
$data = array();
foreach($wiki_lines as $line_number => $this_wiki_line){
	
	$all_metadata[$line_number]['section'] = $section_map[$line_number];

	if(isset($processed_wiki_text[$line_number])){
		$this_processed_text = $processed_wiki_text[$line_number];
	}else{
		$this_processed_text = $this_wiki_line;
	}

	if(isset($all_links[$line_number])){
		$this_links = $all_links[$line_number];
	}else{
		$this_links = array();
	}

        if(isset($all_templates[$line_number])){
                $this_templates = $all_templates[$line_number];
        }else{
                $this_templates = array();
        }

        if(isset($all_metadata[$line_number])){
                $this_metadata = $all_metadata[$line_number];
        }else{
                $this_metadata = array();
        }

        if(isset($all_citations[$line_number])){
                $this_citations = $all_citations[$line_number];
        }else{
                $this_citations = array();
        }

        if(isset($pubmed_abstracts[$line_number])){
                $this_abstracts = $pubmed_abstracts[$line_number];
        }else{
                $this_abstracts = array();
        }


        if(isset($wiki_html[$line_number])){
                $this_wiki_html = $wiki_html[$line_number];
        }else{
                $this_wiki_html = '';
        }


	//should always be set...
	$this_section = $section_map[$line_number];

	if(strlen($this_wiki_line) > 0){
	$data[] = array(
		'line_number' => $line_number,
		'original_wiki_text' => $this_wiki_line,
		'processed_wiki_text' => $this_processed_text,
		'links' => $this_links,
		'templates' => $this_templates,
		'metadata' => $this_metadata,
		'citations' => $this_citations,
		'abstracts' => $this_abstracts,
		'html' => $this_wiki_html,
		);
	}
}

	$data_json = json_encode($data,JSON_PRETTY_PRINT);

	//now lets save the cache
	file_put_contents($summary_tmp_file,$data_json);

	return($data_json);

	}


//returns false if not a heading line  
//returns the name of the heading if it is a heading  
        function is_special_line($line){  
  
		//if the line begins with {{ it is a template line, and infobox or something..
		//not a normal line...

		if(strpos($line,'{{') === 0){
			return(true);
		}		

		if(strpos($line,'{|') === 0){
			return(true);
		}

		return(false);

        }  



//returns false if not a heading line
//returns the name of the heading if it is a heading
	function is_heading_line($line){
		$heading_regex_array = array(
			6 => "/^======(.+?)======$/m",					// SubSubSubsubheading
			5 => "/^=====(.+?)=====$/m",					// SubSubsubheading
			4 => "/^====(.+?)====$/m",						// Subsubheading
			3 => "/^===(.+?)===$/m",						// Subheading
			2 => "/^==(.+?)==$/m",						// Heading
			1 => "/^=(.+?)=$/m",						// Heading
			);

		$is_heading = false;
		foreach($heading_regex_array as $level => $this_regex){

			$is_heading = preg_match($this_regex,$line,$matches);		
	
			if($is_heading){
				return(trim($matches[1]));
			}


		}

		return($is_heading);

	}



	function get_wikitext_from_json($json){
        	$wiki_data = json_decode($json,true);

        //echo "<pre>";
        //var_export($wiki_data);       
        //echo "</pre>";
 
        	if(isset($wiki_data['query']['pages'])){

                	$page_array = $wiki_data['query']['pages'];
                	//we don't know the page id, so lets pop instead..
                	$page = array_pop($page_array);

			if(isset($page['revisions'][0]['*'])){
                		$wiki_text = $page['revisions'][0]['*']; //does this work?
                		return $wiki_text;
			}else{
				return(false);
			}
		}else{
			return(false);
		}	

	}


function compress_wikitext($start,$end,$wiki_text){

        $wiki_lines = explode("\n",$wiki_text);
        $total_diff = 0;
        $new_wiki_text = '';
        foreach($wiki_lines as $line_number => $this_line){

                if($total_diff > 0){
                        $nl = "";
                }else{
                        $nl = "\n";
                }

                $opencurly_count = substr_count($this_line, $start);
                $closecurly_count = substr_count($this_line, $end);

                if($opencurly_count == 0 && $closecurly_count ==0){
                        $new_wiki_text .= "$nl$this_line";
                        continue;
                }                       
                                        
                if($opencurly_count == $closecurly_count){
                        //echo "line $line_number has $opencurly_count template<br>";
                        $new_wiki_text .= "$nl$this_line";
                }else{                  
                                
                        $diff = $opencurly_count - $closecurly_count;
                        $total_diff = $total_diff + $diff;
                       // echo "This $line_number has a diff of $diff with a running total of $total_diff <br>";
                       // echo "$this_line<br>";
                        $new_wiki_text .= "$nl$this_line";
                }
        }

        return($new_wiki_text);
}


function post_to_url($url, $data) {
   $fields = '';
   foreach($data as $key => $value) { 
      $fields .= $key . '=' . $value . '&'; 
   }
   rtrim($fields, '&');

   $post = curl_init();

   curl_setopt($post, CURLOPT_URL, $url);
   curl_setopt($post, CURLOPT_POST, count($data));
   curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
   curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

   $result = curl_exec($post);

   curl_close($post);
	
   return($result);

}


	function get_html_from_parsoid($wiki_text){

		$parsoid_url = "http://parsoid-lb.eqiad.wikimedia.org/enwiki/";

                $parsoid_data = array(
                        'wt' => $wiki_text,
                        'body' => 1,
                );

		$parsoid_html = post_to_url($parsoid_url,$parsoid_data);
		

		return($parsoid_html);


	}



?>
