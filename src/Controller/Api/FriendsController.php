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
    public function friends(?Friend $friend, FriendRepository $friendRepository, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        switch($request->getMethod()) {
            case 'DELETE':

                $params = json_decode($request->getContent(), true);
                if(!isset($params['friends'])) return $this->responseService->ReturnError(400, "Friends not found");
                $friend = $this->em->getRepository(Friend::class)->find($params['friends']);
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

        return $this->responseService->ReturnSuccess($result, ['groups' => 'user:friend']);

    }

    // #[Route('/api/friends/invitation', name: 'api.friends.invitation', methods: ['PATCH'])]
    // public function friends_invitation(Request $request): JsonResponse
    // {

    //     /** @var User */
    //     $user = $this->getUser();

    //     $parameters = json_decode($request->getContent(), true);

    //     $id = $parameters['id'];
    //     $status = $parameters['status'];

    //     $friend = $this->em->getRepository(Friend::class)->find($id);

    //     if($friend == null) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "Relationship not found",
    //             ],
    //             404,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     if($friend->getFriend() != $user && $friend->getUser() != $user) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "You're not the friend",
    //             ],
    //             400,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     switch($status) {
    //         case FriendStatus::ACCEPTED:
    //             $friend->setStatus(FriendStatus::ACCEPTED);
    //             break;
    //         case FriendStatus::DECLINED:
    //             $friend->setStatus(FriendStatus::DECLINED);
    //             break;
    //         case FriendStatus::DELETED:
    //         case FriendStatus::CANCELED:
    //             $friend->setStatus(FriendStatus::CANCELED);
    //             $this->em->remove($friend);
    //             $this->em->flush();
    //             break;
    //         default:
    //             return $this->json(
    //                 [
    //                     "status" => false,
    //                     "message" => "Status not found",
    //                 ],
    //                 404,
    //                 ['Content-Type' => "application/json"]
    //             );
    //     }

    //     if($status != FriendStatus::CANCELED && $status != FriendStatus::DELETED) {
    //         $friend->setStatusBy($user);
    //         $this->em->persist($friend);
    //         $this->em->flush();

    //         return $this->json(
    //             [
    //                 "status" => true,
    //                 "datas" => $friend,
    //             ],
    //             200,
    //             ['Content-Type' => "application/json"],
    //             ['groups' => 'friend:invite']
    //         );
    //     } else {
    //         return $this->json(
    //             [
    //                 "status" => true,
    //                 "datas" => null,
    //             ],
    //             200,
    //             ['Content-Type' => "application/json"],
    //             ['groups' => 'friend:invite']
    //         );
    //     }





    // }

    // #[Route('/api/friends/invite/{friendCode}', name: 'api.friends.invite', methods: ['GET'])]
    // public function friends_invite(string $friendCode = "", SettingService $settingService): JsonResponse
    // {

    //     /** @var User() */
    //     $me = $this->getUser();
    //     $user = $this->em->getRepository(User::class)->findOneBy(['friendCode' => $friendCode]);

    //     $friendRequest = $this->em->getRepository(Friend::class)->getRelationship($me, $user, FriendStatus::PENDING);
        
    //     if($friendRequest) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "You're already sent friend request to this person",
    //             ],
    //             400,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }
        
    //     $friend = $this->em->getRepository(Friend::class)->getRelationship($me, $user, FriendStatus::ALL);
    //     if($friend) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "You're already friend with this person",
    //             ],
    //             400,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     $allowFriend = $settingService->getSetting($user, 'allow-friend-request');

    //     if($allowFriend == null || $allowFriend == false) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "This user doesn't allow friend request",
    //             ],
    //             400,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     if($user == null) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "User not found",
    //             ],
    //             404,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     if($user == $me) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "You can't invite yourself",
    //             ],
    //             400,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     $friend = new Friend();
    //     $friend->setUser($me);
    //     $friend->setFriend($user);
    //     $friend->setStatus(FriendStatus::PENDING);
    //     $friend->setStatusBy($me);

    //     $this->em->persist($friend);
    //     $this->em->flush();

    //     return $this->json(
    //         [
    //             "status" => true,
    //             "datas" => $friend,
    //         ],
    //         200,
    //         ['Content-Type' => "application/json"],
    //         ['groups' => 'friend:invite']
    //     );

    // }

    // #[Route('/api/friends/invites', name: 'api.friends.invites', methods: ['GET'])]
    // public function friends_invites(): JsonResponse
    // {

    //     /** @var User */
    //     $user = $this->getUser();
    //     $friends = $this->em->getRepository(Friend::class)->getFriends($user, FriendStatus::PENDING);

    //     return $this->json(
    //         [
    //             "status" => true,
    //             "datas" => $friends,
    //         ],
    //         200,
    //         ['Content-Type' => "application/json"],
    //         ['groups' => 'user:friend']
    //     );

    // }

    // #[Route('/api/friends/{friend}', name: 'api.friends.find', methods: ['GET'])]
    // public function friends_find(User $friend, Request $request): JsonResponse
    // {

    //     if($friend == null) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "User not found",
    //             ],
    //             404,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }

    //     /** @var User */
    //     $user = $this->getUser();

    //     $friend = $this->em->getRepository(Friend::class)->getRelationship($user, $friend, FriendStatus::ALL);

    //     if($friend == null) {
    //         return $this->json(
    //             [
    //                 "status" => false,
    //                 "message" => "You're not friend with this person",
    //             ],
    //             404,
    //             ['Content-Type' => "application/json"]
    //         );
    //     }
        
        

    //     return $this->json(
    //         [
    //             "status" => true,
    //             "datas" => $user,
    //         ],
    //         200,
    //         ['Content-Type' => "application/json"],
    //         ['groups' => 'user:public']
    //     );

    // }

}
