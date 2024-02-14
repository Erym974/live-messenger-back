<?php

namespace App\Controller\Api;


use App\Entity\Invitation;
use App\Entity\User;
use App\Service\InvitationService;
use App\Service\InvitationsStatus;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('JWT_HEADER_ACCESS')]
class InvitationsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService, private SerializerInterface $serializer)
    {
        
    }

    #[Route('/api/invitations', name: 'api.invitations', methods: ['GET', 'POST', 'DELETE', 'PATCH'])]
    public function invitations(?Invitation $invitation, Request $request, InvitationService $invitationService): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == 'POST') {

            $parameters = json_decode($request->getContent(), true);
            if(!isset($parameters['code'])) return $this->responseService->ReturnError(404, "Code not found");
        
            $code = $parameters['code'];

            $friend = $this->em->getRepository(User::class)->findOneBy(['friendCode' => $code]);

            if($friend == null) return $this->responseService->ReturnError(404, "User not found");

            if($friend === $user) return $this->responseService->ReturnError(400, "Yourself");

            /** @var Invitation */
            $invitation = $this->em->getRepository(Invitation::class)->findInvitation($user, $friend);

            if($invitation != null) {
                $friend = $invitation->getEmitter();
                if($friend === $user) return $this->responseService->ReturnError(400, "Already sent invitation");
                if($invitation->getReceiver() === $user) {
                    if($invitationService->acceptInvitation($invitation)) {

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

            $invitation = $invitationService->sendInvitation($user, $friend);
            if(!$invitation) return $this->responseService->ReturnError(500, "Can't send invitation");

            return $this->responseService->ReturnSuccess($invitation, ['groups' => 'invitation:read']);

        }

        if($request->getMethod() == 'DELETE') {

            $parameters = json_decode($request->getContent(), true);
            if(!isset($parameters['invitation'])) return $this->responseService->ReturnError(404, "Code not found");

            $invitation = $this->em->getRepository(Invitation::class)->find($parameters['invitation']);
            if($invitation == null) return $this->responseService->ReturnError(404, "Invitation not found");

            if(($invitation->getEmitter() != $user && $invitation->getReceiver() != $user) && !$user->hasRole('ROLE_ADMIN')) return $this->responseService->ReturnError(400, "You're not the emitter or the receiver");
            
            $invitationService->declineInvitation($invitation);

            return $this->responseService->ReturnSuccess(null);
        }

        if($request->getMethod() == 'PATCH') {

            $parameters = json_decode($request->getContent(), true);
            if(!isset($parameters['invitation'])) return $this->responseService->ReturnError(404, "Code not found");

            $invitation = $this->em->getRepository(Invitation::class)->find($parameters['invitation']);
            if($invitation == null) return $this->responseService->ReturnError(404, "Invitation not found");

            if(($invitation->getEmitter() != $user && $invitation->getReceiver() != $user) && !$user->hasRole('ROLE_ADMIN')) return $this->responseService->ReturnError(400, "You're not the emitter or the receiver");
            
            $friend = $invitation->getEmitter();  
            $inviteId =  $invitation->getId();

            if($invitationService->acceptInvitation($invitation)) {

                $friendEntity = $user->getFriend($friend);

                if($friendEntity != null) {
                    $friendData = [
                        "id" => $friendEntity->getId(),
                        "friend" => $friend,
                        "since" => $friendEntity->getSince()
                    ];

                    $userData = [
                        "id" => $friendEntity->getId(),
                        "invitation" => $inviteId,
                        "friend" => $user,
                        "since" => $friendEntity->getSince()
                    ];
                }

                return $this->responseService->ReturnSuccess($friendData, ['groups' => 'user:friend']);
            } else {
                return $this->responseService->ReturnError(500, "Can't accept invitation");
            }
        }

        $invitations = $this->em->getRepository(Invitation::class)->findInvitations($user);

        return $this->responseService->ReturnSuccess($invitations, ['groups' => 'invitation:read']);

    }

}
