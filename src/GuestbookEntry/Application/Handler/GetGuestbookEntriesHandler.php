<?php

namespace App\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Application\ReadModel\GuestbookEntriesView;
use App\GuestbookEntry\Application\ReadModel\GuestbookEntryView;
use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class GetGuestbookEntriesHandler
{
    public function __construct(
        private GuestbookEntryRepositoryInterface $repository,

    )
    {}

    public function __invoke(int $page, int $limit): GuestbookEntriesView
    {
        $entries = $this->repository->findAllPaginated($page, $limit);

        $entriesCollectionResponse = [];
        foreach($entries as $entry) {
            $entriesCollectionResponse[] = new GuestbookEntryView(
                $entry->id(),
                $entry->author()->getDisplayName(),
                $entry->message(),
                $entry->createdAt()->format('Y-m-d H:i:s')
            );
        }

        $total = $this->repository->countAll();

        return new GuestbookEntriesView(
            $total,
            $page,
            $limit,
            $entriesCollectionResponse
        );
    }
}
