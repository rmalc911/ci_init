<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function file_path_exists($filename) {
	if (file_exists($filename)) {
		return true;
	}
	if (file_exists(FCPATH . $filename)) {
		return true;
	}
	return URL_exists($filename);
}

function URL_exists($url) {
	$context = stream_context_create([
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
		],
	]);
	$headers = get_headers($url, 0, $context);
	return stripos($headers[0], "200 OK") ? true : false;
}
