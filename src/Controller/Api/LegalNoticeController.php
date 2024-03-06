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

    #[Route('legal-notices/{type}', name: 'api.legal-notices', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function jobs(string $type, LegalNoticeRepository $legalNoticeRepository, Request $request, ResponseService $responseService): JsonResponse
    {
        $locale = $request->getLocale() ?? "fr";
        $legalNotice = $legalNoticeRepository->findOneBy(['type' => $type, 'locale' => $locale]);

        if(!$legalNotice) return $responseService->ReturnError(404, "No such legal notice");

        return $responseService->ReturnSuccess($legalNotice, ['groups' => 'legalNotice:read']);
    }

}