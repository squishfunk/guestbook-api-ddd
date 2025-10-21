<?php

namespace App\GuestbookEntry\Application\Handler;

use App\GuestbookEntry\Application\ReadModel\GuestbookEntryView;
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


        $entriesCollectionResponse = [];
        foreach($entries as $doctrineEntry) {
            $entry = $doctrineEntry->toDomain();
            $entriesCollectionResponse[] = new GuestbookEntryView(
                $entry->id(),
                $entry->author()->getDisplayName(),
                $entry->message(),
                $entry->createdAt()->format('Y-m-d H:i:s')
            );
        }

        $total = $this->repository->countAll();

        return [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'entries' => $entriesCollectionResponse
        ];
    }
}
