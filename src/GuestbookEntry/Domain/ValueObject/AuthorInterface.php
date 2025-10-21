<?php

namespace App\GuestbookEntry\Domain\ValueObject;

interface AuthorInterface
{
    public function getDisplayName(): string;
}
