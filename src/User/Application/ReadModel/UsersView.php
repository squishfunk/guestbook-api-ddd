<?php

namespace App\User\Application\ReadModel;

readonly class UsersView
{
    /**
     * @param list<UserView> $users
     */
    public function __construct(
        public int $total,
        public int $page,
        public int $limit,
        public array $users
    )
    {
    }
}
