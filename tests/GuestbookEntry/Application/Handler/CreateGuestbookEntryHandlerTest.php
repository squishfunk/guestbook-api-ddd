<?php

namespace App\Tests\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Application\Command\CreateGuestbookEntryCommand;
use App\GuestbookEntry\Application\Handler\CreateGuestbookEntryHandler;
use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreateGuestbookEntryHandlerTest extends TestCase
{
    public function testHandlerCreatesAndSavesEntry(): void
    {
        $mockRepo = $this->createMock(GuestbookEntryRepositoryInterface::class);
        $mockRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(GuestbookEntry $entry) {
                    return $entry->author() === 'KubaDDD'
                    && $entry->message() === 'Hello World';
            }));

        $command = new CreateGuestbookEntryCommand(
            'KubaDDD',
            'Hello World'
        );

        $handler = new CreateGuestbookEntryHandler($mockRepo);
        $handler($command);
    }
}
