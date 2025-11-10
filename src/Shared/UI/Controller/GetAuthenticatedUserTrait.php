<?php

namespace App\Shared\UI\Controller;

use App\User\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @mixin AbstractController
 */
trait GetAuthenticatedUserTrait
{
    /**
     * @return User
     */
    protected function getAuthenticatedUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User is not authenticated or invalid user type.');
        }

        return $user;
    }
}
