<?php

namespace App\Tests\Unit\Post\Domain\Entity;

use App\Post\Domain\Entity\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testCanCreateComment(): void
    {
        $postId = 'test-post-id';
        $content = 'To jest komentarz';
        $authorId = 'test-author-id';

        $comment = new Comment($postId, $content, $authorId);

        $this->assertSame($postId, $comment->postId());
        $this->assertSame($content, $comment->content());
        $this->assertSame($authorId, $comment->authorId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->createdAt());
        $this->assertNotEmpty($comment->id());
    }

    public function testCannotCreateCommentWithTooLongContent(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(
            sprintf('Content cannot be more than %s characters.', Comment::MAX_CONTENT_LENGTH)
        );

        $longContent = str_repeat('a', Comment::MAX_CONTENT_LENGTH + 1);
        new Comment('test-post-id', $longContent, 'test-author-id');
    }

    public function testCannotCreateCommentWithoutContent(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Content cannot be empty.');

        new Comment('test-post-id', '', 'test-author-id');
    }

    public function testCannotCreateCommentWithoutPostId(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Post ID cannot be empty.');

        new Comment('', 'To jest komentarz', 'test-author-id');
    }

    public function testCannotCreateCommentWithoutAuthorId(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Author ID cannot be empty.');

        new Comment('test-post-id', 'To jest komentarz', '');
    }
}

