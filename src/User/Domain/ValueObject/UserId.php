<?php

namespace App\User\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

class UserId
{
    private string $value;

    public function __construct(string|Uuid $value)
    {
        if ($value instanceof Uuid) {
            $this->value = $value->toRfc4122();
        } else {
            if (!Uuid::isValid($value)) {
                throw new \DomainException('Invalid UUID format.');
            }
            $this->value = $value;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
