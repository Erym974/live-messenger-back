<?php

namespace App\EntityListener;

use App\Entity\Meta;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{

    public function __construct(private UserPasswordHasherInterface $hasher, private EntityManagerInterface $em)
    {
        
    }

    public function prePersist(User $user)
    {

        $this->createSetting($user);

        if($user->getFriendCode() == null) $user->setFriendCode($this->generateFriendCode());
        $this->encodePassword($user);
    }

    public function preUpdate(User $user) {
        if($user->getFriendCode() == null) $user->setFriendCode($this->generateFriendCode());
        $this->encodePassword($user);
    }

    
    public function encodePassword(User $user) {
        if($user->getPlainPassword() == null) return;
        
        $user->setPassword($this->hasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));
    }

    private function generateFriendCode() : string
    {
        $timestamp = str_split((string) time(), 5);
        $random = rand(11111, 99999);
        $code = $timestamp[0] . "-" . $timestamp[1] . "-" . $random;

        while($this->em->getRepository(User::class)->findOneBy(['friendCode' => $code]) != null) {
            $random = rand(11111, 99999);
            $code = $timestamp[0] . "-" . $timestamp[1] . "-" . $random;
        }
        return $code;
    }

    private function createSetting(User $user) {
        $settings = [
            "allow-friend-request", 
            "language"
        ];

        foreach($settings as $meta) {
            $meta = $this->em->getRepository(Meta::class)->findOneBy(['name' => $meta]);
            if(!$meta) continue;
            $setting = new Setting();
            $setting->setUser($user);
            $setting->setMeta($meta);
            $setting->setValue($meta->getValue());
            $this->em->persist($setting);
        }
    }
}