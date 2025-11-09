<?php

namespace App\Shared\Infrastructure\EventListener;

use DomainException;
use JsonException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[AsEventListener(event: 'kernel.exception')]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if ($e instanceof NotEncodableValueException) {
            $event->setResponse(new JsonResponse([
                'message' => 'Invalid JSON format.',
            ], Response::HTTP_BAD_REQUEST));
        }

        if ($e instanceof DomainException) {
            $event->setResponse(new JsonResponse([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        if ($e instanceof JsonException) {
            $event->setResponse(new JsonResponse([
                'message' => 'Invalid JSON format.',
            ], Response::HTTP_BAD_REQUEST));
        }

        if ($e instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse([
                'message' => 'Access denied: ' . $e->getMessage()
            ], Response::HTTP_FORBIDDEN));
        }
    }
}
