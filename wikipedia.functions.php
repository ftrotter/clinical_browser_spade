<?php
/*
 * Given a url, this function returns the title of the Wikipage that is then useful for further API calls
 */
        function get_wiki_title($url){

                $url_array = explode('/',$url);

//                $title = array_pop($url_array); //this does not work for things like HIV/AIDS

                $the_http = array_shift($url_array);
                $nothing = array_shift($url_array);
                $domain = array_shift($url_array);

		$the_word_wiki = array_shift($url_array);
		$title = implode('/',$url_array); //should account for HIV/AIDS


                if(strpos($domain,'wikipedia') !== false){
                        return($title);
                }else{
                        return(false);
                }
    
        }


/*
 * Takes a look at the json results from a wiki call, and determines if it is a redirect.
 */

function is_redirect($wiki_json){

	$redirect_string = '"#REDIRECT ';
	if(strpos($wiki_json,$redirect_string) !== false){
		return(true); // we found the string, which means this is a redirect file...
	}else{
		return(false);
	}
}
/*
 * if a given json is a redirect, get the place it redirects to and return the title for that page
 */
function get_redirect($wiki_json){

	preg_match_all('/\[\[(.+?)\]\]/u',$wiki_json,$matches); // find any string inside the [[ ]] which form wiki links...

	if(!isset($matches[1][0])){
		echo json_encode(array('result' => 'error','problem' => 'regex fail on redirect'));
		exit();
	}
		
	$new_string = $matches[1][0];

	return($new_string); //we return only the first match... 

}


function get_wiki_api_url($title,$revision_id = null){

                if(is_null($revision_id) || $revision_id == 0){
                        //we do nothing
                        $url_parameters = "&titles=$title";
                }else{
                        $url_parameters = "&revids=$revision_id";
                }

                $api_url = "https://en.wikipedia.org/w/api.php?format=json&action=query$url_parameters";
                $api_url .= "&prop=revisions&rvprop=content";

		return($api_url);
}


/*
 * Given a particular title of a wikipage, download the JSON representation...
 */
function download_wiki_result($title,$id_to_get = null){


		$wiki_tmp_file = "./tmp/$title.$id_to_get.wiki.json";
		if($id_to_get != 0){
			if(file_exists($wiki_tmp_file)){
				$json = file_get_contents($wiki_tmp_file);
				return($json);
			}
		}else{
			//if we get here then we are looking for the current version of the file...
			if(file_exists($wiki_tmp_file)){
				$file_mod_time = filemtime($wiki_tmp_file);
				$now_time = time();
				$age_in_seconds = $now_time - $file_mod_time;
				$seconds_in_a_day = 86400;
				if($age_in_seconds < $seconds_in_a_day){
					//then we have already used the API for this today...
					//lets just return the cache...
					$json = file_get_contents($wiki_tmp_file);
					return($json);
				}else{
					//then our cache is old.. we need to re-download...
				}

			}
		}


		$api_url = get_wiki_api_url($title,$id_to_get);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_USERAGENT,
                        'MET/1.0 (http://www.fredtrotter.com/; fred.trotter@gmail.com)');

                curl_setopt($ch, CURLOPT_URL, $api_url);
                $result = curl_exec($ch);
                if(curl_error($ch)) {
                        exit('wikipedia.functions.php cURL Error: '.curl_error($ch)."<br> hitting $api_url");
                }

		if(!$result){
			echo "wikipedia.functions.php negative result from title of: $title url: <a href='$api_url'>$api_url</a>";
			echo "<pre>";
			var_export(curl_getinfo($ch));
			exit();
		}

		if(is_redirect($result)){ //sometimes wiki pages are just stubs that redirect
					//the web user just sees the right page...
					//but the API actually returns the redirect...
			$redirect_to = get_redirect($result); //this returns the title that the orginal title redirects to..
			$result = download_wiki_result($redirect_to); //this returns the wiki_json for the right title.
		}

		file_put_contents($wiki_tmp_file,$result);
		return($result);

}


