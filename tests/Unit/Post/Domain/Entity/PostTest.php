<?php

namespace App\Tests\Unit\Post\Domain\Entity;

use App\Post\Domain\Entity\Post;
use App\Post\Domain\ValueObject\GuestAuthor;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testCanCreatePost(): void
    {
        $author = new GuestAuthor('Damian', 'test@gmail.com');


        $post = new Post($author, 'Cześć wszystkim!');

        $this->assertInstanceOf(GuestAuthor::class, $post->author());
        $this->assertSame('Cześć wszystkim!', $post->message());
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->createdAt());
    }

    public function testCannotCreatePostWithTooLongMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            sprintf('Message cannot be more than %s characters.', Post::MAX_MESSAGE_LENGTH)
        );

        $longMessage = str_repeat('a', Post::MAX_MESSAGE_LENGTH + 1);
        new Post(new GuestAuthor('Damian', 'test@gmail.com'), $longMessage);
    }

    public function testCannotCreatePostWithoutMessage(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Message cannot be empty.');

        $author = new GuestAuthor('Damian', 'test@gmail.com');


        new Post($author, '');
    }
}

