<?php
/*
	runs stats on arrays of json data.. 

*/


	function run_all_stats($wiki_data){

		if(count($wiki_data) == 0){
			//nope...
			return(array());

		}


		$total_citations = total_citations($wiki_data);
		$total_journal_citations = total_journal_citations($wiki_data);
		$total_pmid_citations = total_journal_pmid_citations($wiki_data);
		$total_review_citations = total_journal_review_citations($wiki_data);
		$total_book_citations = total_book_citations($wiki_data);
		$total_web_citations = total_web_citations($wiki_data);
       		$total_links = total_links($wiki_data);
        	$sections = get_sections($wiki_data);
        	$total_sections = total_sections($wiki_data);
        	$total_lines = total_lines($wiki_data);

		$avg_citation_per_line = round($total_citations / $total_lines,5);
		$other_citation_total = $total_book_citations + $total_web_citations;
		if($other_citation_total > 0){
			$ratio_journal_other = round(	$total_journal_citations / 
						($total_book_citations + $total_web_citations)
						,2);
		}else{
			$ratio_journal_other = 0;
		}	
		$avg_lines_per_section = round($total_lines / $total_sections,5);


		$stats = array(
			'total_citations' => $total_citations,
			'total_journal_citations' => $total_journal_citations,
			'total_pmid_citations' => $total_pmid_citations,
			//'total_review_citations' => $total_review_citations,
			'total_book_citations' => $total_book_citations,
			'total_web_citations' => $total_web_citations,
			'total_sections' => $total_sections,
			'total_lines' => $total_lines,
			'avg_citation_per_line' => $avg_citation_per_line,
			'ratio_journal_to_other_citation_types' => $ratio_journal_other,
			'avg_lines_per_section' => $avg_lines_per_section,
			);


		return($stats);

	}


	function total_citations($wiki_data){
		$total_references = 0;
		foreach($wiki_data as $this_line){
			$total_references = $total_references + count($this_line['citations']);	
		}
		return($total_references);
	}

	function total_journal_citations($wiki_data){
		$return_me = 0;
		foreach($wiki_data as $this_line){
			$return_me = $return_me + $this_line['metadata']['reference_count_journal'];	
		}
		return($return_me);
	}

	function total_journal_pmid_citations($wiki_data){
		$return_me = 0;
		foreach($wiki_data as $this_line){
			$return_me = $return_me + $this_line['metadata']['reference_count_journal_pmid'];	
		}
		return($return_me);
	}

	function total_journal_review_citations($wiki_data){
		$return_me = 0;
		foreach($wiki_data as $this_line){
			$return_me = $return_me + $this_line['metadata']['reference_count_journal_review'];	
		}
		return($return_me);
	}


	function total_book_citations($wiki_data){
		$return_me = 0;
		foreach($wiki_data as $this_line){
			$return_me = $return_me + $this_line['metadata']['reference_count_book'];	
		}
		return($return_me);
	}

	function total_web_citations($wiki_data){
		$return_me = 0;
		foreach($wiki_data as $this_line){
			$return_me = $return_me + $this_line['metadata']['reference_count_web'];	
		}
		return($return_me);
	}





	function total_links($wiki_data){
		$total_links = 0;
		foreach($wiki_data as $this_line){
			$total_links = $total_links + count($this_line['links']);			
		}
		return($total_links);
	}


	function get_sections($wiki_data){
		$sections = array();
		foreach($wiki_data as $this_line){
			$sections[$this_line['metadata']['section']] = $this_line['metadata']['section'];
		}

		$sections = array_keys($sections);
		return($sections);

	}
	

	function total_sections($wiki_data){
		$sections = get_sections($wiki_data);
		return(count($sections));
	
	}

	function total_lines($wiki_data){
		return(count($wiki_data) -2); //not sure why we always have two empty lines
			//but its a bug.. we have to address here..
	}




?>
