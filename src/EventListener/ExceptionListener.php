<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'code' => $throwable->getStatusCode(),
                'message' => 'Entity is not found',
            ]);

            $event->setResponse($response);
        } else if ($throwable instanceof HttpException) {
            $response = new JsonResponse([
                'code' => $throwable->getStatusCode(),
                'message' => $throwable->getMessage(),
            ]);

            $event->setResponse($response);
        } else {
//            $response = new JsonResponse([
//                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
//                'message' => 'Something went wrong',
//            ]);
//
//            $event->setResponse($response);
        }
    }
}
