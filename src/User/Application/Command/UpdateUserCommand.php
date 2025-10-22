<?php

namespace App\User\Application\Command;

class UpdateUserCommand
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}
}
