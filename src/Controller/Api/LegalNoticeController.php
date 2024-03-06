<?php

namespace App\Controller\Api;

use App\Entity\LegalNotice;
use App\Repository\JobRepository;
use App\Repository\LegalNoticeRepository;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LegalNoticeController extends AbstractController
{

    public function __construct(private ResponseService $responseService)
    {
        
    }

    #[Route('api/legal-notices/{type}', name: 'api.legal-notices', methods: ['GET'])]
    public function jobs(string $type, LegalNoticeRepository $legalNoticeRepository, Request $request): JsonResponse
    {
        $locale = $request->getLocale() ?? "fr";
        $legalNotice = $legalNoticeRepository->findOneBy(['type' => $type, 'locale' => $locale]);

        if(!$legalNotice) return $this->responseService->ReturnError(404, "No such legal notice");

        return $this->responseService->ReturnSuccess($legalNotice, ['groups' => 'legalNotice:read']);
    }

}