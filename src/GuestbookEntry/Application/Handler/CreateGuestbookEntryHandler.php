<?php

namespace App\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Application\Command\CreateGuestbookEntryCommand;
use App\GuestbookEntry\Application\Response\GuestbookEntryResponse;
use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CreateGuestbookEntryHandler
{
    public function __construct(
        private GuestbookEntryRepositoryInterface $repository
    ) {}

    public function __invoke(CreateGuestbookEntryCommand $command): GuestbookEntry
    {
        $entry = new GuestbookEntry(
            $command->author,
            $command->message
        );

        $this->repository->save($entry);

        return $entry;
    }
}
