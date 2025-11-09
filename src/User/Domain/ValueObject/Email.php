<?php

namespace App\User\Domain\ValueObject;

class Email
{
    /**
     * @var non-empty-string
     */
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new \DomainException('Email cannot be empty.');
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email format.');
        }

        if (strlen($value) > 255) {
            throw new \DomainException('Email cannot exceed 255 characters.');
        }

        $this->value = strtolower($value);
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
