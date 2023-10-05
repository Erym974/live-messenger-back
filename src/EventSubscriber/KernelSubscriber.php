<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\JWT;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class KernelSubscriber implements EventSubscriberInterface {

    public function __construct(private EntityManagerInterface $em, private TokenStorageInterface $tokenStorage, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest($event)
    {
        $request = $event->getRequest();
        $authorization = $request->headers->get('Authorization');
        if($authorization == "" || $authorization == null) return;
        $jwt = str_replace('Bearer ', '', $authorization);
        if($jwt == null) return;
        if(!JWT::identify($jwt) || JWT::isExpired($jwt));
        try {
            $payload = JWT::getPayload($jwt);
            $id = $payload['id'];
            $user = $this->em->getRepository(User::class)->find($id);
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event);
        } catch(Exception $error) {
            echo "An error occurred";
        }
    }

}