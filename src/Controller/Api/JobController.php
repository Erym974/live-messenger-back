<?php

namespace App\Controller\Api;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class JobController extends AbstractController
{

    #[Route('jobs', name: 'api.jobs', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function jobs(JobRepository $jobRepository, ResponseService $responseService): JsonResponse
    {
        $jobs = $jobRepository->findAll();
        return $responseService->ReturnSuccess($jobs, ['groups' => 'jobs:read']);
    }

}
