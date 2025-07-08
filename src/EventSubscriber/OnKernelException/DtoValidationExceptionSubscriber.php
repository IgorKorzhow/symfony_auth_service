<?php

namespace App\EventSubscriber\OnKernelException;

use App\Exceptions\DtoValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

class DtoValidationExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof DtoValidationException) {
            $response = new JsonResponse([
                'message' => $exception->getMessage(),
                'errors'  => $exception->getValidationErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

            $event->setResponse($response);
        }
    }
}
