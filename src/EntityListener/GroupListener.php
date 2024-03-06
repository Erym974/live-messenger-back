<?php

namespace App\EntityListener;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GroupListener
{

    public function prePersist(Group $group)
    {
        $group->setCreatedAt(new \DateTimeImmutable());
        $group->setLastActivity(new \DateTimeImmutable());
    }

    public function preUpdate(Group $group) {
        $group->setLastActivity(new \DateTimeImmutable());
    }


}