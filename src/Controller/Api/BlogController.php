<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('api/blog/posts', name: 'api..blog.posts', methods: ['GET'])]
    public function posts(Request $request): JsonResponse
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->responseService->ReturnSuccess($posts, ['groups' => 'posts:read']);
    }

}
