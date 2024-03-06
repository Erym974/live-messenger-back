<?php

namespace App\Controller\Api;

use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{

    public function __construct(private ResponseService $responseService)
    {
        
    }

    #[Route('/', name: 'api.home', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function jobs(): JsonResponse
    {
        return $this->responseService->ReturnSuccess(["API" => "Connected"]);
    }

}
