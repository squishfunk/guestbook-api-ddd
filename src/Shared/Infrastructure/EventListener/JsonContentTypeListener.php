<?php

namespace App\Shared\Infrastructure\EventListener;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: KernelEvents::REQUEST)]
class JsonContentTypeListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            return;
        }

        /* TODO AS DEFAULT ALL ENDPOINTS SHOULD BE API */
//        if (!$this->isApiEndpoint($request->getPathInfo())) {
//            return;
//        }

        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format. Expected JSON.');
        }
    }

}
