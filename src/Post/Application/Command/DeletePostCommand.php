<?php

namespace App\Post\Application\Command;

class DeletePostCommand
{
    public function __construct(
        public string $id,
    ) {}
}

