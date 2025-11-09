<?php

namespace App\Post\Infrastructure\Persistence;

use App\Post\Domain\Entity\Post as DomainPost;
use App\Post\Domain\ValueObject\AuthorInterface;
use App\Post\Domain\ValueObject\GuestAuthor;
use App\Post\Domain\ValueObject\RegisteredAuthor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'post')]
class DoctrinePost
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

    public static function fromDomain(DomainPost $post): self
    {
        $self = new self();
        $self->id = $post->id();

        $author = $post->author();
        if ($author instanceof GuestAuthor) {
            $self->authorName = $author->getDisplayName();
            $self->authorEmail = $author->getEmail();
            $self->userId = null;
        } elseif ($author instanceof RegisteredAuthor) {
            $self->authorName = $author->getDisplayName();
            $self->userId = $author->getUserId();
            $self->authorEmail = null;
        }

        $self->message = $post->message();
        $self->createdAt = $post->createdAt();

        return $self;
    }

    public function toDomain(): DomainPost
    {
        if ($this->userId !== null) {
            $author = new RegisteredAuthor($this->userId, $this->authorName);
        } else {
            $author = new GuestAuthor($this->authorName, $this->authorEmail);
        }

        return new DomainPost(
            $author,
            $this->message,
            $this->createdAt
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}

