<?php

namespace App\User\Application\Handler;

use App\User\Application\ReadModel\UsersView;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Repository\UserRepositoryInterface;

class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(int $page, int $limit): UsersView
    {
        $users = $this->repository->findAllPaginated($page, $limit);
        $total = $this->repository->countAll();

        $usersCollectionResponse = [];
        foreach ($users as $user) {
            $usersCollectionResponse[] = new UserView(
                $user->id()->value(),
                $user->name(),
                $user->email()->value(),
                $user->createdAt()->format('Y-m-d H:i:s'),
                $user->updatedAt()->format('Y-m-d H:i:s')
            );
        }

        return new UsersView(
            $total,
            $page,
            $limit,
            $usersCollectionResponse
        );
    }
}
