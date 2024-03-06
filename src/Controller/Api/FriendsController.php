<?php

namespace App\Controller\Api;

use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class FriendsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('/api/friends/{friend?}', name: 'api.friend.delete', methods: ['DELETE'])]
    public function friend_delete(?Friend $friend, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        /** Get All datas */
        $params = json_decode($request->getContent(), true);
        if(!isset($params['friend'])) return $this->responseService->ReturnError(400, "Friends not found");

        $friend = $this->em->getRepository(Friend::class)->find($params['friend']);
        if($friend == null) return $this->responseService->ReturnError(404, "Friend not found");

        if($friend->getUser() != $user) return $this->responseService->ReturnError(400, "You're not the friend");

        /** @var User */
        $other = $friend->getUser() === $user ? $friend->getFriend() : $friend->getUser();

        $otherFriend = $other->getFriend($user);

        if($otherFriend == null) return $this->responseService->ReturnError(404, "Friend not found");

        $this->em->remove($otherFriend);
        $this->em->remove($friend);
        $this->em->flush();

        return $this->responseService->ReturnSuccess(null);

    }

    #[Route('/api/friends/{friend}', name: 'api.friend.get', methods: ['GET'])]
    public function friend_get(?Friend $friend): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
                
        if(!$friend->hasUser($user)) return  $this->responseService->ReturnError(400, "You're not the friend");

        $mutual = $this->em->getRepository(Friend::class)->getMutualFriends($user, $friend->getFriend());
        $mutual = array_map(function($friend) {
            return [
                'id' => $friend->getFriend()->getId(),
                'fullname' => $friend->getFriend()->getFullname(),
                'profilePicture' => $friend->getFriend()->getProfilePicture()
            ];
        }, $mutual);
        $friend->setMutual($mutual);

        return $this->responseService->ReturnSuccess($friend, ['groups' => 'user:friend']);

    }

    #[Route('/api/friends', name: 'api.friends.get', methods: ['GET'])]
    public function friends_get(): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $result = $user->getFriends();

        $result = array_map(function($friend) use ($user) {
            $mutual = $this->em->getRepository(Friend::class)->getMutualFriends($user, $friend->getFriend());
            $mutual = array_map(function($friend) {
                return [
                    'id' => $friend->getFriend()->getId(),
                    'fullname' => $friend->getFriend()->getFullname(),
                    'profilePicture' => $friend->getFriend()->getProfilePicture()
                ];
            }, $mutual);
            $friend->setMutual($mutual);
            return $friend;
        }, $result->toArray());

        return $this->responseService->ReturnSuccess($result, ['groups' => 'user:friend']);

    }



}
