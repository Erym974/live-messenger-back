<?php

namespace App\Security;

use App\Service\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class JwtAccessVoter extends Voter
{
    protected function supports($attribute, $subject) : bool
    {
        return $attribute === 'JWT_HEADER_ACCESS';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        $request = Request::createFromGlobals();
        $authorization = $request->headers->get('Authorization');
        $jwt = str_replace('Bearer ', '', $authorization);
        return (JWT::identify($jwt) && !JWT::isExpired($jwt)) ? true : false;
    }
}