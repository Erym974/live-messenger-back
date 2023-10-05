<?php

namespace App\Controller\Realtime;

use App\Entity\Group;
use App\Entity\User;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class PokesController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('api/realtime/pokes', name: 'api.realtime.pokes', methods: ['POST'])]
    public function test(Request $request): JsonResponse
    {
        
        $params = json_decode($request->getContent(), true);
        /** @var User */
        $user = $this->getUser();
        
        if(!isset($params['group'])) return $this->responseService->ReturnError(400, "Missing parameters");

        /** @var Group */
        $group = $this->em->getRepository(Group::class)->find($params['group']);
        if(!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");

        return$this->responseService->ReturnSuccess(null);
    }

}
