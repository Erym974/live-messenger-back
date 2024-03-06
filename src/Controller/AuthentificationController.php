<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthentificationController extends AbstractController
{

    #[Route(path: '/auth/login', name: 'auth.login', host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        /** @var User */
        $user = $this->getUser();
        if ($user) return $this->redirectToRoute('admin.dashboard');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('authentification/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/auth/logout', name: 'auth.logout', host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
