<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiErrorListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();

        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        if ($subdomain == 'api') {
            $exception = $event->getThrowable();
            
            switch (true) {
                case $exception instanceof NotFoundHttpException:
                    $response = new JsonResponse(['status' => false, "message" => 'Resource not found'], JsonResponse::HTTP_NOT_FOUND);
                    break;
                default:
                    $response = new JsonResponse(['status' => false, "message" => $exception->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                    break;
            }
            $event->setResponse($response);
        }
    }
}