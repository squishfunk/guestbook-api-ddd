<?php

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\Entity\User as DomainUser;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class DoctrineUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $emailVerified = false;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $emailVerificationToken = null;

    public static function fromDomain(DomainUser $user): self
    {
        $self = new self();
        $self->id = $user->id()->value();
        $self->name = $user->name();
        $self->email = $user->email()->value();
        $self->password = $user->password()->hash();
        $self->createdAt = $user->createdAt();
        $self->updatedAt = $user->updatedAt();
        $self->emailVerified = $user->isEmailVerified();
        $self->emailVerificationToken = $user->getEmailVerificationToken();

        return $self;
    }

    public function toDomain(): DomainUser
    {
        return new DomainUser(
            $this->name,
            new Email($this->email),
            Password::fromHash($this->password),
            new UserId($this->id),
            $this->createdAt,
            $this->updatedAt,
            $this->emailVerified,
            $this->emailVerificationToken
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): void
    {
        $this->emailVerificationToken = $emailVerificationToken;
    }
}
