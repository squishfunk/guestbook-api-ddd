<?php

namespace App\Tests\Unit\GuestbookEntry\Domain\ValueObject;

use App\GuestbookEntry\Domain\ValueObject\DisplayNameTrait;
use App\GuestbookEntry\Domain\ValueObject\GuestAuthor;
use PHPUnit\Framework\TestCase;

class GuestAuthorTest extends TestCase
{
    public function testCanCreateGuestAuthor(): void
    {
        $guestbookEntry = new GuestAuthor('Damian', 'test@gmail.com');

        $this->assertSame('Damian', $guestbookEntry->getDisplayName());
        $this->assertSame('test@gmail.com', $guestbookEntry->getEmail());
    }

    public function testNameCannotBeEmpty(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Display name cannot be empty.');
        new GuestAuthor('');
    }

    public function testEmailCanBeNull(): void
    {
        $author = new GuestAuthor('Jane Doe');
        $this->assertNull($author->getEmail());
    }

    public function testCannotCreateEntryWithoutAuthor(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Display name cannot be empty.');

        new GuestAuthor('', 'test@gmail.com');
    }

    public function testDisplayNameCannotBeLongerThanMaxLength(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(sprintf('Display name cannot exceed %s characters.', GuestAuthor::MAX_AUTHOR_LENGTH));

        $displayName = str_repeat('a', GuestAuthor::MAX_AUTHOR_LENGTH + 1);
        new GuestAuthor($displayName, 'test@gmail.com');
    }
}
