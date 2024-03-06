<?php

namespace App\Controller\Api;

use App\Entity\Friend;
use App\Entity\Invitation;
use App\Entity\User;
use App\Service\JWT;
use App\Service\ResponseService;
use App\Service\SettingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Serializer\SerializerInterface;


class UserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService, private SettingService $settingService)
    {
    }

    #[Route('users/me', name: 'api.users.me.post', methods: ['POST'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function users_me_post(Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);
        if (!$params) $params = $request->request->all();

        $file = $request->files->get('file');

        if ($params == null) return $this->responseService->ReturnError(400, "Missing parameters");
        if ($file == null) return $this->responseService->ReturnError(400, "Missing file parameters");

        if ($params['picture'] != "profile" && $params['picture'] != "cover") {
            return $this->responseService->ReturnError(400, "Invalid parameters");
        }

        if ($file->getMimeType() != "image/jpeg" && $file->getMimeType() != "image/png") {
            return $this->responseService->ReturnError(400, "Invalid file type");
        }

        if ($file->getSize() > 1000000) {
            return $this->responseService->ReturnError(400, "File is too big");
        }

        $filename = md5(uniqid()) . "." . $file->guessExtension();
        $file->move($this->getParameter('users_upload_directory'), $filename);

        if ($params['picture'] == "profile") {
            $user->setProfilePicture("/" . $filename);
        } else {
            $user->setCoverPicture("/" . $filename);
        }

        $this->em->persist($user);
        $this->em->flush();

        $user->setSettings($this->settingService->fetchSettings($user));

        return $this->responseService->ReturnSuccess([
            "user" => $user,
        ], ['groups' => 'user:read']);
    }

    #[Route('users/me', name: 'api.users.me.patch', methods: ['PATCH'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function me_patch(Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();


        $params = json_decode($request->getContent(), true);

        if ($params == null) $this->responseService->ReturnError(400, "Missing parameters");

        $firstname = $params['firstname'] ?? null;
        $lastname = $params['lastname'] ?? null;
        $biography = $params['biography'] ?? null;
        $email = $params['email'] ?? null;

        if (strlen($biography) > 50) $this->responseService->ReturnError(400, "Biography is too long");


        if ($email != null) {
            $user_email = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->responseService->ReturnError(400, "Invalid format for email");
            if ($user_email != null) $this->responseService->ReturnError(400, "Email already used");
        }

        if ($firstname != null) $user->setFirstname($firstname);
        if ($lastname != null) $user->setLastname($lastname);
        if ($biography != null) $user->setBiography($biography);
        if ($email != null) $user->setEmail($email);

        $this->em->persist($user);
        $this->em->flush();

        $user->setSettings($this->settingService->fetchSettings($user));

        return $this->responseService->ReturnSuccess([
            "user" => $user,
        ], ['groups' => 'user:read']);
    }

    #[Route('users/me', name: 'api.users.me.get', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function me_get(Request $request): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        
        $user->setSettings($this->settingService->fetchSettings($user));

        return $this->responseService->ReturnSuccess([
            "user" => $user,
        ], ['groups' => 'user:read']);
    }

    #[Route('users/{user}', name: 'api.users', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function users(User $user): JsonResponse
    {

        if (!$user) return $this->responseService->ReturnError(400, "Missing parameters");

        /** @var User */
        $me = $this->getUser();

        if ($me == $user) return $this->responseService->ReturnError(400, "You can't see your own profile, please use /api/users/me");

        $friend = $me->getFriend($user);

        if ($friend != null) {
            $mutual = $this->em->getRepository(Friend::class)->getMutualFriends($me, $friend->getFriend());
            $mutual = array_map(function ($friend) {
                return [
                    'id' => $friend->getFriend()->getId(),
                    'fullname' => $friend->getFriend()->getFullname(),
                    'profilePicture' => $friend->getFriend()->getProfilePicture()
                ];
            }, $mutual);
            $friend->setMutual($mutual);

            $friend = [
                "id" => $friend->getId(),
                "since" => $friend->getSince(),
                "mutual" => $friend->getMutual(),
                "group" => $friend->getConversation()->getId()
            ];
        }

        $user->setSettings($this->settingService->fetchSettings($user));
        $invitation = $this->em->getRepository(Invitation::class)->findInvitation($me, $user);

        return $this->responseService->ReturnSuccess([
            "user" => $user,
            "relationship" => $friend,
            "invitation" => $invitation
        ], ['groups' => 'user:friend']);
    }
}
