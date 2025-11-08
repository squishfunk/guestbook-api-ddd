<?php

namespace App\GuestbookEntry\Application\ReadModel;

readonly class GuestbookEntriesView
{
    /**
     * @param list<GuestbookEntryView> $entries
     */
    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly array $entries
    )
    {}
}
