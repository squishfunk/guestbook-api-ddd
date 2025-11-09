<?php

namespace App\Post\Domain\ValueObject;

class GuestAuthor implements AuthorInterface
{

    use DisplayNameTrait;

    private ?string $email;

    public function __construct(
        string $displayName,
        ?string $email = null
    )
    {
        $this->setDisplayName($displayName);
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}

