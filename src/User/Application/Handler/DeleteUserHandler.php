<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\UserId;

class DeleteUserHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = new UserId($command->id);
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new \DomainException('User not found.');
        }

        $this->repository->delete($user);
    }
}
