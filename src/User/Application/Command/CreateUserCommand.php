<?php

namespace App\User\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $password,
    ) {}
}
