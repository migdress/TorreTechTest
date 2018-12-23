<?php

namespace AppBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\TorreAPI;
use AppBundle\Utils\LinkedInAPI;

/**
 * Utilities
 *
 * @author Migdress
 */
class Utils {

	/**
	 * Generate a random string, using a cryptographically secure
	 * pseudorandom number generator (random_int)
	 *
	 * For PHP 7, random_int is a PHP core function
	 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
	 *
	 * @param int $length      How many characters do we want?
	 * @param string $keyspace A string of all possible characters
	 *                         to select from
	 * @return string
	 */
	static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces [] = $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}

	static function makeHTTPRequest($url, $method, $data, $jsonFields=false, $bearer=null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$headers = [];
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		switch ($method) {
			case Request::METHOD_POST:
				curl_setopt($ch, CURLOPT_POST, 1);
				if($jsonFields){
					$data_string = json_encode($data);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Content-Length: ' . strlen($data_string);
				}else{
					$headers[] = 'Content-Type: application/x-www-form-urlencoded';
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				}
				break;
			case Request::METHOD_GET:
				break;
		}

		// Bearer if needed
		if(!is_null($bearer)){
			$headers[] = "Authorization: Bearer ".$bearer;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}


	static function mergeLinkedInAndTorreBio($linkedInInfo, $torreBioInfo){

		// Headline and location are taken from linkedIn, remaining information from TorreBio
		$merge[TorreAPI::TORREAPI_KEY_NAME] = $torreBioInfo[TorreAPI::TORREAPI_KEY_NAME];
		$merge[TorreAPI::TORREAPI_KEY_HEADLINE] = $linkedInInfo[LinkedInAPI::KEY_HEADLINE];
		$merge[TorreAPI::TORREAPI_KEY_LOCATION] = $linkedInInfo[LinkedInAPI::KEY_LOCATION];
		$merge[TorreAPI::TORREAPI_KEY_SKILLS] = $torreBioInfo[TorreAPI::TORREAPI_KEY_SKILLS];
		$merge[TorreAPI::TORREAPI_KEY_ASPIRATIONS] = $torreBioInfo[TorreAPI::TORREAPI_KEY_ASPIRATIONS];
		$merge[TorreAPI::TORREAPI_KEY_EDUCATIONS] = $torreBioInfo[TorreAPI::TORREAPI_KEY_EDUCATIONS];
		$merge[TorreAPI::TORREAPI_KEY_INTERESTS] = $torreBioInfo[TorreAPI::TORREAPI_KEY_INTERESTS];
		return $merge;
	}

}
