<?php

namespace App\Controller\Api;

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

#[IsGranted('JWT_HEADER_ACCESS')]
class UserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ResponseService $responseService)
    {
        
    }

    #[Route('api/users/me', name: 'api.users.me', methods: ['GET', 'PATCH', 'POST'])]
    public function users_me(Request $request, SettingService $settingService): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == "PATCH") {

            $params = json_decode($request->getContent(), true);

            if($params == null) $this->responseService->ReturnError(400, "Missing parameters");

            $firstname = $params['firstname'] ?? null;
            $lastname = $params['lastname'] ?? null;
            $biography = $params['biography'] ?? null;
            $email = $params['email'] ?? null;

            if(strlen($biography) > 50) $this->responseService->ReturnError(400, "Biography is too long");


            if($email != null) {
                $user_email = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->responseService->ReturnError(400, "Invalid format for email");
                if($user_email != null) $this->responseService->ReturnError(400, "Email already used");
            }

            if($firstname != null) $user->setFirstname($firstname);
            if($lastname != null) $user->setLastname($lastname);
            if($biography != null) $user->setBiography($biography);
            if($email != null) $user->setEmail($email);

            $this->em->persist($user);
            $this->em->flush();

        }

        if($request->getMethod() == "POST") {

            $params = json_decode($request->getContent(), true);
            if(!$params) $params = $request->request->all();

            $file = $request->files->get('file');

            if($params == null) $this->responseService->ReturnError(400, "Missing parameters");
            if($file == null) $this->responseService->ReturnError(400, "Missing file parameters");

            if($params['picture'] != "profile" && $params['picture'] != "cover") {
                $this->responseService->ReturnError(400, "Invalid parameters");
            }

            if($file->getMimeType() != "image/jpeg" && $file->getMimeType() != "image/png") {
                $this->responseService->ReturnError(400, "Invalid file type");
            }

            if($file->getSize() > 1000000) {
                $this->responseService->ReturnError(400, "File is too big");
            }

            $filename = md5(uniqid()) . "." . $file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $filename);

            if($params['picture'] == "profile") {
                $user->setProfilePicture("/" . $filename);
            } else {
                $user->setCoverPicture("/" . $filename);
            }

            $this->em->persist($user);
            $this->em->flush();

        }

        $user->setSettings($settingService->fetchSettings($user));
        


        $mercure = JWT::generate(86400, [
            "mercure" => [
                "subscribe" => [
                    "http://localhost:3000/user/" . $user->getId(),
                ],
            ]
        ]);

        $response = $this->responseService->ReturnSuccess([
            "user" => $user,
        ], ['groups' => 'user:read']);

        $response->headers->setCookie(new Cookie(
            'mercureAuthorization',
            $mercure,
            (new \DateTime())->add(new \DateInterval('PT2H')),
            '/.well-know/mercure',
            null,
            false,
            true,
            false
        ));

        return $response;

    }

    #[Route('api/users/{user?}', name: 'api.users', methods: ['GET'])]
    public function users(?User $user, SettingService $settingService, SerializerInterface $serialize): JsonResponse
    {

        if(!$user) return $this->responseService->ReturnError(400, "Missing parameters");

        /** @var User */
        $me = $this->getUser();

        if($me == $user) return $this->responseService->ReturnError(400, "You can't see your own profile, please use /api/users/me");

        $friend = $me->getFriend($user); 

        // map friend to keep only since and id
        if($friend != null) {
            $friend = [
                "id" => $friend->getId(),
                "since" => $friend->getSince()
            ];
        }

        $user->setSettings($settingService->fetchSettings($user));
        $invitation = $this->em->getRepository(Invitation::class)->findInvitation($me, $user);

        return $this->responseService->ReturnSuccess([
            "user" => $user,
            "relationship" => $friend,
            "invitation" => $invitation
        ], ['groups' => 'user:friend']);

    }

}
