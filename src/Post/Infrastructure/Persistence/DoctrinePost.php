<?php

namespace App\Post\Infrastructure\Persistence;

use App\Post\Domain\Entity\Comment as DomainComment;
use App\Post\Domain\Entity\Post as DomainPost;
use App\Post\Domain\ValueObject\AuthorInterface;
use App\Post\Domain\ValueObject\GuestAuthor;
use App\Post\Domain\ValueObject\RegisteredAuthor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, DoctrineComment>
     */
    #[ORM\OneToMany(targetEntity: DoctrineComment::class, mappedBy: 'post', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

        foreach ($post->comments() as $comment) {
            $self->comments->add(DoctrineComment::fromDomain($comment, $self));
        }

        return $self;
    }

    public function toDomain(): DomainPost
    {
        if ($this->userId !== null) {
            $author = new RegisteredAuthor($this->userId, $this->authorName);
        } else {
            $author = new GuestAuthor($this->authorName, $this->authorEmail);
        }

        $comments = [];
        foreach ($this->comments as $comment) {
            $comments[] = $comment->toDomain();
        }

        return new DomainPost(
            $author,
            $this->message,
            $this->createdAt,
            $this->id,
            $comments
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

    /**
     * @return Collection<int, DoctrineComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}

