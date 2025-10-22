<?php

namespace App\User\Domain\ValueObject;

class Password
{
    const MIN_PASSWORD_LENGTH = 6;
    private string $hashedValue;

    public function __construct(?string $plainPassword)
    {
        if($plainPassword){
            $this->validatePassword($plainPassword);
        }

        $this->hashedValue = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public static function fromHash(string $hashedPassword): self
    {
        $instance = new self(null);
        $instance->hashedValue = $hashedPassword;
        return $instance;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedValue);
    }

    public function hash(): string
    {
        return $this->hashedValue;
    }

    public function equals(Password $other): bool
    {
        return $this->hashedValue === $other->hashedValue;
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new \DomainException(sprintf('Password must be at least %s characters long.', self::MIN_PASSWORD_LENGTH));
        }

        if (strlen($password) > 128) {
            throw new \DomainException('Password cannot exceed 128 characters.');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new \DomainException('Password must contain at least one uppercase letter.');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new \DomainException('Password must contain at least one lowercase letter.');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \DomainException('Password must contain at least one number.');
        }
    }
}
