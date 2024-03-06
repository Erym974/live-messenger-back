<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\LocaleSwitcher;

class KernelSubscriber implements EventSubscriberInterface {

    public function __construct(private EntityManagerInterface $em, private LocaleSwitcher $localeSwitcher, private EventDispatcherInterface $eventDispatcher)
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

        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        if ($subdomain == 'api') {
            $locale = $request->headers->get('Content-Language') ?? 'en';
            $request->setLocale($locale);
            $this->localeSwitcher->setLocale($locale);
        }
    }

}