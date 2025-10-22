<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(CreateUserCommand $command): UserView
    {
        $email = new Email($command->email);
        
        if ($this->repository->existsByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = new User(
            $command->name,
            $email,
            new Password($command->password)
        );

        $this->repository->save($user);

        return new UserView(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->createdAt()->format('Y-m-d H:i:s'),
            $user->updatedAt()->format('Y-m-d H:i:s')
        );
    }
}
