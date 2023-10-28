<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Entity\User;
use App\Service\GroupService;
use App\Service\ResponseService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[isGranted('JWT_HEADER_ACCESS')]
class GroupsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private GroupService $groupService, private ResponseService $responseService)
    {
        
    }

    #[Route('api/groups/{group?}', name: 'api.groups', methods: ['GET', 'POST'])]
    public function groups(?Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == 'GET' && $group) {
            if(!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");
            $this->groupService->parseDatas($group);
            return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);
        }

        if($request->getMethod() == 'POST') {

            $parameters = json_decode($request->getContent(), true);
            $member = $this->em->getRepository(User::class)->find($parameters['member']);

            if(!$member) return $this->responseService->ReturnError(404, "Member not found");

            foreach ($user->getGroups() as $group) {
                if(count($group->getMembers()) === 2) {
                    if($group->hasMember($member) && $group->hasMember($user)) return $this->responseService->ReturnError(400, "You are already in a private group with this user");
                }
            }

            $group = new Group();
            
            if(array_key_exists('name', $parameters)) {
                $group->setName($parameters['name']);
            }
            $group->addMember($user);
            $group->addMember($member);

            $this->em->persist($group);
            $this->em->flush();

            return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);

        }

        $groups = $user->getGroups();
        
        foreach($groups as $group) {
            $group = $this->groupService->parseDatas($group);
        }

        // sort group by date of last message 
        $groups = $groups->toArray();
        usort($groups, function($a, $b) {
            return $a->getLastMessage()->getSendedAt() < $b->getLastMessage()->getSendedAt();
        });
        $groups = new ArrayCollection($groups);

        return $this->responseService->ReturnSuccess($groups, ['groups' => 'user:groups']);

    }

}
