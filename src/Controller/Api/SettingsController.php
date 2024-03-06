<?php

namespace App\Controller\Api;

use App\Entity\Meta;
use App\Entity\Setting;
use App\Entity\User;
use App\Service\ResponseService;
use App\Service\SettingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class SettingsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
        
    }

    #[Route('api/settings', name: 'api.settings', methods: ['POST'])]
    
    public function settings(Request $request, SettingService $settingsService): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);

        if(!isset($params['meta']) || !isset($params['value'])) return $this->responseService->ReturnError(400, "Missing parameters");

        
        $meta = $this->em->getRepository(Meta::class)->findOneBy(['name' => $params['meta']]);

        if(!$meta) return $this->responseService->ReturnError(400, "Meta not found");

        $setting = $this->em->getRepository(Setting::class)->findSetting($user, $meta);

        $verfiedValue = $settingsService->verifyValue($meta, $params['value']);

        if($verfiedValue === null) return $this->responseService->ReturnError(400, "Value not valid for this meta setting");

        if(!$setting) {
            $setting = new Setting();
            $setting->setUser($user);
            $setting->setMeta($meta);
            $setting->setValue($verfiedValue);
        } else {
            $setting->setValue($verfiedValue);
        }

        $this->em->persist($setting);
        $this->em->flush();

        return $this->responseService->ReturnSuccess($user, ['groups' => "user:read"]);

    }

}
