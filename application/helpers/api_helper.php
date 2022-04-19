<?php

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

	$response = callAPI('POST', $url, null);

	if(!$response) {
		return false;
	}
	return $response;
}

function get_quran_chapters($chapter) {
	$url = "https://api.quran.com/api/v4/chapters/".$chapter;
	// echo $url;die;
	$response = callAPI('GET', $url, null);
	// print_r($response);
	if(!$response) {
		return false;
	}
	return $response;
}
function get_languages($language='ar') { /** Defualt language set to Arabic */
	$url = "https://api.quran.com/api/v4/resources/languages?language=$language";
	// echo $url;
	$response = callAPI('GET', $url, null);
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
	$response = callAPI('GET', $url, null);
	// print_r($response);
	if(!$response) {
		return false;
	}
	return $response;
}
