<?php

namespace App\Service;

use App\Entity\Friend;
use App\Entity\Group;
use App\Entity\Invitation;
use App\Entity\User;
use App\Service\AbstractService;

class InvitationServiceResponse {

    public function __construct(
        public bool $status,
        public string $message,
        public ?Invitation $invitation
    ) {}

    public function getStatus(): bool {return $this->status;}
    public function getMessage(): string {return $this->message;}
    public function getInvitation(): Invitation {return $this->invitation;}

}

class InvitationService extends AbstractService {

    public function sendInvitation(User $emitter, User $receiver) : ?InvitationServiceResponse
    {
        $invitation = $this->em->getRepository(Invitation::class)->findInvitation($emitter, $receiver);

        if($invitation) return new InvitationServiceResponse(true, "Invitation already sent", null);

        $invitation = new Invitation();
        $invitation->setEmitter($emitter);
        $invitation->setReceiver($receiver);

        $this->em->persist($invitation);
        $this->em->flush();

        return new InvitationServiceResponse(true, "Invitation sent", $invitation);
    }

    public function acceptInvitation(Invitation $invitation) : InvitationServiceResponse
    {

        
        try {
            $emitter = $invitation->getEmitter();
            $receiver = $invitation->getReceiver();
            
            /** @var Friend */
            $friend_emitter = $this->createFriendEntity($emitter, $receiver);
            $emitter->addFriend($friend_emitter);

            /** @var Friend */
            $friend_receiver = $this->createFriendEntity($receiver, $emitter);
            $receiver->addFriend($friend_receiver);

            /** @var ?Group */
            $group = null;

            $group = $this->em->getRepository(Group::class)->findPrivateGroup($emitter, $receiver);

            
            if(!$group) {
                $group = new Group();
                $group->addMember($emitter);
                $group->addMember($receiver);
                $group->setAdministrator($emitter);
                $group->setPrivate(true);
                $this->em->persist($group);
            }

            $friend_emitter->setConversation($group);
            $friend_receiver->setConversation($group);

            $this->removeInvitation($invitation);
            $this->em->persist($friend_emitter);
            $this->em->persist($friend_receiver);
            $this->em->persist($emitter);
            $this->em->persist($receiver);
            $this->em->flush();

            return new InvitationServiceResponse(true, "Accepted", null);
        } catch (\Throwable $th) {
            return new InvitationServiceResponse(false, "Can't accept invitation", null);
        }

    }

    public function declineInvitation(Invitation $invitation) : InvitationServiceResponse
    {
        return $this->removeInvitation($invitation);
    }

    private function removeInvitation(Invitation $invitation) : InvitationServiceResponse
    {
        try {
            $this->em->remove($invitation);
            $this->em->flush();
            return new InvitationServiceResponse(true, "Invitation removed successfully", null);
        } catch (\Throwable $th) {
            return new InvitationServiceResponse(false, $th->getMessage(), null);
        }
    }

    private function createFriendEntity(User $user, User $user2, bool $save = false) : Friend
    {
        $friend = new Friend();
        $friend->setUser($user);
        $friend->setFriend($user2);
        if($save) {
            $this->em->persist($friend);
            $this->em->flush();
        }
        return $friend;
    }

}