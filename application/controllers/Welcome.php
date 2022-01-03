<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	/** Get Countries and make an array in a format of our database table requires */
	function getCountries()
	{
		// die;
		$countries = $this->get_countries();

		$final_arr = array();
		foreach($countries->results as $key => $value) {
			$final_arr[$key]['id'] = $key+1;
			$final_arr[$key]['name'] = '"'.$value->country.'"';
			$final_arr[$key]['isocode'] = '"'.$value->isocode.'"';
		}
		$final_arr = [
			'countries'=>$final_arr,
		];
		// echo"<pre>";print_r($final_arr);

		$this->add_in_api_collection($final_arr);
	}

	/** Get Quran Chapters and About them */
	function getQuranChapters($chapter='') {
		$chapters = $this->get_quran_chapters($chapter);
		// echo "<pre>"; print_r($chapters);
		$final_arr = array();
		// echo '<pre>';print_r($_chapters);

		/** For All chapters */
		if(isset($chapters->chapters)){
			foreach($chapters->chapters as $key => $value) {
				$final_arr[$key]['id'] = $value->id;
				$final_arr[$key]['name_arabic'] = '"'.$value->name_arabic.'"';
				$final_arr[$key]['name_english'] = '"'.$value->name_simple.'"';
				$final_arr[$key]['name_complex'] = '"'.$value->name_complex.'"';
				$final_arr[$key]['Bismillah_pre'] = (empty($value->bismillah_pre))?0: $value->bismillah_pre;
				$final_arr[$key]['verses'] = $value->verses_count;
				$final_arr[$key]['reveal_order'] = $value->revelation_order;
				$final_arr[$key]['reveal_place'] = '"'.$value->revelation_place.'"';
			}
		}

		/** For 1 specific chapter */
		else if(isset($chapters->chapter)){
			// echo '<pre>';print_r($chapters->chapter);
			$final_arr['id'] = $chapters->chapter->id;
			$final_arr['name_arabic'] = '"'.$chapters->chapter->name_arabic.'"';
			$final_arr['name_english'] = '"'.$chapters->chapter->name_simple.'"';
			$final_arr['name_complex'] = '"'.$chapters->chapter->name_complex.'"';
			$final_arr['Bismillah_pre'] = (empty($chapters->chapter->bismillah_pre))?0: $chapters->chapter->bismillah_pre;
			$final_arr['verses'] = $chapters->chapter->verses_count;
			$final_arr['reveal_order'] = $chapters->chapter->revelation_order;
			$final_arr['reveal_place'] = '"'.$chapters->chapter->revelation_place.'"';
		}

		/** For not Data */
		else {
			$this->add_in_api_collection($final_arr);
		}
		$final_arr = array(
			'Quran_chapters'=> $final_arr,
		);
		// echo '<pre>';print_r($final_arr['Quran_chapters']);
		$this->add_in_api_collection($final_arr);
	}
	
	function getLanguages($lang='') {
		// echo 'language: '.$lang."\n";
		$languages_and_info = $this->get_languages($lang);
		$final_arr = array();
		// print_r($languages_and_info->languages);
		if(isset($languages_and_info->languages)){
			$index=0;
			// $final_arr
			foreach($languages_and_info->languages as $language) {
				// echo $index;
				$final_arr['languages'][$index]['id'] = $language->id;
				$final_arr['languages'][$index]['name'] = "'".$language->name."'";
				$final_arr['languages'][$index]['iso_code'] = "'".$language->iso_code."'";
				$final_arr['languages'][$index]['native_name'] = "'".$language->native_name."'";
				$final_arr['languages'][$index]['write_direction'] = "'".$language->direction."'";
				$index++;
			}
		}
		// print_r($final_arr);die;
		$this->add_in_api_collection($final_arr);
		
	}
	/**
	 *  IN-PROGESS -- 7:03pm Monday Jan 3 2022
	 *  BACK-TO-CONTINUE -- 10:03am Tuesday 4 2022
	 */
	function getDetailsAgainstSearchedWordInQuran($word='Allah', $size=1, $pagination_pages=1, $language='en') {
		$get_searched = $this->quran_word_search($word, $size, $pagination_pages, $language);
		$formated_1_array = array();
		// print_r($get_searched->search);
		if(isset($get_searched->search)){
			$results_index=$words_index=0;
			foreach($get_searched as $searched) {
				$formated_1_array['searched_key'] = $searched->query;
				$formated_1_array['occur_times'] = $searched->total_results;
				foreach($searched->results as $result) {
					$formated_1_array['occurance_details'][$results_index]['verse'] = $result->verse_key;
					$formated_1_array['occurance_details'][$results_index]['id'] = $result->verse_id;
					$formated_1_array['occurance_details'][$results_index]['ayat'] = $result->text;
					// print(count($result->words).' -=-> ');
					foreach($result->words as $word) {
						// print($word->char_type.' - ');
						$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text'] = $word->text;
						$words_index++; /** NOTE: Inside OBJECT, iterate The arrays - $WORD-COUNT = ++ */
					}
					$words_index=0;	/** NOTE: Back to 0, For next 'Object' */
					foreach($result->translations as $translation) {
						$formated_1_array['occurance_details'][$results_index]['translation_details']['translation'] = $translation->text;
						$formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name'] = $translation->name;
						$formated_1_array['occurance_details'][$results_index]['translation_details']['language'] = $translation->language_name;
					}
					$results_index++; /** NOTE: Inside OBJECT, iterate The arrays - $RESULTS-COUNT = ++ */
				}
			}
		}
		$searched_id_value = $this->getSearchedIdCount();
		// print $searched_id_value->id_value;die;
		$final_arr = array();
		if(!empty($formated_1_array)) {
			// print_r($formated_1_array);
			$results_index=$words_index=0;
			foreach($get_searched as $searched) {
				if (strpos($formated_1_array['searched_key'], "'") !== FALSE) {
					$formated_1_array['searched_key'] = str_replace("'", "\'", $formated_1_array['searched_key']);
				}
				$final_arr['srch_searched_keys'][] = 
						($searched_id_value->id_value+1).
					",'".$formated_1_array['searched_key']."',".
						$formated_1_array['occur_times'];
						$index_ayat_id=1;
				foreach($searched->results as $result) {
					if (strpos($formated_1_array['occurance_details'][$results_index]['ayat'], "'") !== FALSE) {
						echo "\n==========".$formated_1_array['occurance_details'][$results_index]['ayat']."========\n";
						$formated_1_array['occurance_details'][$results_index]['ayat'] = str_replace("'", "\'", $formated_1_array['occurance_details'][$results_index]['ayat']);
					}
					if (strpos($formated_1_array['occurance_details'][$results_index]['translation_details']['translation'], "'") !== FALSE) {
						$formated_1_array['occurance_details'][$results_index]['translation_details']['translation'] = str_replace("'", "\'", $formated_1_array['occurance_details'][$results_index]['translation_details']['translation']);
					}
					if (strpos($formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name'], "'") !== FALSE) {
						$formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name'] = str_replace("'", "\'", $formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name']);
					}
					$final_arr['srch_result_occur_details'][] = 
						$formated_1_array['occurance_details'][$results_index]['id'].",".
						($searched_id_value->id_value+1).
					",'".$formated_1_array['occurance_details'][$results_index]['verse']."',".
						$index_ayat_id.
					',"'.$formated_1_array['occurance_details'][$results_index]['ayat'].'"'.
					",'".$formated_1_array['occurance_details'][$results_index]['translation_details']['translation']."'".
					",'".$formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name']."'".
					",'".$formated_1_array['occurance_details'][$results_index]['translation_details']['language']."'";
					foreach($result->words as $word) {
						$final_arr['srch_word_details'][] = 
							$index_ayat_id.
						",'".$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text']."'".
						$words_index++; /** NOTE: Inside OBJECT, iterate The arrays - $WORD-COUNT = ++ */
					}
					$words_index=0;	/** NOTE: Back to 0, For next 'Object' */
					$results_index++; /** NOTE: Inside OBJECT, iterate The arrays - $RESULTS-COUNT = ++ */
					$index_ayat_id++;
				}
			}
		}
		// print_r($final_arr);
		$this->add_in_api_collection($final_arr);
	}

	function callAPI($method, $url, $header = null, $data = false)
	{
		if ($header == null) {
			$header = array("Content-Type: application/json");
		}
		$curl = curl_init();
	
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, "1");
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	//    if ($method == 'POST') {
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	//    }
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	
		// curl_setopt($curl, CURLOPT_HEADER, 1);
	
		$result = curl_exec($curl);
		$result = json_decode($result);
		// $error_msg = curl_error($curl);
		curl_close($curl);
	
		return $result;
	}
	
	function get_countries()
	{
		$url = "http://apiv3.iucnredlist.org/api/v3/country/list?token=9bb4facb6d23f48efbf424bb05c0c1ef1cf6f468393bc745d42179ac4aca5fee";
	
		$response = $this->callapi('POST', $url, null);
	
		if(!$response) {
			return false;
		}
		return $response;
	}

	function get_quran_chapters($chapter) {
		$url = "https://api.quran.com/api/v4/chapters/".$chapter;
		// echo $url;die;
		$response = $this->callAPI('GET', $url, null);
		// print_r($response);
		if(!$response) {
			return false;
		}
		return $response;
	}
	function get_languages($language='ar') { /** Defualt language set to Arabic */
		$url = "https://api.quran.com/api/v4/resources/languages?language=$language";
		// echo $url;
		$response = $this->callAPI('GET', $url, null);
		// print_r($response); /**  It shows undefined characters ; WHILE showing on web */
		if(!$response) {
			return false;
		}
		// echo urlencode("تجربة");
		return $response;
	}
	function quran_word_search($word, $size, $pagination_pages, $language) {
		$url = "https://api.quran.com/api/v4/search?q=$word&size=$size&page=$pagination_pages&language=$language";
		// echo $url;
		$response = $this->callAPI('GET', $url, null);
		// print_r($response);
		if(!$response) {
			return false;
		}
		return $response;
	}
	function getSearchedIdCount() {
		$this->load->database();

		$sql = "SELECT COUNT(*) AS id_value FROM `srch_searched_keys`";
		$value = $this->db->query($sql);
		/**
		 * NOTE: LEARN => $value => this will give a whole DB object - AVOID using THIS.
		 * NOTE: LEARN => $value->row() => Function gives 1 row from query result
		 * NOTE: LEANR -> $value->return_array() => Function gives whole Array from query result
		 */
		return($value->row());
	}

