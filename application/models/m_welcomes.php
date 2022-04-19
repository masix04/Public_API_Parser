<?php

class m_welcomes extends CI_Model {
	function __construct() {
        parent::__construct();
		// $this->load->database();
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
				'keys' => '`id`,`searched_key`,`occur_times`,`searched_at`',
				'duplicates' => '`searched_key`=VALUES(`searched_key`),`occur_times`=VALUES(`occur_times`), `searched_at`= VALUES(`searched_at`)',
			),
			'srch_result_occur_details' => array(
				'keys' => '`id`,`searched_id`,`verse`,`ayat_id`,`ayat`,`translation`,`translator`,`language`',
				'duplicates' => '`id` = VALUES(`id`),`searched_id`=VALUES(`searched_id`),`verse`=VALUES(`verse`),`ayat`=VALUES(`ayat`),`translation`=VALUES(`translation`),`translator`=VALUES(`translator`),`language`=VALUES(`language`)'
			),
			'srch_word_details' => array(
				'keys' => '`id`,`ayaat_id`,`word`',
				'duplicates' => '`id`=VALUES(`id`),`ayaat_id`=VALUES(`ayaat_id`),`word`=VALUES(`word`)',
			),
			/** PAIR Towards Above */
		);

		/** Necessory to use DataBase  */
		$this->load->database();
		// echo '<pre>'; print($array_size);

		foreach ($array1 as $table_name => $data) {
			$array_size = sizeof($data);
			// print_r($table_name);
			// echo'<pre>'; print_r($data);
			// die;
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
				echo $table_name." data has successfully updated.\n";
			} 

		/** If array size is 1*/ 
			else if($array_size == 1){
				// echo"\n";print_r(implode($data));echo "\n----------\n\n\n\n----------\n";
				$sql = "INSERT INTO `$table_name` ({$insert_globals[$table_name]['keys']}) VALUES (".implode(',',$data) . ") ON DUPLICATE KEY UPDATE {$insert_globals[$table_name]['duplicates']}";
				$this->db->query($sql);
				echo $table_name." data has successfully updated.\n";
			} 

		/** If not exists OR not stored*/
			else { echo 'Data has not stored.'; }
		}
	}

}
