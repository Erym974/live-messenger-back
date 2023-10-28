<?php

namespace App\Controller\Realtime;

use App\Entity\Group;
use App\Entity\User;
use App\Service\RealtimeService;
use App\Service\ResponseService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class EasterController extends AbstractController
{

    public function __construct(private RealtimeService $realtime, private EntityManagerInterface $em, private ResponseService $responseService, private HubInterface $hub)
    {
        
    }

    #[Route('api/realtime/easter', name: 'api.realtime.easter', methods: ['POST'])]
    public function test(Request $request): JsonResponse
    {
        
        $params = json_decode($request->getContent(), true);
        /** @var User */
        $user = $this->getUser();
        
        if(!isset($params['group'])) return $this->responseService->ReturnError(400, "Missing parameters");

        /** @var Group */
        $group = $this->em->getRepository(Group::class)->find($params['group']);
        if(!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");

        $this->realtime->publish(
            $this->realtime->getTopicsGroupUpdate("easter", $group),
            json_encode(["name" => $params['easter']]),
        );

        return$this->responseService->ReturnSuccess(null);
    }

}
