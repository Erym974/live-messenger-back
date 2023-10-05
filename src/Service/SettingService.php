<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\Meta;
use App\Entity\Setting;
use App\Entity\User;
use App\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;

class SettingService extends AbstractService {

    function verifyValue(Meta $meta, string $value) {
        
        $allowed = $meta->getAllowed();

        switch($allowed) {
            case "bool":
            case "boolean":
                if($value == "true" || $value == true) return "true";
                if($value == "false" || $value == false) return "false";
                return null;
                break;
            case "int":
            case "integer":
                if(is_numeric($value)) return intval($value);
                return null;
                break;
            case "array":
                if(is_array($value)) return $value;
                return null;
                break;
            default:
                return $value;
        }

    }

    public function getSetting(User $user, string $meta) : ?string
    {

        $meta = $this->em->getRepository(Meta::class)->findOneBy(['name' => $meta]);

        if($meta == null) return null;

        $settings = $user->getSettings();
        foreach($settings as $setting) {
            if($setting->getMeta() == $meta) return $setting->getValue();
        }
        return $meta->getValue();
    }

    public function fetchSettings(User $user) : array
    {

        $settings = $user->getSettings();
        $metas = $this->em->getRepository(Meta::class)->findAll();
        $finalSettings = [];
        $tempSettings = [];

        foreach($settings as $setting) {
            array_push($tempSettings, $setting->getMeta()->getName());
            array_push($finalSettings, $setting);
        }

        foreach($metas as $meta) {
            
            if(!in_array($meta->getName(), $tempSettings)) {
                $setting = new Setting();
                $setting->setMeta($meta);
                $setting->setValue($meta->getValue());
                array_push($finalSettings, $setting);
            }

        }

        return $finalSettings;

    }

}