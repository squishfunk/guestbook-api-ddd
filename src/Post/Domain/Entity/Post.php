<?php

namespace App\Post\Domain\Entity;

use App\Post\Domain\ValueObject\AuthorInterface;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class Post
{
    const MAX_MESSAGE_LENGTH = 300;
    const MAX_AUTHOR_LENGTH = 64;

    private string $id;

    private AuthorInterface $author;

    private string $message;

    private DateTimeImmutable $createdAt;

    /** @var list<Comment> */
    private array $comments;

    /**
     * @param list<Comment> $comments
     */
    public function __construct(AuthorInterface $author, string $message, ?DateTimeImmutable $createdAt = null, ?string $id = null, array $comments = [])
    {
        $this->id = $id ?? Uuid::v1();
        $this->setAuthor($author);
        $this->setMessage($message);
        $this->comments = [];

        if($createdAt){
            $this->createdAt = $createdAt;
        }else{
            $this->createdAt = new DateTimeImmutable();
        }

        foreach($comments as $comment){
            $this->addComment($comment);
        }
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function author(): AuthorInterface
    {
        return $this->author;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function updateMessage(string $message): void
    {
        $this->setMessage($message);
    }

    public function addComment(Comment $comment): void
    {
        if($comment->postId() !== $this->id){
            throw new \DomainException('Comment does not belong to this post.');
        }

        $this->comments[] = $comment;
    }

    /**
     * @return list<Comment>
     */
    public function comments(): array
    {
        return $this->comments;
    }

    private function setMessage(string $message): void
    {
        if(strlen($message) >= Post::MAX_MESSAGE_LENGTH){
            throw new \DomainException(sprintf('Message cannot be more than %s characters.', Post::MAX_MESSAGE_LENGTH));
        }

        if(empty($message)){
            throw new \DomainException('Message cannot be empty.');
        }

        $this->message = $message;
    }

    private function setAuthor(AuthorInterface $author): void
    {
        if (trim($author->getDisplayName()) === '') {
            throw new \DomainException('Author cannot be empty.');
        }

        if(strlen($author->getDisplayName()) >= Post::MAX_AUTHOR_LENGTH){
            throw new \DomainException(sprintf('Author cannot have more than %s characters.', Post::MAX_AUTHOR_LENGTH));
        }

        $this->author = $author;
    }
}

