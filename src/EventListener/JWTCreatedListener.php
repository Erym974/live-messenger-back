<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{

    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {

        /* Check if remember me is checked */
        $request = $this->requestStack->getCurrentRequest();
        if ($request->getContentType() === 'json') {
            $data = json_decode($request->getContent(), true);
            $remember = $data['remember'] ?? false;
        }

        $payload       = $event->getData();
        /** @var User */
        $user = $event->getUser();
        $payload['id'] = $user->getId();

        if ($remember) $payload['exp'] = (new \DateTime())->modify('+1 week')->getTimestamp();

        unset($payload['roles']);

        $event->setData($payload);

    }
}
