<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{

    #[Route('blog/posts', name: 'api..blog.posts', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function posts(PostRepository $postRepository, ResponseService $responseService): JsonResponse
    {
        $posts = $postRepository->findAllPosts();
        return $responseService->ReturnSuccess($posts, ['groups' => 'posts:read']);
    }

}
