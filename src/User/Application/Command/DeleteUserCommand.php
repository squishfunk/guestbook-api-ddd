<?php

namespace App\User\Application\Command;

class DeleteUserCommand
{
    public function __construct(
        public string $id,
    ) {}
}
