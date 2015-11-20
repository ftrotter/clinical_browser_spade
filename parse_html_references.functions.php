<?php

	require_once('wikipedia.functions.php');
	require_once('simple_html_dom.php');


	
	function parse_html_references($title,$id_to_get){


		$force = false;

		$summary_tmp_file = "./tmp/$title.$id_to_get.html.summary.json";

		if(!isset($_GET['force']) && !$force){
			if(file_exists($summary_tmp_file)){ //lets use the cache...
				$title_json = file_get_contents($summary_tmp_file);		
	//			header('Content-Type: application/json');
				return($title_json);
				}
		}

		//we also cache the wiki file... but download_wiki_result knows how to do that!!

		$html_tmp_file = "./tmp/$title.$id_to_get.html";
		if(file_exists($html_tmp_file)){
			$wiki_html = file_get_contents($html_tmp_file);
		}else{	
			$wiki_url = "https://en.wikipedia.org/w/index.php?title=$title&oldid=$id_to_get";
			$wiki_html = file_get_contents($wiki_url);
			file_put_contents($html_tmp_file,$wiki_html);
		}

		$HTML = str_get_html($wiki_html);

		$OL = $HTML->find('.references');

		$results = [];

		$references = [];
	
		foreach($OL as $this_OL){
			foreach($this_OL->find('li') as $this_LI){
				$content = $this_LI->plaintext;
				
				$class = 'none';
				foreach($this_LI->find('cite') as $this_cite){
					$class = $this_cite->class;
				}
				if(isset($results[$class])){
					$results[$class]++;
				}else{
					$results[$class] = 1;
				}
				$tmp = [
					'reference' => $content,
					'type' => $class,
					];
				$references[] = $tmp;
		
			}
		}

		$return_me = [];
		foreach($results as $class => $count){
			$tmp = [
				'reference_type' => $class,
				'count' => $count
			];
			$return_me['summary'][] = $tmp;
		}

		$return_me['references'] = $references;

		return(json_encode($return_me));

	}

?>
