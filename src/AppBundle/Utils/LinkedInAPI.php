<?php

namespace AppBundle\Utils;



use AppBundle\Utils\Utils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of LinkedInAPI
 *
 * @author Migdress
 */
class LinkedInAPI {


	const LINKEDIN_SIGNINURL = "https://www.linkedin.com/oauth/v2/authorization";
	const LINKEDIN_REQTOKEN = "https://www.linkedin.com/oauth/v2/accessToken";
	const LINKEDIN_PERFILSPECIFIC = "https://api.linkedin.com/v1/people/~:(picture-url,headline,location)?format=json";

	// Auth
	const PARAM_RESPONSETYPE = "response_type";
	const PARAM_REDIRECTURI = "redirect_uri";
	const PARAM_CLIENTID = "client_id";
	const PARAM_STATE = "state";

	// Token
	const PARAM_GRANTTYPE = "grant_type";
	const PARAM_CODE = "code";
	const PARAM_CLIENTSECRET = "client_secret";

	// key to get token
	const KEY_ACCESS_TOKEN = "access_token";

	// specific info
	const KEY_HEADLINE = "headline";
	const KEY_PICTUREURL = "pictureUrl";
	const KEY_LOCATION = "location";
	const KEY_NAME = "name";

	public static function getAuthorizationUrl($hostname, $state, $clientId){
		$redirectUri = $hostname."/callback";
		$data = array(
			self::PARAM_RESPONSETYPE=> 'code',
			self::PARAM_REDIRECTURI => $redirectUri,
			self::PARAM_CLIENTID => $clientId,
			self::PARAM_STATE => $state,
		);
		$query = http_build_query($data);
		return self::LINKEDIN_SIGNINURL."?".$query;
	}


	static function saveState($state){
		//Sorry this is not the best, but I have no time to set database
		$fp = fopen('lastState', 'w');
		fwrite($fp, $state);
		fclose($fp);
	}

	static function readState(){
		$handle = fopen("lastState", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false && trim($line) !== "") {
				fclose($handle);
				return trim($line);
			}
		} else {
			return null;
		}
	}

	static function requestToken($hostname, $authCode, $clientId, $clientSecret){
		$redirectUri = $hostname."/callback";
		$data = array(
			self::PARAM_GRANTTYPE=> 'authorization_code',
			self::PARAM_CODE => $authCode,
			self::PARAM_REDIRECTURI => $redirectUri,
			self::PARAM_CLIENTID => $clientId,
			self::PARAM_CLIENTSECRET => $clientSecret,
		);
		$query = http_build_query($data);
		$result = Utils::makeHTTPRequest(self::LINKEDIN_REQTOKEN, Request::METHOD_POST, $query);
		$jsonResult = json_decode($result, true);
		if(isset($jsonResult[self::KEY_ACCESS_TOKEN])){
			return $jsonResult[self::KEY_ACCESS_TOKEN];
		}else{
			return null;
		}
	}


	static function getPerfilInfo($token){
		$perfilInfo = Utils::makeHTTPRequest(
				self::LINKEDIN_PERFILSPECIFIC,
				Request::METHOD_GET,
				null,
				null,
				$token);
		$response = json_decode($perfilInfo, true);
		if(is_null($response)){
			return null;
		}
		$result[self::KEY_HEADLINE] = $response[self::KEY_HEADLINE];
		$result[self::KEY_PICTUREURL] = $response[self::KEY_PICTUREURL];
		$result[self::KEY_LOCATION] = $response[self::KEY_LOCATION][self::KEY_NAME];
		return $result;
	}




}
