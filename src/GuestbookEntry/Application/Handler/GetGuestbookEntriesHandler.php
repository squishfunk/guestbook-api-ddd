<?php

namespace App\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class GetGuestbookEntriesHandler
{
    public function __construct(
        private GuestbookEntryRepositoryInterface $repository,

    )
    {}

    public function __invoke(int $page, int $limit): iterable
    {
        $entries = $this->repository->findAllPaginated($page, $limit);
        $total = $this->repository->countAll();

        return [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'entries' => $entries
        ];
    }
}
