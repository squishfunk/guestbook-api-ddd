<?php

namespace App\Tests\Unit\GuestbookEntry\Domain\Entity;

use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use App\GuestbookEntry\Domain\ValueObject\GuestAuthor;
use PHPUnit\Framework\TestCase;

class GuestbookEntryTest extends TestCase
{
    public function testCanCreateEntry(): void
    {
        $author = new GuestAuthor('Damian', 'test@gmail.com');


        $guestbookEntry = new GuestbookEntry($author, 'Cześć wszystkim!');

        $this->assertInstanceOf(GuestAuthor::class, $guestbookEntry->author());
        $this->assertSame('Cześć wszystkim!', $guestbookEntry->message());
        $this->assertInstanceOf(\DateTimeImmutable::class, $guestbookEntry->createdAt());
    }

    public function testCannotCreateEntryWithTooLongMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            sprintf('Message cannot be more than %s characters.', GuestbookEntry::MAX_MESSAGE_LENGTH)
        );

        $longMessage = str_repeat('a', GuestbookEntry::MAX_MESSAGE_LENGTH + 1);
        new GuestbookEntry(new GuestAuthor('Damian', 'test@gmail.com'), $longMessage);
    }

    public function testCannotCreateEntryWithoutMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Message cannot be empty.');

        $author = new GuestAuthor('Damian', 'test@gmail.com');


        new GuestbookEntry($author, '');
    }
}
