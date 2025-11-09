<?php

namespace App\Post\Domain\ValueObject;

use DomainException;

trait DisplayNameTrait
{
    public const MAX_AUTHOR_LENGTH = 64;

    private string $displayName;

    /**
     * @param string $displayName
     * @throws DomainException jeśli displayName jest pusty lub za długi
     */
    private function setDisplayName(string $displayName): void
    {
        $displayName = trim($displayName);

        if ($displayName === '') {
            throw new DomainException('Display name cannot be empty.');
        }

        if (strlen($displayName) > self::MAX_AUTHOR_LENGTH) {
            throw new DomainException(sprintf('Display name cannot exceed %s characters.', self::MAX_AUTHOR_LENGTH));
        }

        $this->displayName = $displayName;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}

