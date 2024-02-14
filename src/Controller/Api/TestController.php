<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Entity\User;
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
        $user = $this->getUser();
        $user2 = $this->em->getRepository(User::class)->find(2);
        $group = $this->em->getRepository(Group::class)->findPrivateGroup($user, $user2);
        dd($group);
    }

}
