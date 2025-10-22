<?php

namespace App\User\Application\Command;

class GetUserCommand
{
    public function __construct(
        public string $id,
    ) {}
}
