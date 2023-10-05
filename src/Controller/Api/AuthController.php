<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\JWT;
use App\Service\ResponseService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;

class AuthController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
        
    }

    #[Route('auth/login', name: 'api.auth.login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $email = $parameters['email'] ?? null;
        $password = $parameters['password'] ?? null;
        $remember = $parameters['remember'] ?? false;

        if ($email == null || $password == null) return $this->responseService->ReturnError(400, "Missing parameters");

        /** @var User */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        $isValid = false;

        if($user != null) $isValid = $this->hasher->isPasswordValid($user, $password);

        if ($user == null || !$isValid) return $this->responseService->ReturnError(404, "No account was found with these credentials");

        $token = JWT::generate($remember ? 86400 * 7 : 7200, [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
        ]);

        return $this->responseService->ReturnSuccess([
            "token" => $token,
            "user" => $user
        ], ['groups' => 'user:read']);
    }

    #[Route('auth/register', name: 'api.auth.register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $datas = [
            "firstname" => $parameters['firstname'] ?? null,
            "lastname" => $parameters['lastname'] ?? null,
            "email" => $parameters['email'] ?? null,
            "password" => $parameters['password'] ?? null,
            "password2" => $parameters['password2'] ?? null,
        ];

        $hasNullValue = false;

        foreach ($datas as $value) {
            if ($value === null) {
                $hasNullValue = true;
                break;
            }
        }

        if ($hasNullValue) return $this->responseService->ReturnError(400, "Missing parameters");

        if (!filter_var($datas['email'], FILTER_VALIDATE_EMAIL)) return $this->responseService->ReturnError(400, "Invalid email");

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $datas['password'])) {
            
        }

        if($datas['password'] != $datas['password2']) return $this->responseService->ReturnError(400, "Password does not match");

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $datas['email']]);

        if ($user != null)  return $this->responseService->ReturnError(400, "Email already used");

        return $this->responseService->ReturnError(500, $e->getMessage());

        $user = new User();
        $user->setFirstname($datas['firstname'])
            ->setLastname($datas['lastname'])
            ->setEmail($datas['email'])
            ->setPlainPassword($datas['password']);

        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->responseService->ReturnError(500, $e->getMessage());
        }

        return $this->responseService->ReturnSuccess($user->getId());
    }

    #[Route('auth/refresh', name: 'api.auth.refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {

        $authorization = $request->headers->get('Authorization');
        $jwt = str_replace('Bearer ', '', $authorization);
        
        if(!JWT::identify($jwt) || JWT::isExpired($jwt)) return $this->responseService->ReturnError(400, "Bad credentials");

        $header = JWT::getHeader($jwt);
        $payload = JWT::getPayload($jwt);

        $token = JWT::generate($payload, $header);

        return $this->responseService->ReturnSuccess([
            "status" => true,
            "token" => $token
        ]);
        
    }

}
