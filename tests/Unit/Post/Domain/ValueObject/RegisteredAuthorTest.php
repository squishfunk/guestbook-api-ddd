<?php

namespace App\Tests\Unit\Post\Domain\ValueObject;

use App\Post\Domain\ValueObject\RegisteredAuthor;
use PHPUnit\Framework\TestCase;

class RegisteredAuthorTest extends TestCase
{
    public function testCanCreateRegisteredAuthor(): void
    {
        $registeredAuthor = new RegisteredAuthor('123', 'Username666');

        $this->assertSame('123', $registeredAuthor->getUserId());
        $this->assertSame('Username666', $registeredAuthor->getDisplayName());
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

