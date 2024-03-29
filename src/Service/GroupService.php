<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\User;
use App\Service\AbstractService;

class GroupService extends AbstractService {

    public function parseDatas(Group $group, ?User $user = null) : Group
    {

        /** @var User */
        if(!$user) $user = $this->getUser();

        if($group->getName() == null){
            if($group->getMembers()->count() > 2) {
                $index = 0;
                if($user == $group->getMembers()[$index]) $index = 1;
                $group->setName($group->getMembers()[$index]->getFullname() . " and " . ($group->getMembers()->count() - 2 ) . ' ' . ((($group->getMembers()->count() - 2) === 1 ) ? "other" : "others"));
                if($group->getPicture() === null) $group->setTempPicture("https://ui-avatars.com/api/?name=" . str_replace(' ', '+', $group->getMembers()[$index]->getFullname()) . "");
            } else {
                if($user === $group->getMembers()[0]) {
                    $group->setName($group->getMembers()[1]->getFullname());
                    if($group->getPicture() === null) $group->setPicture($group->getMembers()[1]->getProfilePicture(true));
                } else {
                    $group->setName($group->getMembers()[0]->getFullname());
                    if($group->getPicture() === null) $group->setPicture($group->getMembers()[0]->getProfilePicture(true));
                }
            }
        } else {
            if($group->getPicture() === null) $group->setTempPicture("https://ui-avatars.com/api/?name=" . str_replace(' ', '+', $group->getName()[0]) . "");
        }
        return $group;
    }

}