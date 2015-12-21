<?php

function curlGet($link, $timeOut = 3)
{
	$curl = curl_init();
	curl_setopt_array($curl, array(
			CURLOPT_URL => $link,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => $timeOut,
			//CURLOPT_FILETIME => false,
			CURLOPT_USERAGENT => 'iPhone',
	));
	$result =  curl_exec($curl);
	curl_close($curl);
	return $result;
}