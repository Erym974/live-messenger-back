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

#[IsGranted('JWT_HEADER_ACCESS')]
class FriendsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('/api/friends/{friend?}', name: 'api.friends', methods: ['GET', 'DELETE'])]
    public function friends(?Friend $friend, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        switch($request->getMethod()) {
            case 'DELETE':

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

                break;
            case 'GET':
                
                if($friend) {
                    if($friend->hasUser($user)) return  $this->responseService->ReturnError(400, "You're not the friend");
                    $result = $friend;
                } else {
                    $result = $user->getFriends();
                }

                break;
        }

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
