<?php

namespace App\Service;

use App\Entity\Friend;
use App\Entity\Group;
use App\Entity\Invitation;
use App\Entity\User;
use App\Service\AbstractService;

class InvitationService extends AbstractService {

    public function sendInvitation(User $emitter, User $receiver) : ?Invitation
    {
        $invitation = $this->em->getRepository(Invitation::class)->findInvitation($emitter, $receiver);
        if($invitation) return null;

        $invitation = new Invitation();
        $invitation->setEmitter($emitter);
        $invitation->setReceiver($receiver);

        $this->em->persist($invitation);
        $this->em->flush();

        return $invitation;
    }

    public function acceptInvitation(Invitation $invitation) : bool
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

            return true;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return false;
        }

    }

    public function declineInvitation(Invitation $invitation) : bool
    {
        return $this->removeInvitation($invitation);
    }

    private function removeInvitation(Invitation $invitation) : bool
    {
        try {
            $this->em->remove($invitation);
            $this->em->flush();
            return true;
        } catch (\Throwable $th) {
            return false;
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