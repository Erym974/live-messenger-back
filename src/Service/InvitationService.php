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
            $emitter = $invitation->getEmitter();
            $receiver = $invitation->getReceiver();
            
            
            /** @var Friend */
            $friend = $this->createFriendEntity($emitter, $receiver);
            $emitter->addFriend($friend);
            /** @var Friend */
            $friend2 = $this->createFriendEntity($receiver, $emitter);
            $receiver->addFriend($friend2);

            /** @var ?Group */
            $group = null;

            foreach($emitter->getGroups() as $grp) {
                if($grp && $grp->hasMember($receiver) && $grp->getMembers()->count() == 2) {
                    $group = $grp;
                    break;
                }
            }

            
            if(!$group) $group = GroupService::createGroup(null, [$emitter, $receiver]);
            
            $this->em->persist($group);
            $friend->setConversation($group);
            $friend2->setConversation($group);

            $this->removeInvitation($invitation);
            $this->em->persist($friend);
            $this->em->persist($friend2);
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