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

    public function __construct(private ResponseService $responseService)
    {
        
    }

    #[Route('api/jobs', name: 'api.jobs', methods: ['GET'])]
    public function jobs(JobRepository $jobRepository): JsonResponse
    {
        $jobs = $jobRepository->findAll();
        return $this->responseService->ReturnSuccess($jobs, ['groups' => 'jobs:read']);
    }

}
