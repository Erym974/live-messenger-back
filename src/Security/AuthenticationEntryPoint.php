<?php

namespace App\Security;

use App\Service\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct() {
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(
            ["error" => "Bad JWT Token"],
            401,
            ['Content-Type' => "application/json"]
        );
    }
}