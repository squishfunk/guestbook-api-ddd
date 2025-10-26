<?php

namespace App\User\Domain\Event;

use App\Shared\Domain\Event\EventInterface;

class UserRegisteredEvent implements EventInterface
{
    public function __construct(
        private string $userId,
        private string $name,
        private string $email,
        private string $emailVerificationToken,
        private \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {

    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function emailVerificationToken(): string
    {
        return $this->emailVerificationToken;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

}
