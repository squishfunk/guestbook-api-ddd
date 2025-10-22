<?php

namespace App\User\Application\ReadModel;

readonly class UserView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
