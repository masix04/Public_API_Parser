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
		// echo "<pre>"; print_r($chapters);die;
		$final_arr = array();
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
		$final_arr = array(
			'Quran_chapters'=> $final_arr,
		);
		// echo '<pre>';print_r($final_arr['Quran_chapters']);
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

		if(!$response) {
			return false;
		}
		return $response;
	}
/** from m_welcomes */
	//////////////////////////////  1st  ////////////////////////////////////////////
	function add_in_api_collection($array) {
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
		);
		/** Necessory to use DataBase  */
		$this->load->database();
		// echo '<pre>'; print_r($array);
		foreach ($array as $table_name => $data) {
			// print_r($table_name);
			// echo'<pre>'; print_r($data);
			foreach($data as $arrayIndex => $eacharray) {
				// foreach($eacharray as $key => $value) {
					// echo'<pre>';print_r($eacharray);
					// $sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (" . ($value['id'].','.$value['name'].','.$value['isocode']) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
					$sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (".implode(',',$eacharray) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
					// echo $sql; 
					$data_entry_check = $this->db->query($sql);
				// }
				
			}
			if($data_entry_check!=0){
				echo $table_name." data has successfully updated.";
			}else{ echo $table_name." data has not stored.";}
		}
	}

}
