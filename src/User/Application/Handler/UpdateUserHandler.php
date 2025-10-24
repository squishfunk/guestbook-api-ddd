<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\UserId;

class UpdateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(UpdateUserCommand $command): UserView
    {
        $userId = new UserId($command->id);
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new \DomainException('User not found.');
        }

        if ($command->name !== null) {
            $user->updateName($command->name);
        }

        if ($command->email !== null) {
            $email = new Email($command->email);
            if ($this->repository->existsByEmail($email) && !$user->email()->equals($email)) {
                throw new \DomainException('User with this email already exists.');
            }
            $user->updateEmail($email);
        }

        if ($command->password !== null) {
            $user->updatePassword(new Password($command->password));
        }

        $this->repository->update($user);

        return new UserView(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->createdAt()->format('Y-m-d H:i:s'),
            $user->updatedAt()->format('Y-m-d H:i:s')
        );
    }
}
