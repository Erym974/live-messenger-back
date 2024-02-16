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

    #[Route('api/members/{group}', name: 'api.members', methods: ['POST'])]
    public function members(?Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == 'POST') {

            $params = json_decode($request->getContent(), true);

            if($group->isPrivate()) return $this->responseService->ReturnError(403, "You can't edit a private group");

            $action = $params['action'];
            if(!$action) return $this->responseService->ReturnError(404, "Action not found");

            if(in_array($action, ['promote', 'kick', 'add'])) {
                if($group->getAdministrator() != $user) return $this->responseService->ReturnError(403, "You are not the administrator of this group");
            }

            /** Si ce n'est pas une d'action d'ajout dans le groupe */
            if($action != 'add') {
                $member = $params['member'] ?? 0;
                /** @var User */
                $member = $this->em->getRepository(User::class)->find($member);
                if(!$member) return $this->responseService->ReturnError(404, "Member not found");
                if(!$group->hasMember($member)) return $this->responseService->ReturnError(404, "Member not found in this group");
            }

            /** Si c'est pour ajouter des membres */
            if($action == 'add') {
                $members = $params['members'] ?? null;
                if(!$members) return $this->responseService->ReturnError(404, "Members not found");
                foreach($members as $member) {
                    $memberEntity = $this->em->getRepository(User::class)->find($member);
                    if(!$memberEntity) continue;
                    if($group->hasMember($member)) continue;

                    $group->addMember($memberEntity);
                }
            }

            switch($action) {
                case 'kick':
                    $group->removeMember($member);
                    break;
                case 'promote':
                    $group->setAdministrator($member);
                    break;
                default:
                    break;
            }

            $this->em->persist($group);
            $this->em->flush();
            
            $this->groupService->parseDatas($group);
            return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);

        }

        return $this->responseService->ReturnError(404, "Group not found");

    }

    #[Route('api/groups/{group?}', name: 'api.groups', methods: ['GET', 'POST'])]
    public function groups(?Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == 'POST') {

            $params = json_decode($request->getContent(), true);
            $members = $params['members'] ?? null;
            $name = $params['name'] ?? null;

            // if name is string and is is more than 10 characters remove the leading
            if($name != null && is_string($name) && strlen($name) > 10) {
                $name = substr($name, 0, 10);
            }
            
            
            if(!$members) return $this->responseService->ReturnError(404, "Members not found");
            if(count($members) < 2) return $this->responseService->ReturnError(400, "You need at least 2 members to create a group");
            
            
            $group = new Group();
            
            $group->setName($name);
            $group->addMember($user);
            $group->setAdministrator($user);
            foreach($members as $member) {
                $memberEntity = $this->em->getRepository(User::class)->find($member);
                $group->addMember($memberEntity);
            }

            if(count($group->getMembers()) < 3) return $this->responseService->ReturnError(400, "You need at least 2 members to create a group");

            $this->em->persist($group);
            $this->em->flush();

            return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);

        }

        if($request->getMethod() == 'GET' && $group) {
            if(!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");
            $this->groupService->parseDatas($group);
            return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);
        }

        $groups = $user->getGroups();
        
        foreach($groups as $group) {
            $group = $this->groupService->parseDatas($group);
        }

        // sort group by date of last message 
        $groups = $groups->toArray();
        usort($groups, function($a, $b) {
            return $a->getLastActivity() < $b->getLastActivity();
        });
        $groups = new ArrayCollection($groups);

        return $this->responseService->ReturnSuccess($groups, ['groups' => 'user:groups']);

    }

}
