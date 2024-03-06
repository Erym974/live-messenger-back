<?php

namespace App\Controller\Api;

use App\DTO\GroupDTO;
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

class GroupsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private GroupService $groupService, private ResponseService $responseService)
    {
    }

    /** Modification des membres */
    #[Route('members/{group}', name: 'api.members', methods: ['POST'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function members(Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);

        if ($group->isPrivate()) return $this->responseService->ReturnError(403, "You can't edit a private group");

        $action = $params['action'];
        if (!$action) return $this->responseService->ReturnError(404, "Action not found");

        if (in_array($action, ['promote', 'kick', 'add', 'delete'])) {
            if ($group->getAdministrator() != $user) return $this->responseService->ReturnError(403, "You are not the administrator of this group");
        }

        /** Si ce n'est pas une d'action d'ajout dans le groupe */
        if ($action != 'add' && $action != "delete" && $action != "leave") {
            $member = $params['member'] ?? 0;
            /** @var User */
            $member = $this->em->getRepository(User::class)->find($member);
            if (!$member) return $this->responseService->ReturnError(404, "Member not found");
            if (!$group->hasMember($member)) return $this->responseService->ReturnError(404, "Member not found in this group");
        }

        /** Si c'est pour ajouter des membres */
        if ($action == 'add') {
            $members = $params['members'] ?? null;
            if (!$members) return $this->responseService->ReturnError(404, "Members not found");
            foreach ($members as $member) {
                $memberEntity = $this->em->getRepository(User::class)->find($member);
                if (!$memberEntity) continue;
                if ($group->hasMember($memberEntity)) continue;

                $group->addMember($memberEntity);
            }
        }

        /** Si c'est pour quitter le groupe */
        if ($action == 'leave') {
            if (!$group->hasMember($user)) return $this->responseService->ReturnError(404, "You are not a member of this group");
        }

        switch ($action) {
            case 'kick':
                $group->removeMember($member);
                break;
            case 'promote':
                $group->setAdministrator($member);
                break;
            case 'leave':
                $group->removeMember($user);
                break;
            case 'delete':
                foreach ($group->getMembers() as $member) {
                    $group->removeMember($member);
                }
                break;
            default:
                break;
        }

        $this->em->persist($group);
        $this->em->flush();

        $this->groupService->parseDatas($group);
        return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);


        return $this->responseService->ReturnError(404, "Group not found");
    }

    /** Création d'un groupe */
    #[Route('groups', name: 'api.groups.post', methods: ['POST'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function groups_post(Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);
        $members = $params['members'] ?? null;
        $name = $params['name'] ?? null;

        if ($name != null && is_string($name) && strlen($name) > 10) $name = substr($name, 0, 10);

        if (!$members) return $this->responseService->ReturnError(404, "Members not found");
        if (count($members) < 2) return $this->responseService->ReturnError(400, "You need at least 2 members to create a group");

        $group = new Group();

        $group->setName($name);
        $group->addMember($user);
        $group->setAdministrator($user);
        foreach ($members as $member) {
            $memberEntity = $this->em->getRepository(User::class)->find($member);
            $group->addMember($memberEntity);
        }

        if (count($group->getMembers()) < 3) return $this->responseService->ReturnError(400, "You need at least 2 members to create a group");

        // $this->em->persist($group);
        // $this->em->flush();

        return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);
    }


    /** Récupération de tout les groupes */
    #[Route('groups', name: 'api.groups.get', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function groups_get(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $groups = $user->getGroups()->toArray();

        usort($groups, function ($a, $b) {
            return $a->getLastActivity() < $b->getLastActivity();
        });

        foreach($groups as $group) {
            $group = $this->groupService->parseDatas($group);
        }

        $groups = new ArrayCollection($groups);

        return $this->responseService->ReturnSuccess($groups, ['groups' => 'user:groups']);
    }

    /** Récupération d'un groupe */
    #[Route('groups/{group}', name: 'api.group.get', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function group_get(Group $group, Request $request): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        if (!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");
        $this->groupService->parseDatas($group);
        return $this->responseService->ReturnSuccess($group, ['groups' => 'group:read']);
    }

}
