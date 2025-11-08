<?php

namespace App\GuestbookEntry\Domain\Entity;

use App\GuestbookEntry\Domain\ValueObject\AuthorInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class GuestbookEntry
{
    const MAX_MESSAGE_LENGTH = 300;
    const MAX_AUTHOR_LENGTH = 64;

    private string $id;

    private AuthorInterface $author;

    private string $message;

    private DateTimeImmutable $createdAt;

    public function __construct(AuthorInterface $author, string $message, ?DateTimeImmutable $createdAt = null)
    {
        $this->id = Uuid::v1();
        $this->setAuthor($author);
        $this->setMessage($message);

        if($createdAt){
            $this->createdAt = $createdAt;
        }else{
            $this->createdAt = new DateTimeImmutable();
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

    private function setMessage(string $message): void
    {
        if(strlen($message) >= GuestbookEntry::MAX_MESSAGE_LENGTH){
            throw new \DomainException(sprintf('Message cannot be more than %s characters.', GuestbookEntry::MAX_MESSAGE_LENGTH));
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

        if(strlen($author->getDisplayName()) >= GuestbookEntry::MAX_AUTHOR_LENGTH){
            throw new \DomainException(sprintf('Author cannot have more than %s characters.', GuestbookEntry::MAX_AUTHOR_LENGTH));
        }

        $this->author = $author;
    }
}
