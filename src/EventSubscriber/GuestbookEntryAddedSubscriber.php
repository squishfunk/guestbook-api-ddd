<?php

namespace App\EventSubscriber;

use App\Event\GuestbookEntryAddedEvent;
use App\Message\GuestbookEntryAnalysisMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GuestbookEntryAddedSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $bus){}

    public static function getSubscribedEvents()
    {
        return [
            GuestbookEntryAddedEvent::class => ['enqueueGuestbookEntryAdded'],
        ];
    }

    public function enqueueGuestbookEntryAdded(GuestbookEntryAddedEvent $event): void
    {
        $this->bus->dispatch(new GuestbookEntryAnalysisMessage($event->getEntry()->getId()));
    }
}
