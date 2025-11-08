<?php

namespace App\User\Application\ReadModel;
use App\GuestbookEntry\Application\ReadModel\GuestbookEntryView;

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
