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

	$response = callapi('POST', $url, null);

	if(!$response) {
		return false;
	}
	return $response;
}
