<?php

namespace App\Security;

use App\Service\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {

        $roles = $authException->getPrevious()->getAttributes();

        // return new JsonResponse(
        //     ["error" => "Bad JWT Token"],
        //     401,
        //     ['Content-Type' => "application/json"]
        // );

        if(in_array('JWT_HEADER_ACCESS', $roles)) {
            return new JsonResponse(
                ["error" => "Bad JWT Token"],
                401,
                ['Content-Type' => "application/json"]
            );
        }

        return new RedirectResponse($this->router->generate('auth.login'));

    }
}