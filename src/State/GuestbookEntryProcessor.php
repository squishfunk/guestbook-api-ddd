<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GuestbookEntry;
use App\Event\GuestbookEntryAddedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GuestbookEntryProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor $processor,
        private EventDispatcherInterface $eventDispatcher
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        if($result instanceof GuestbookEntry){
            $this->eventDispatcher->dispatch(new GuestbookEntryAddedEvent($result));
        }

        return $result;
    }
}
