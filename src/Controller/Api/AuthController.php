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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Mime\Address;

class AuthController extends AbstractController
{

    use ResetPasswordControllerTrait;

    public function __construct(private TranslatorInterface $translator, private MailerInterface $mailer, private ResetPasswordHelperInterface $resetPasswordHelper, private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
        
    }

    #[Route('api/auth/register', name: 'api.auth.register', methods: ['POST'])]
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

        if (!preg_match('/^(?=.*[^\w\s]).{8,}$/', $datas['password'])) return $this->responseService->ReturnError(400, "password_requirements");

        if($datas['password'] != $datas['password2']) return $this->responseService->ReturnError(400, "password_mismatch");

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $datas['email']]);

        if ($user != null)  return $this->responseService->ReturnError(400, "Email already used");

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

        $emailSended = $this->processSendingActiveAccountEmail($user);

        return $this->responseService->ReturnSuccess(["user" => $user->getId(), "email" => $emailSended]);
    }

    /*** RESET PASSWORD */

    #[Route('api/auth/reset-password', name: 'api.auth.reset-password', methods: ['POST'])]
    public function resetPasswordRequest(Request $request, MailerInterface $mailer, TranslatorInterface $translator): JsonResponse
    {

        $params = json_decode($request->getContent(), true);

        $email = $params['email'] ?? null;
        if ($email === null) return $this->responseService->ReturnError(400, "Missing parameters");

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user === null) return $this->responseService->ReturnSuccess(200, []);

        $result = $this->processSendingPasswordResetEmail(
            $email,
            $mailer,
            $translator
        );
        if($result) return $this->responseService->ReturnSuccess(200, []);
        return $this->responseService->ReturnSuccess(200, []);

    }

    #[Route('api/auth/reset-password/reset', name: 'api.auth.reset-password.reset', methods: ['POST'])]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): JsonResponse
    {

        $params = json_decode($request->getContent(), true);

        $token = $params['token'] ?? null;
        if ($token === null) return $this->responseService->ReturnError(400, "Missing token");

        $password = $params['password'] ?? null;
        if ($password === null) return $this->responseService->ReturnError(400, "Missing password");

        $confirmPassword = $params['confirmPassword'] ?? null;
        if ($confirmPassword === null) return $this->responseService->ReturnError(400, "Missing confirm password");

        if (!preg_match('/^(?=.*[^\w\s]).{8,}$/', $password)) return $this->responseService->ReturnError(400, "password_requirements");

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->responseService->ReturnError(401, "Invalid token");
        }

        if ($password !== $confirmPassword) return $this->responseService->ReturnError(400, "password_mismatch");

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($encodedPassword);
        $this->em->persist($user);
        $this->em->flush();

        return $this->responseService->ReturnSuccess(200, []);

    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): bool
    {

        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return false;
        }

        $email = (new TemplatedEmail())
            ->from(new Address('reset-password@swiftchat.fr', '[SwiftChat] Reset Password'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('mails/reset-password.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'front_end' => $this->getParameter('front_end'),
            ])
        ;

        $mailer->send($email);
        return true;
    }

    /*** ACTIVE ACCOUNT */

    #[Route('api/auth/active-account', name: 'api.auth.active-account', methods: ['POST'])]
    public function activeAccount(Request $request, TranslatorInterface $translator): JsonResponse
    {

        $params = json_decode($request->getContent(), true);
        
        $token = $params['token'] ?? null;
        if ($token === null) return $this->responseService->ReturnError(400, "Missing token");

        if (!JWT::identify($token)) return $this->responseService->ReturnError(400, "Invalid token");
        if (JWT::isExpired($token)) return $this->responseService->ReturnError(400, "Token expired");
        
        $payload = JWT::getPayload($token);
        /** @var User */
        $user = $this->em->getRepository(User::class)->find($payload['id']);

        if ($user === null) return $this->responseService->ReturnError(400, "User not found");
        $user->setIsVerified(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->responseService->ReturnSuccess(200, []);

    }

    #[Route('api/auth/active-account/request', name: 'api.auth.active-account.request', methods: ['POST'])]
    public function activeAccountRequest(Request $request, TranslatorInterface $translator): JsonResponse
    {
            
            $params = json_decode($request->getContent(), true);
    
            $email = $params['email'] ?? null;
            if ($email === null) return $this->responseService->ReturnError(400, "Missing email");
    
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user === null) return $this->responseService->ReturnError(400, "User not found");

            $emailSended = $this->processSendingActiveAccountEmail($user);

            if(!$emailSended) return $this->responseService->ReturnError(500, "Error while sending email");
            return $this->responseService->ReturnSuccess(200, []);
    
    }

    private function processSendingActiveAccountEmail(User $user) : bool
    {

        try {
            $email = (new TemplatedEmail())
            ->from(new Address('registration@swiftchat.fr', '[SwiftChat] Active Account'))
            ->to($user->getEmail())
            ->subject('Active your account')
            ->htmlTemplate('mails/active-account.html.twig')
            ->context([
                'front_end' => $this->getParameter('front_end'),
                'token' => JWT::generate(900, ['id' => $user->getId()])
            ])
            ;

            $this->mailer->send($email);

            return true;
        } catch(\Exception $e) {
            return false;
        }

    }



}
