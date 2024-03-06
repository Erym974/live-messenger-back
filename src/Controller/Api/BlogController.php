<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{

    public function __construct(private ResponseService $responseService)
    {
        
    }

    #[Route('api/blog/posts', name: 'api..blog.posts', methods: ['GET'])]
    public function posts(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();
        return $this->responseService->ReturnSuccess($posts, ['groups' => 'posts:read']);
    }

}
