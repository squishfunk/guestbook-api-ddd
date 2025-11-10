<?php

namespace App\Post\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class Comment
{
    const MAX_CONTENT_LENGTH = 500;

    private string $id;

    private string $postId;

    private string $content;

    private string $authorId;

    private DateTimeImmutable $createdAt;

    public function __construct(string $postId, string $content, string $authorId, ?DateTimeImmutable $createdAt = null, ?string $id = null)
    {
        $this->id = $id ?? Uuid::v1();
        $this->setPostId($postId);
        $this->setContent($content);
        $this->setAuthorId($authorId);

        if($createdAt){
            $this->createdAt = $createdAt;
        }else{
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function postId(): string
    {
        return $this->postId;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function authorId(): string
    {
        return $this->authorId;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setPostId(string $postId): void
    {
        if(empty($postId)){
            throw new \DomainException('Post ID cannot be empty.');
        }

        $this->postId = $postId;
    }

    private function setContent(string $content): void
    {
        if(strlen($content) >= Comment::MAX_CONTENT_LENGTH){
            throw new \DomainException(sprintf('Content cannot be more than %s characters.', Comment::MAX_CONTENT_LENGTH));
        }

        if(empty($content)){
            throw new \DomainException('Content cannot be empty.');
        }

        $this->content = $content;
    }

    private function setAuthorId(string $authorId): void
    {
        if(empty($authorId)){
            throw new \DomainException('Author ID cannot be empty.');
        }

        $this->authorId = $authorId;
    }
}

