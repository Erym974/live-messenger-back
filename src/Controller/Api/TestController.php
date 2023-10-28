<?php

namespace App\Controller\Api;

use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TestController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('api/test', name: 'api.test', methods: ['GET', 'POST', 'PATCH', 'DELETE'])]
    public function test(Request $request): JsonResponse
    {
        $method = $request->getMethod();
        return $this->responseService->ReturnSuccess("Method $method works");
    }

}
