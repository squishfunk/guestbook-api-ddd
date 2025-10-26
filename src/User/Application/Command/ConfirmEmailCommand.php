<?php

namespace App\User\Application\Command;

class ConfirmEmailCommand
{
    public function __construct(
        public readonly string $token
    ) {}
}
