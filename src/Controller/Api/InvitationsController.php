<?php

namespace App\Controller\Api;


use App\Entity\Invitation;
use App\Entity\User;
use App\Service\InvitationService;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class InvitationsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService, private SerializerInterface $serializer)
    {
        
    }

    #[Route('/invitations', name: 'api.invitations.get', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function invitations_get(): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
        $invitations = $this->em->getRepository(Invitation::class)->findInvitations($user);
        return $this->responseService->ReturnSuccess($invitations, ['groups' => 'invitation:read']);

    }

    #[Route('/invitations', name: 'api.invitations.delete', methods: ['DELETE'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function invitations_delete(Request $request, InvitationService $invitationService): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
        $parameters = json_decode($request->getContent(), true);
        if(!isset($parameters['invitation'])) return $this->responseService->ReturnError(404, "Code not found");
        $invitation = $this->em->getRepository(Invitation::class)->find($parameters['invitation']);
        if($invitation == null) return $this->responseService->ReturnError(404, "Invitation not found");

        if(($invitation->getEmitter() != $user && $invitation->getReceiver() != $user) && !$user->hasRole('ROLE_ADMIN')) return $this->responseService->ReturnError(400, "You're not the emitter or the receiver");
        
        $invitationResponse = $invitationService->declineInvitation($invitation);

        if(!$invitationResponse->getStatus()) return $this->responseService->ReturnError(500, "Can't decline invitation");
        return $this->responseService->ReturnSuccess(null);

    }

    #[Route('/invitations', name: 'api.invitations.post', methods: ['POST'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function invitations_post(Request $request, InvitationService $invitationService): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
        $parameters = json_decode($request->getContent(), true);
        if(!isset($parameters['code'])) return $this->responseService->ReturnError(404, "Code not found");
    
        $code = $parameters['code'];
        /** @var User */
        $friend = $this->em->getRepository(User::class)->findOneBy(['friendCode' => $code]);

        if($friend == null) return $this->responseService->ReturnError(404, "User not found");
        if($friend === $user) return $this->responseService->ReturnError(400, "Yourself");

        if($friend->getSetting('allow-friend-request') != "true") return $this->responseService->ReturnError(400, "User doesn't allow friend request");

        /** @var Invitation */
        $invitation = $this->em->getRepository(Invitation::class)->findInvitation($user, $friend);

        if($invitation != null) {

            $friend = $invitation->getEmitter();

            if($friend === $user) return $this->responseService->ReturnError(400, "Already sent invitation");
            if($invitation->getReceiver() === $user) {

                $invitationResponse = $invitationService->acceptInvitation($invitation);

                if($invitationResponse->getStatus()) {

                    $friendEntity = $user->getFriend($friend); 

                    if($friendEntity != null) {
                        $friendEntity = [
                            "id" => $friendEntity->getId(),
                            "since" => $friendEntity->getSince()
                        ];
                    }

                    return $this->responseService->ReturnSuccess([
                        "user" => $friend,
                        "relationship" => $friendEntity,
                        "invitation" => null,
                    ], ['groups' => 'user:friend']);

                    return $this->responseService->ReturnSuccess(null, ['groups' => 'invitation:read']);
                } else {
                    return $this->responseService->ReturnError(500, "Can't accept invitation");
                }
            }
        };

        if($user->hasFriend($friend)) return $this->responseService->ReturnError(400, "Already your friend");

        $sendInviteResponse = $invitationService->sendInvitation($user, $friend);

        if(!$sendInviteResponse->getStatus()) return $this->responseService->ReturnError(500, $sendInviteResponse->getMessage());
        return $this->responseService->ReturnSuccess($sendInviteResponse->getInvitation(), ['groups' => 'invitation:read']);

    }

    #[Route('/invitations', name: 'api.invitations.patch', methods: ['PATCH'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function invitations_patch(Request $request, InvitationService $invitationService): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        $parameters = json_decode($request->getContent(), true);
        if(!isset($parameters['invitation'])) return $this->responseService->ReturnError(404, "Code not found");

        $invitation = $this->em->getRepository(Invitation::class)->find($parameters['invitation']);
        if($invitation == null) return $this->responseService->ReturnError(404, "Invitation not found");

        if(($invitation->getEmitter() != $user && $invitation->getReceiver() != $user) && !$user->hasRole('ROLE_ADMIN')) return $this->responseService->ReturnError(400, "You're not the emitter or the receiver");
        
        $friend = $invitation->getEmitter();  
        $inviteId =  $invitation->getId();

        $invitationResponse = $invitationService->acceptInvitation($invitation);

        if($invitationResponse->getStatus()) {

            $friendEntity = $user->getFriend($friend);

            if($friendEntity != null) {
                $friendData = [
                    "id" => $friendEntity->getId(),
                    "friend" => $friend,
                    "since" => $friendEntity->getSince()
                ];

            }

            return $this->responseService->ReturnSuccess($friendData, ['groups' => 'user:friend']);
        }

        return $this->responseService->ReturnError(500, $invitationResponse->getMessage());


    }

}
