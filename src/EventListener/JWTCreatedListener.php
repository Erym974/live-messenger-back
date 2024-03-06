<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{


    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {

        $payload       = $event->getData();
        /** @var User */
        $user = $event->getUser();
        $payload['id'] = $user->getId();

        $event->setData($payload);

    }
}
