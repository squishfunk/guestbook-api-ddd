<?php

namespace App\Post\Application\Command;

class UpdatePostCommand
{
    public function __construct(
        public string $id,
        public string $message
    ) {}
}

