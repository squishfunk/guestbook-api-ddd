<?php

namespace App\Post\Infrastructure\Persistence;

use App\Post\Domain\Entity\Comment as DomainComment;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
class DoctrineComment
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: DoctrinePost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private DoctrinePost $post;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'string', length: 36)]
    private string $authorId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public static function fromDomain(DomainComment $comment, DoctrinePost $post): self
    {
        $self = new self();
        $self->id = $comment->id();
        $self->post = $post;
        $self->content = $comment->content();
        $self->authorId = $comment->authorId();
        $self->createdAt = $comment->createdAt();

        return $self;
    }

    public function toDomain(): DomainComment
    {
        return new DomainComment(
            $this->post->getId(),
            $this->content,
            $this->authorId,
            $this->createdAt,
            $this->id
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPost(): DoctrinePost
    {
        return $this->post;
    }
}

