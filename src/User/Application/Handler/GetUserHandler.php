<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\GetUserCommand;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\UserId;

class GetUserHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(GetUserCommand $command): UserView
    {
        $userId = new UserId($command->id);
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new \DomainException('User not found.');
        }

        return new UserView(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->createdAt()->format('Y-m-d H:i:s'),
            $user->updatedAt()->format('Y-m-d H:i:s')
        );
    }
}
