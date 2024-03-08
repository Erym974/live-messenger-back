<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EncryptService {

    private $secret, $algo;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->secret = $parameters->get('encrypt_secret');
        $this->algo = $parameters->get('encrypt_algo');
    }

    public function encrypt(string $string, string|int $iv) {
        return openssl_encrypt($string, $this->algo, $this->secret, 0, $iv);
    }

    public function decrypt(string $string, string|int $iv) {
        return openssl_decrypt($string, $this->algo, $this->secret, 0, $iv);
    }

    public function getAlgo() : string
    {
        return $this->algo;
    }


}