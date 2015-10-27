<?php


	if(isset($_GET['project'])){
		$project = $_GET['project'];
		header('Content-Type: application/json'); //because this is on the web...
	}

	if(isset($argv[1])){
		$project = $argv[1];
	}


	       require_once('simple_html_dom.php');

	$offset = 1;

	$base_url = "https://tools.wmflabs.org/enwp10/cgi-bin/list2.fcgi?namespace=&quality=&run=yes&importance=&score=&sorta=Importance&sortb=Quality&limit=1000&pagename=&projecta=$project&&offset=";

	$result_array = [];

	$still_getting_tables = true;

	while($still_getting_tables){
		$still_getting_tables = false;
	//	echo "working on $offset\n";
		$this_url = $base_url . $offset;

		$this_html_page = get_wiki_page($this_url);
	
		$html = str_get_html($this_html_page);
	
		$the_table = $html->find('.wikitable');
		if($the_table){
			
			$all_rows = [];
			foreach($the_table[0]->find('.list-odd') as $odd_tr){
					$all_rows[] = $odd_tr;
			}

			foreach($the_table[0]->find('.list-even') as $even_tr){
					$all_rows[] = $even_tr;
			}

			foreach($all_rows as $this_tr){
				$still_getting_tables = true; //
			//	echo "this_tr: $this_tr->plaintext \n";
				$my_link_array = $this_tr->find('a');
				$my_link = $my_link_array[0];
				$my_name = $my_link->plaintext;
				$my_url = $my_link->href;

				$all_cells = $this_tr->find('td');
				/*
				foreach($all_cells as $id => $this_cell){
					echo "$id: $this_cell->plaintext\n";
				}
				*/
				
				$importance = $all_cells[2]->plaintext;
				$grade = $all_cells[4]->plaintext;
				$release_score = $all_cells[8]->plaintext;
				$result_array[] = [
					'name' => $my_name,
					'url' => $my_url,
					'importance' => $importance,
					'grade' => $grade,
					'release_score' => $release_score,
					];
				

			}
		}
		$offset = $offset + 1000;
	}


	echo json_encode($result_array);



function get_wiki_page($url){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($ch);
curl_close($ch);

return($html);

}


?>
