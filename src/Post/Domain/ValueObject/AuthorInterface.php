<?php

namespace App\Post\Domain\ValueObject;

interface AuthorInterface
{
    public function getDisplayName(): string;
}

