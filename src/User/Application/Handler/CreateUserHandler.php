<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private MessageBusInterface $bus
    ) {}

    public function __invoke(CreateUserCommand $command): UserView
    {
        $email = new Email($command->email);

        if ($this->repository->existsByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::register($command->name, $email, new Password($command->password));

        $this->repository->save($user);

        foreach ($user->releaseEvents() as $event) {
            $this->bus->dispatch($event);
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
