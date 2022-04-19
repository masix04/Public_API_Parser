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
	public function __construct() {
		parent::__construct();
		$this->load->model('m_welcomes');
	}
	public function index()
	{
		$this->load->view('welcome_message');
	}
	/** Get Countries and make an array in a format of our database table requires */
	function getCountries()
	{
		// die;
		$countries = get_countries();

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

		$this->m_welcomes->add_in_api_collection($final_arr);
	}

	/** Get Quran Chapters and About them */
	function getQuranChapters($chapter='') {
		$chapters = get_quran_chapters($chapter);
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
		$this->m_welcomes->add_in_api_collection($final_arr);
	}
	
	function getLanguages($lang='') {
		// echo 'language: '.$lang."\n";
		$languages_and_info = get_languages($lang);
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
		$this->m_welcomes->add_in_api_collection($final_arr);
		
	}
	/**
	 *  IN-PROGESS -- 7:03pm Monday Jan 3 2022
	 * 
	 *  BACK-TO-CONTINUE -- 10:03am Tuesday 4 2022
	 *  IN-PROGRESS -- 7:25pm Tuesday Jan 4 2022
	 * 
	 *  BACK-TO-CONTINUE -- 10:20am Wednesday Jan 5 2022
	 *  STOPPED -- 10:55am
	 * 
	 *  BACK-TO-CONTINUE -- 9:26pm Thursday Jan 6 2022
	 * 	STOPPED -- 10:33AM Wednesday Jan 19 2022
	 * 
	 */
	function getDetailsAgainstSearchedWordInQuran($word='Allah', $size=1, $pagination_pages=1, $language='en') {
		$get_searched = quran_word_search($word, $size, $pagination_pages, $language);
		$formated_1_array = array();
		// echo "Get Data\n";
		// echo json_encode($get_searched);
		// die;
	// } 
		// return $formated_1_array;
		// print_r($get_searched->search);
		if(isset($get_searched->search)){
			$results_index=$words_index=0;
			/** NOTE: Get Confirmation & Count of Searched.
			 *       So, Next time the id will store will be the +1 of it. 
			 */
			$searched_id_value = $this->getSearchedIdCount();

			foreach($get_searched as $searched) {
				$formated_1_array['srch_searched_keys'][]=
					"'" . $searched->query . "'," .
					$searched->total_results;
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
			print_r($formated_1_array);
			echo "\n";
		}
		$searched_id_value = $this->getSearchedIdCount();
		print '[searched_id_value -> id_value] => '.$searched_id_value->id_value."\n";
		$final_arr = array();

		if(!empty($formated_1_array)) {
			print_r($formated_1_array);
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
						if (strpos($formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text'], "'") !== FALSE) {
							$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text'] = str_replace("'", "\'", $formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text']);
						}
						$final_arr['srch_word_details'][] = 
							($words_index+1).
						",".$index_ayat_id.
						",".($searched_id_value->id_value+1).
						",'".$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text']."'";
						$words_index++; /** NOTE: Inside OBJECT, iterate The arrays - $WORD-COUNT = ++ */
					}
					$words_index=0;	/** NOTE: Back to 0, For next 'Object' */
					$results_index++; /** NOTE: Inside OBJECT, iterate The arrays - $RESULTS-COUNT = ++ */
					$index_ayat_id++;
				}
			}
		}
		// print_r($final_arr);
		$this->m_welcomes->add_in_api_collection($final_arr);
	}

	/** Improved Above Function/Method */
	function imporoved_getDetailsAgainstSearchedWordInQuran($word='Allah', $size=1, $pagination_pages=1, $language='en') {
		$get_searched = quran_word_search($word, $size, $pagination_pages, $language);
		$data_array = [];
		
		$date = date('Y-N-j h:i:s A'); 

		echo "Get Data\n";
		// echo json_encode($get_searched);
// die;
		$getDataCount = $this->getSearchedIdCount();

		if(!empty($get_searched) && $get_searched != NULL) {

			$data_array['srch_searched_keys'][] = checkEmpty($getDataCount->id_value + 1) . ", "
			. "'" . $get_searched->search->query . "',"
			. $get_searched->search->total_results . ","
			. "'" . $date . "'";

			foreach($get_searched->search->results as $ayat_key => $result) {
				// echo json_encode($result->translations[0]->text);
				$data_array['srch_result_occur_details'][] = $result->verse_id . ","
				. checkEmpty($getDataCount->id_value+1) . ","
				. "'" . $result->verse_key . "',"
				. checkEmpty($getDataCount->id_value + 1) . ($ayat_key+1) . $result->verse_id  . ","
				. "'" . $result->text . "',"
				. "'" . $result->translations[0]->text . "'," 
				. "'" . $result->translations[0]->name . "',"
				. "'" . $result->translations[0]->language_name . "'";

				foreach($result->words as $word_key => $word) {
					$data_array['srch_word_details'][] = ($word_key+1) . ","
					. checkEmpty($getDataCount->id_value + 1) . ($ayat_key+1) . $result->verse_id  . ","
					. "'" . $word->text . "'" ;
				}
			}
		}
		print_r($data_array);
		// die;
		$this->m_welcomes->add_in_api_collection($data_array);
	}

	function getSearchedIdCount() {
		$this->load->database();

		$sql = "SELECT COUNT(*) AS id_value FROM `srch_searched_keys`";
		$value = $this->db->query($sql);
		print_r($value->row());

		/**
		 * NOTE: LEARN => $value => this will give a whole DB object - AVOID using THIS.
		 * NOTE: LEARN => $value->row() => Function gives 1 row from query result
		 * NOTE: LEANR -> $value->return_array() => Function gives whole Array from query result
		 */
		return($value->row());
	}

}

/*
***********************************************
For Saving Work.

***********************************************
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
						if (strpos($formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text'], "'") !== FALSE) {
							$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text'] = str_replace("'", "\'", $formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text']);
						}
						$final_arr['srch_word_details'][] = 
							($words_index+1).
						",".$index_ayat_id.
						",".($searched_id_value->id_value+1).
						",'".$formated_1_array['occurance_details'][$results_index]['word_list'][$words_index]['text']."'";
						$words_index++; /** NOTE: Inside OBJECT, iterate The arrays - $WORD-COUNT = ++ */
						/*
					}
					$words_index=0;	/** NOTE: Back to 0, For next 'Object' *//*
					$results_index++; /** NOTE: Inside OBJECT, iterate The arrays - $RESULTS-COUNT = ++ *//*
					$index_ayat_id++;
				}
			}
		}

*/
/*

*********************************
First Function	 is Saving

********************************
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
			/*}
			$words_index=0;	/** NOTE: Back to 0, For next 'Object' */
			/*foreach($result->translations as $translation) {
				$formated_1_array['occurance_details'][$results_index]['translation_details']['translation'] = $translation->text;
				$formated_1_array['occurance_details'][$results_index]['translation_details']['translator_name'] = $translation->name;
				$formated_1_array['occurance_details'][$results_index]['translation_details']['language'] = $translation->language_name;
			}
			$results_index++; /** NOTE: Inside OBJECT, iterate The arrays - $RESULTS-COUNT = ++ */
		/*}
	}
}
*/
