<?php

namespace App\Post\Application\ReadModel;

readonly class PostView
{
    public function __construct(
        public string $id,
        public string $author,
        public string $message,
        public string $createdAt,
    )
    {}
}

