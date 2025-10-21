<?php

namespace App\GuestbookEntry\Infrastructure\Persistence;

use App\GuestbookEntry\Domain\Entity\GuestbookEntry as DomainGuestbookEntry;
use App\GuestbookEntry\Domain\ValueObject\AuthorInterface;
use App\GuestbookEntry\Domain\ValueObject\GuestAuthor;
use App\GuestbookEntry\Domain\ValueObject\RegisteredAuthor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'guestbook_entry')]
class DoctrineGuestbookEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 64)]
    private string $authorName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $authorEmail;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $userId;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public static function fromDomain(DomainGuestbookEntry $entry): self
    {
        $self = new self();
        $self->id = $entry->id();

        $author = $entry->author();
        if ($author instanceof GuestAuthor) {
            $self->authorName = $author->getDisplayName();
            $self->authorEmail = $author->getEmail();
            $self->userId = null;
        } elseif ($author instanceof RegisteredAuthor) {
            $self->authorName = $author->getDisplayName();
            $self->userId = $author->getUserId();
            $self->authorEmail = null;
        }

        $self->message = $entry->message();
        $self->createdAt = $entry->createdAt();

        return $self;
    }

    public function toDomain(): DomainGuestbookEntry
    {
        if ($this->userId !== null) {
            $author = new RegisteredAuthor($this->userId, $this->authorName);
        } else {
            $author = new GuestAuthor($this->authorName, $this->authorEmail);
        }

        return new DomainGuestbookEntry(
            $author,
            $this->message,
            $this->createdAt
        );
    }
}
