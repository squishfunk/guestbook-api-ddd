<?php

namespace App\Post\Application\Command;

class CreatePostCommand
{
    public function __construct(
        public string $author,
        public string $message,
        public ?string $email = null,
        public ?string $userId = null,
    ) {}
}

