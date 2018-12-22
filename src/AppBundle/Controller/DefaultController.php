<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Utils\Utils;

class DefaultController extends Controller
{

	const DEFAULT_BIO = "miguelmorcillo";

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
			"nombre" => "Miguel Morcillo",
			"headline" => "Full Stack Developer",
			"interests" => "Interested in remote opportunities, education, and jobs.",
			"location" => "Departamento del Cauca, Colombia",
			"aspirations" => [
				"Full Stack Role at Torre",
				"Animation Programmer at Rockstar Games"
			],
			"skills" => [
				"Early adopter",
				"Communication",
				"eLearning",
				"Development",
			],
			"linkedInId" => "migdress",
			"educations" => [
				[
					"title" => "Computer Science",
					"place" => "Universidad del Cauca",
					"time" => "Sep 2010 - Sep 2016"
				],
			]
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
