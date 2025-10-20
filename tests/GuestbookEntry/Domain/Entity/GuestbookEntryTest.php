<?php

namespace App\Tests\GuestbookEntry\Domain\Entity;

use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use PHPUnit\Framework\TestCase;

class GuestbookEntryTest extends TestCase
{
    public function testCanCreateEntry(): void
    {
        $guestbookEntry = new GuestbookEntry('Damian', 'Cześć wszystkim!');

        $this->assertSame('Damian', $guestbookEntry->author());
        $this->assertSame('Cześć wszystkim!', $guestbookEntry->message());
    }

    public function testCannotCreateEntryWithTooLongMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            sprintf('Message cannot be more than %s characters.', GuestbookEntry::MAX_MESSAGE_LENGTH)
        );

        $longMessage = str_repeat('a', GuestbookEntry::MAX_MESSAGE_LENGTH + 1);
        new GuestbookEntry('Damian', $longMessage);
    }

    public function testCannotCreateEntryWithTooLongAuthor(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            sprintf('Author cannot have more than %s characters.', GuestbookEntry::MAX_AUTHOR_LENGTH)
        );

        $tooLongAuthor = str_repeat('a', GuestbookEntry::MAX_AUTHOR_LENGTH + 1);
        new GuestbookEntry($tooLongAuthor, 'Cześć wszystkim!');
    }

    public function testCannotCreateEntryWithoutMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Message cannot be empty.');

        new GuestbookEntry('Damian', '');
    }

    public function testCannotCreateEntryWithoutAuthor(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Author cannot be empty.');

        new GuestbookEntry('', 'Cześć wszystkim!');
    }
}
