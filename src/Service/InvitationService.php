<?php

namespace App\Service;

use App\Entity\Friend;
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
            $user = $invitation->getEmitter();
            $user2 = $invitation->getReceiver();
    
            $user->addFriend($this->createFriendEntity($user, $user2, true));
            $user2->addFriend($this->createFriendEntity($user2, $user, true));

            $alreadyGroup = false;

            foreach($user->getGroups() as $group) {
                if($group->hasMember($user2) && $group->getMembers()->count() == 2) {
                    $alreadyGroup = true;
                    break;
                }
            }

            if(!$alreadyGroup) $group = GroupService::createGroup(null, [$user, $user2]);

            $this->removeInvitation($invitation);
            if($group) $this->em->persist($group);
            $this->em->persist($user);
            $this->em->persist($user2);
            $this->em->flush();

            return true;
        } catch (\Throwable $th) {
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

    private function createFriendEntity(User $user, User $user2, bool $save) : Friend
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