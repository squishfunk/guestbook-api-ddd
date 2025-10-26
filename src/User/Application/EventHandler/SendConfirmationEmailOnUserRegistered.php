<?php

namespace App\User\Application\EventHandler;

use App\User\Domain\Event\UserRegisteredEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendConfirmationEmailOnUserRegistered
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        // TODO: Implement email verification sending
        // This would typically:
        // 1. Generate verification link with token
        // 2. Send email with verification link
        // 3. Log the action

        // For now, just log that verification email should be sent
        error_log(sprintf(
            'Email verification should be sent to: %s for user: %s with token: %s',
            $event->email(),
            $event->userId(),
            $event->emailVerificationToken()
        ));
    }
}
