<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Utils;
use AppBundle\Utils\TorreAPI;
use AppBundle\Utils\LinkedInAPI;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

		$result = TorreAPI::getBioInfo();

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
			"name" => $result[TorreAPI::TORREAPI_KEY_NAME],
			"headline" => $result[TorreAPI::TORREAPI_KEY_HEADLINE],
			"interests" => $result[TorreAPI::TORREAPI_KEY_INTERESTS],
			"location" => $result[TorreAPI::TORREAPI_KEY_LOCATION],
			"aspirations" => $result[TorreAPI::TORREAPI_KEY_ASPIRATIONS],
			"skills" => $result[TorreAPI::TORREAPI_KEY_SKILLS],
			"linkedInId" => "migdress",
			"educations" => $result[TorreAPI::TORREAPI_KEY_EDUCATIONS]
		]);
    }

	/**
	 *
	 * @route("/linkedin/{bioId}", name="linkedInSignIn")
	 *
	 * @param request $request
	 */
	public function linkedInSignInAction(Request $request, $bioId){

		$state = Utils::random_str(12);
		LinkedInAPI::saveState($state);
		TorreAPI::saveBioId($bioId);
		$hostname = $this->getParameter("host_public_name");
		$clientId = $this->getParameter("client_id");
		$authorizationUrl = LinkedInAPI::getAuthorizationUrl($hostname, $state, $clientId);
		return $this->redirect($authorizationUrl);
	}

	/**
	 *
	 * @route("/callback", name="linkedInCallback")
	 *
	 * @param request $request
	 */
	public function linkedInCallbackAction(Request $request){

		$authCode = $request->get("code");
		$state = $request->get("state");
		$error = $request->get("error");

		if(!is_null($error)){
			return new JsonResponse([
				"code"=>Response::HTTP_EXPECTATION_FAILED,
				"msg"=>"Oops, guess you didn't want to sign in at the end?"
			]);
		}


		$savedState = LinkedInAPI::readState();

		if($state!== $savedState){
			return new JsonResponse([
				"code"=>Response::HTTP_UNAUTHORIZED,
				"msg"=>"Unauthorized"
			]);
		}

		$hostname = $this->getParameter("host_public_name");
		$clientId = $this->getParameter("client_id");
		$clientSecret = $this->getParameter("client_secret");
		$token = LinkedInAPI::requestToken($hostname, $authCode, $clientId, $clientSecret);
		$linkedInInfo = LinkedInAPI::getPerfilInfo($token);
		$bioId = TorreAPI::readBioId();
		$torreBioInfo = TorreAPI::getBioInfo($bioId);
		$merge = Utils::mergeLinkedInAndTorreBio($linkedInInfo, $torreBioInfo);

		//Update profile look, not persistent sorry :(
		return $this->render('default/index.html.twig', [
			"name" => $merge[TorreAPI::TORREAPI_KEY_NAME],
			"headline" => $merge[TorreAPI::TORREAPI_KEY_HEADLINE],
			"interests" => $merge[TorreAPI::TORREAPI_KEY_INTERESTS],
			"location" => $merge[TorreAPI::TORREAPI_KEY_LOCATION],
			"aspirations" => $merge[TorreAPI::TORREAPI_KEY_ASPIRATIONS],
			"skills" => $merge[TorreAPI::TORREAPI_KEY_SKILLS],
			"linkedInId" => "migdress",
			"educations" => $merge[TorreAPI::TORREAPI_KEY_EDUCATIONS]
		]);
	}



	/**
	 *
	 * @Route("/example", name="example")
	 * @param Request $request
	 */
	public function exampleAction(Request $request){
		$url = "https://torre.bio/api/bios/miguelandresmorcillo";
		$result = Utils::makeHTTPRequest($url, Request::METHOD_GET, null);
		return $result;
	}





}
