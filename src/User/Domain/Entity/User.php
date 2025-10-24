<?php

namespace App\User\Domain\Entity;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const MAX_NAME_LENGTH = 100;

    private UserId $id;
    private string $name;
    private Email $email;
    private Password $password;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private array $roles = ['ROLE_USER'];

    public function __construct(
        string $name,
        Email $email,
        Password $password,
        ?UserId $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id ?? new UserId(Uuid::v4());
        $this->setName($name);
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->setName($name);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updatePassword(Password $password): void
    {
        $this->password = $password;
        $this->updatedAt = new DateTimeImmutable();
    }

    private function setName(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new \DomainException('Name cannot be empty.');
        }

        if (strlen($name) > self::MAX_NAME_LENGTH) {
            throw new \DomainException(sprintf('Name cannot exceed %d characters.', self::MAX_NAME_LENGTH));
        }

        $this->name = $name;
    }

    // Symfony Security Interface methods
    public function getUserIdentifier(): string
    {
        return $this->email->value();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getPassword(): ?string
    {
        return $this->password->hash();
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        $this->roles = array_filter($this->roles, fn($r) => $r !== $role);
    }
}