/** from m_welcomes */
	//////////////////////////////  1st  ////////////////////////////////////////////
	function add_in_api_collection($array1) {
		$data_entry_check=0;

		$insert_globals = array(
			'countries' => array(
				'keys' => '`id`,`name`,`isocode`',
				'duplicates' => '`name`= VALUES(`name`),`isocode`= VALUES(`isocode`)',
			),
			'Quran_chapters' => array(
				'keys' => '`id`, `name_arabic`, `name_english`, `name_complex`, `bismillah_pre`, `verses`, `reveal_order`, `reveal_place`',
				'duplicates' => '`name_arabic`=VALUES(`name_arabic`), `name_english`=VALUES(`name_english`), `name_complex`=VALUES(`name_complex`), `reveal_place`=VALUES(`reveal_place`)',
			),
			'languages' => array(
				'keys' => '`id`,`name`,`iso_code`,`native_name`,`write_direction`',
				'duplicates' => '`name`=VALUES(`name`),`iso_code`=VALUES(`iso_code`),`native_name`=VALUES(`native_name`),`write_direction`=VALUES(`write_direction`)',
			),
			/** PAIR Towards Below */
			'srch_searched_keys' => array(
				'keys' => '`id`,`searched_key`,`occur_times`',
				'duplicates' => '`searched_key`=VALUES(`searched_key`),`occur_times`=VALUES(`occur_times`)',
			),
			'srch_result_occur_details' => array(
				'keys' => '`id`,`searched_id`,`verse`,`ayat_id`,`ayat`,`translation`,`translator`,`language`',
				'duplicates' => '`searched_id`=VALUES(`searched_id`),`verse`=VALUES(`verse`),`ayat`=VALUES(`ayat`),`translation`=VALUES(`translation`),`translator`=VALUES(`translator`),`language`=VALUES(`language`)'
			),
			'srch_word_details' => array(
				'keys' => '`id`,`ayaat_id`,`searched_id`,`word`',
				'duplicates' => '`ayaat_id`=VALUES(`ayaat_id`),`searched_id`=VALUES(`searched_id`),`word`=VALUES(`word`)',
			),
			/** PAIR Towards Above */
		);

		/** Necessory to use DataBase  */
		$this->load->database();
		// echo '<pre>'; print($array_size);die;

		foreach ($array1 as $table_name => $data) {
		$array_size = count($data);
			// print_r($table_name);
			// echo'<pre>'; print_r($data);die;

		/** If array size is greater than 1 */
			if($array_size > 1) {
				foreach($data as $arrayIndex => $eacharray) {
					if(gettype($eacharray)=='string'){ 
						$eacharray = explode(',',$eacharray);
						// $sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (".implode(',',$eacharray) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";

					}
					// echo"\n";print_r($eacharray);echo "\n----------\n\n\n\n----------\n";
					// $sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (" . ($value['id'].','.$value['name'].','.$value['isocode']) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
					$sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (".implode(',',$eacharray) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
					// echo $sql;die; 
					$data_entry_check = $this->db->query($sql);
				}
				echo $table_name." data has successfully updated.";
			} 

		/** If array size is 1*/ 
			else if($array_size == 1){
				// echo"\n";print_r(implode($data));echo "\n----------\n\n\n\n----------\n";
				$sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (".implode(',',$data) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
				$this->db->query($sql);
				echo $table_name." data has successfully updated.";
			} 

		/** If not exists OR not stored*/
			else { echo 'Data has not stored.'; }
		}
	}

}
