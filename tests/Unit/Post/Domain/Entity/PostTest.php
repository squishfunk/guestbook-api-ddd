<?php

namespace App\Tests\Unit\Post\Domain\Entity;

use App\Post\Domain\Entity\Comment;
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

    public function testCanAddCommentToPost(): void
    {
        $author = new GuestAuthor('Damian', 'test@gmail.com');
        $post = new Post($author, 'Cześć wszystkim!');

        $comment = new Comment($post->id(), 'To jest komentarz', 'author-id-1');
        $post->addComment($comment);

        $comments = $post->comments();
        $this->assertCount(1, $comments);
        $this->assertSame($comment, $comments[0]);
    }

    public function testCannotAddCommentWithWrongPostId(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Comment does not belong to this post.');

        $author = new GuestAuthor('Damian', 'test@gmail.com');
        $post = new Post($author, 'Cześć wszystkim!');

        $comment = new Comment('wrong-post-id', 'To jest komentarz', 'author-id-1');
        $post->addComment($comment);
    }

    public function testCanCreatePostWithComments(): void
    {
        $author = new GuestAuthor('Damian', 'test@gmail.com');
        $postId = 'test-post-id';
        $post = new Post($author, 'Cześć wszystkim!', null, $postId);

        $comment1 = new Comment($postId, 'Komentarz 1', 'author-id-1');
        $comment2 = new Comment($postId, 'Komentarz 2', 'author-id-2');

        $post = new Post($author, 'Cześć wszystkim!', null, $postId, [$comment1, $comment2]);

        $comments = $post->comments();
        $this->assertCount(2, $comments);
        $this->assertSame($comment1, $comments[0]);
        $this->assertSame($comment2, $comments[1]);
    }
}

