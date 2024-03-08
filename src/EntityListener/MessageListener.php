<?php

namespace App\EntityListener;

use App\Entity\Message;
use App\Service\EncryptService;

class MessageListener
{

    public function __construct(private EncryptService $encryptService)
    {
        
    }

    public function postLoad(Message $message)
    {
        if($message->getEncryptedContent() != null) {
            $message->setContent(
                $this->encryptService->decrypt(
                    $message->getEncryptedContent(), 
                    $this->getIVFromMessage($message)
                )
            );
        }
    }

    public function prePersist(Message $message)
    {
        $this->encryptMessage($message);
    }

    public function preUpdate(Message $message) {
        $this->encryptMessage($message);
    }

    private function encryptMessage(Message $message) {
        if($message->getContent() != null) {

            $iv = $this->getIVFromMessage($message);

            $message->setEncryptedContent(
                $this->encryptService->encrypt(
                    $message->getContent(), 
                    $iv
                )
            );
        }
    }

    private function getIVFromMessage(Message $message) : string|int 
    {
        $ivLength = openssl_cipher_iv_length($this->encryptService->getAlgo());
        $timestamp = $message->getSendedAt()->getTimestamp();
        $iv = str_pad($timestamp, $ivLength, "\0");
        return $iv;
    }

}