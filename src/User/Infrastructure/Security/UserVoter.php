<?php

namespace App\User\Infrastructure\Security;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\UserId;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && ($subject instanceof User || $subject instanceof UserId || is_string($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $targetUserId = $this->extractUserId($subject);
        if (!$targetUserId) {
            return false;
        }

        return match ($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $user->id()->value() === $targetUserId,
            default => false,
        };
    }

    private function extractUserId(mixed $subject): ?string
    {
        if ($subject instanceof User) {
            return $subject->id()->value();
        }

        if ($subject instanceof UserId) {
            return $subject->value();
        }

        if (is_string($subject)) {
            return $subject;
        }

        return null;
    }
}



