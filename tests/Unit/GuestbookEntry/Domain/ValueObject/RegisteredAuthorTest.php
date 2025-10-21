<?php

namespace App\Tests\Unit\GuestbookEntry\Domain\ValueObject;

use App\GuestbookEntry\Domain\ValueObject\RegisteredAuthor;
use PHPUnit\Framework\TestCase;

class RegisteredAuthorTest extends TestCase
{
    public function testCanCreateGuestAuthor(): void
    {
        $guestbookEntry = new RegisteredAuthor('123', 'Username666');

        $this->assertSame('123', $guestbookEntry->getUserId());
        $this->assertSame('Username666', $guestbookEntry->getDisplayName());
    }

    public function testIdCannotBeEmpty(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User ID cannot be empty.');
        new RegisteredAuthor('', 'Username666');
    }

    public function testNameCannotBeEmpty(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Display name cannot be empty.');
        new RegisteredAuthor('123', '');
    }
}
