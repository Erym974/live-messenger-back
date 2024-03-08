<?php

namespace App\EntityListener;

use App\Entity\File;
use App\Entity\Meta;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{

    public function __construct(private UserPasswordHasherInterface $hasher, private EntityManagerInterface $em, private ParameterBagInterface $parameters)
    {
        
    }

    public function prePersist(User $user)
    {

        $this->createSetting($user);
        
        $user->setProfilePicture($this->uploadFile('profile'));
        $user->setCoverPicture($this->uploadFile('cover'));
        
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
            "allow-easter", 
            "language"
        ];

        foreach($settings as $meta) {
            $meta = $this->em->getRepository(Meta::class)->findOneBy(['name' => $meta]);
            if(!$meta) continue;
            $setting = new Setting();
            $setting->setMeta($meta);
            $setting->setValue($meta->getValue());
            $user->addSetting($setting);
        }
    }

    private function uploadFile(string $type) : File 
    {

        $defaultPictureName = "";


        switch($type) {
            case 'profile':
                $defaultProfilePictures = $this->parameters->get('default_profile_pictures');
                $defaultPictureName = $defaultProfilePictures[array_rand($defaultProfilePictures)];
                break;
            case 'cover':
                $defaultCoverPictures = $this->parameters->get('default_cover_pictures');
                $defaultPictureName = $defaultCoverPictures[array_rand($defaultCoverPictures)];
                break;
        }

        $parts = explode(".", $defaultPictureName);
        $ext = end($parts);
                    
        $name = md5(uniqid()) . ".$ext";
        $file = new File();
        $file->setName($name);
        $file->setParent('users');
        $file->setType("image/$ext");
        $file->setPath($this->parameters->get('ressources_url') . "/users/" . $name);
        copy($this->parameters->get('file_factory_path') . $defaultPictureName, $this->parameters->get('upload_directory') . "users/" . $name);
        return $file;
    }

}