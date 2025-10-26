<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\ConfirmEmailCommand;
use App\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ConfirmEmailHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(ConfirmEmailCommand $command): void
    {
        $user = $this->repository->findByEmailVerificationToken($command->token);
        
        if (!$user) {
            throw new \DomainException('Invalid verification token.');
        }

        $user->confirmEmail($command->token);
        $this->repository->save($user);
    }
}
