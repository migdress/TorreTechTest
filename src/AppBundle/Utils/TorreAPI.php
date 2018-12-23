<?php

namespace AppBundle\Utils;



use AppBundle\Utils\Utils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of TorreAPI
 *
 * @author Migdress
 */
class TorreAPI {

	const DEFAULT_BIO = "miguelmorcillo";
	const TORREAPI_BIOINFO = "https://torre.bio/api/bios/:bioId";
	const TORREAPI_PEOPLEINFO = "https://torre.bio/api/people/:bioId";


	const TORREAPI_KEY_PERSON = "person";
	const TORREAPI_KEY_NAME = "name";
	const TORREAPI_KEY_HEADLINE = "professionalHeadline";
	const TORREAPI_KEY_LOCATION = "location";
	const TORREAPI_KEY_SKILLS = "strengths";
	const TORREAPI_KEY_ASPIRATIONS = "aspirations";
	const TORREAPI_KEY_EDUCATIONS = "education";
	const TORREAPI_KEY_INTERESTS = "opportunities";

	public static function getBioInfo($bioId=null){

		if(is_null($bioId)){
			$url = str_replace(":bioId", self::DEFAULT_BIO, self::TORREAPI_BIOINFO);
		}else{
			$url = str_replace(":bioId", $bioId, self::TORREAPI_BIOINFO);
		}

		$result = Utils::makeHTTPRequest($url, Request::METHOD_GET, null);

		if(is_null($result)){
			return new Response("Oops, something went wrong consuming Torre API", Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		$jsonResult = json_decode($result, true);

		$response[self::TORREAPI_KEY_NAME] = $jsonResult[self::TORREAPI_KEY_PERSON][self::TORREAPI_KEY_NAME];
		$response[self::TORREAPI_KEY_HEADLINE] = $jsonResult[self::TORREAPI_KEY_PERSON][self::TORREAPI_KEY_HEADLINE];
		$response[self::TORREAPI_KEY_LOCATION] = $jsonResult[self::TORREAPI_KEY_PERSON][self::TORREAPI_KEY_LOCATION];

		if(isset($jsonResult[self::TORREAPI_KEY_SKILLS])){
			foreach($jsonResult[self::TORREAPI_KEY_SKILLS] as $skill){
				$response[self::TORREAPI_KEY_SKILLS][] = $skill[self::TORREAPI_KEY_NAME];
			}
		}

		if(isset($jsonResult[self::TORREAPI_KEY_ASPIRATIONS])){
			foreach($jsonResult[self::TORREAPI_KEY_ASPIRATIONS] as $aspiration){
				$response[self::TORREAPI_KEY_ASPIRATIONS][] = $aspiration[self::TORREAPI_KEY_NAME];
			}
		}

		if(isset($jsonResult[self::TORREAPI_KEY_EDUCATIONS])){
			foreach($jsonResult[self::TORREAPI_KEY_EDUCATIONS] as $education){
				$response[self::TORREAPI_KEY_EDUCATIONS][] = $education[self::TORREAPI_KEY_NAME];
			}
		}

		if(isset($jsonResult[self::TORREAPI_KEY_INTERESTS])){
			$interests = "Interested in ";
			$interestsLen = count($jsonResult[self::TORREAPI_KEY_INTERESTS]);
			for($i=0;$i<$interestsLen;$i++){
				if($i==$interestsLen-1){
					$interests.="and ".$jsonResult[self::TORREAPI_KEY_INTERESTS][$i][self::TORREAPI_KEY_NAME];
				}else{
					$interests.=$jsonResult[self::TORREAPI_KEY_INTERESTS][$i][self::TORREAPI_KEY_NAME].", ";
				}
			}
			$response[self::TORREAPI_KEY_INTERESTS] = $interests;
		}
		return $response;
	}


	public static function getPeopleInfo($bioId=null){

		if(is_null($bioId)){
			$url = str_replace(":bioId", $bioId, self::TORREAPI_PEOPLEINFO);
		}else{
			$url = str_replace(":bioId", self::DEFAULT_BIO, self::TORREAPI_PEOPLEINFO);
		}
		$result = Utils::makeHTTPRequest($url, Request::METHOD_GET, null);
		if(is_null($result)){
			return new Response("Oops, something went wrong consuming Torre API", Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		$jsonResult = json_decode($result);
		return json_decode($jsonResult, true);
	}


	static function saveBioId($bioId){
		//Sorry this is not the best, but I have no time to set database
		$fp = fopen('bioId', 'w');
		fwrite($fp, $bioId);
		fclose($fp);
	}

	static function readBioId(){
		$handle = fopen("bioId", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false && trim($line) !== "") {
				fclose($handle);
				return trim($line);
			}
		} else {
			return null;
		}
	}


	//put your code here
}
