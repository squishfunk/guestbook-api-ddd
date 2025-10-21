<?php

namespace App\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Application\Command\CreateGuestbookEntryCommand;
use App\GuestbookEntry\Application\ReadModel\GuestbookEntryView;
use App\GuestbookEntry\Application\Response\GuestbookEntryResponse;
use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use App\GuestbookEntry\Domain\ValueObject\GuestAuthor;
use App\GuestbookEntry\Domain\ValueObject\RegisteredAuthor;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CreateGuestbookEntryHandler
{
    public function __construct(
        private GuestbookEntryRepositoryInterface $repository
    ) {}

    public function __invoke(CreateGuestbookEntryCommand $command): GuestbookEntryView
    {

        if ($command->userId !== null) {
            $author = new RegisteredAuthor($command->userId, $command->author);
        } else {
            $author = new GuestAuthor($command->author, $command->email);
        }

        $entry = new GuestbookEntry(
            $author,
            $command->message
        );
        $this->repository->save($entry);

        return new GuestbookEntryView(
            $entry->id(),
            $entry->author()->getDisplayName(),
            $entry->message(),
            $entry->createdAt()->format('Y-m-d H:i:s')
        );
    }
}
