<?php

namespace App\User\Domain\Entity;

use App\Shared\Domain\Event\EventInterface;
use App\User\Domain\Event\UserRegisteredEvent;
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

    /** @var list<string> */
    private array $roles = ['ROLE_USER'];
    private bool $emailVerified = false;
    private ?string $emailVerificationToken = null;

    /** @var list<EventInterface> */
    private array $events = [];

    public function __construct(
        string $name,
        Email $email,
        Password $password,
        ?UserId $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
        bool $emailVerified = false,
        ?string $emailVerificationToken = null
    ) {
        $this->id = $id ?? new UserId(Uuid::v4());
        $this->setName($name);
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->emailVerified = $emailVerified;
        $this->emailVerificationToken = $emailVerificationToken ?? bin2hex(random_bytes(32));
    }

    static function register(
        string $name,
        Email $email,
        Password $password,
    ): self {
        $user = new self(
            $name,
            $email,
            $password
        );

        $user->record(new UserRegisteredEvent(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->getEmailVerificationToken()
        ));

        return $user;
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

    public function confirmEmail(string $token): void
    {
        if ($this->emailVerified) {
            throw new \DomainException('Email is already verified.');
        }

        if ($this->emailVerificationToken !== $token) {
            throw new \DomainException('Invalid verification token.');
        }

        $this->emailVerified = true;
        $this->emailVerificationToken = null;
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

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        $this->roles = array_values(array_filter($this->roles, fn($r) => $r !== $role));
    }

    private function record(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @return list<EventInterface>
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
