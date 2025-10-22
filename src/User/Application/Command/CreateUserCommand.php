<?php

namespace App\User\Application\Command;

class CreateUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
