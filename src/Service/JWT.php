<?php

namespace App\Service;

use App\Entity\User;
use App\Service\AbstractService;
use DateTime;

class JWT extends AbstractService {
    
    static public function generate($validity = 7200, $payload = [], $header = ['typ' => 'JWT', 'alg' => 'HS256']) : string
    {

        $expiration = (new DateTime())->getTimestamp() + $validity;

        $payload = [
            'iat' => (new DateTime())->getTimestamp(),
            'exp' => $expiration,
            ...$payload
        ];

        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        $secret = base64_encode($_ENV['JWT_SECRET']);

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $secret, true);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64Header . "." . $base64Payload . "." . $base64Signature;

        return $jwt;

    }

    static public function identify(string $jwt) : bool
    {
        if(!$jwt) return false;
        if (preg_match_all('/\./', $jwt) !== 2) return false;

        $header = self::getHeader($jwt);
        $payload = self::getPayload($jwt);
        if ($header === null || $payload === null) return false;
        $verifToken = self::generate(7200, $payload, $header);
        return $verifToken === $jwt;
    }

    static public function isExpired(string $jwt) : bool
    {
        $payload = self::getPayload($jwt);
        
        if (self::identify($jwt)) {
            return $payload['exp'] < (new DateTime())->getTimestamp();
        } else {
            return true;
        }
    }

    static public function getHeader(string $jwt) : array | null
    {
        return json_decode(base64_decode(explode('.', $jwt)[0]), true);
    }

    static public function getPayload(string $jwt) : array | null
    {
        return json_decode(base64_decode(explode('.', $jwt)[1]), true);
    }

}