<?php

namespace App\GuestbookEntry\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'guestbook_entry')]
class GuestbookEntry
{
    const MAX_MESSAGE_LENGTH = 300;
    const MAX_AUTHOR_LENGTH = 64;
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[Groups(['guestbook'])]
    private string $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['guestbook'])]
    private string $author;

    #[ORM\Column(type: 'text')]
    #[Groups(['guestbook'])]
    private string $message;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['guestbook'])]
    private DateTimeImmutable $createdAt;

    public function __construct(string $author, string $message)
    {
        $this->id = Uuid::v1();
        $this->setAuthor($author);
        $this->setMessage($message);
        $this->createdAt = new DateTimeImmutable();
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function id(): string
    {
        return $this->id;
    }

    private function setMessage(string $message)
    {
        if(strlen($message) >= GuestbookEntry::MAX_MESSAGE_LENGTH){
            throw new \DomainException(sprintf('Message cannot be more than %s characters.', GuestbookEntry::MAX_MESSAGE_LENGTH));
        }

        if(empty($message)){
            throw new \DomainException('Message cannot be empty.');
        }

        $this->message = $message;
    }

    private function setAuthor(string $author)
    {
        if (trim($author) === '') {
            throw new \DomainException('Author cannot be empty.');
        }

        if(strlen($author) >= GuestbookEntry::MAX_AUTHOR_LENGTH){
            throw new \DomainException(sprintf('Author cannot have more than %s characters.', GuestbookEntry::MAX_AUTHOR_LENGTH));
        }

        $this->author = $author;
    }
}
