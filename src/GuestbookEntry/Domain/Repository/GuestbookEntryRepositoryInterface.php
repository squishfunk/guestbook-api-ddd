<?php

namespace App\GuestbookEntry\Domain\Repository;

use App\GuestbookEntry\Domain\Entity\GuestbookEntry;

interface GuestbookEntryRepositoryInterface
{
    public function findById(string $id): ?GuestbookEntry;

    /**
     * @return list<GuestbookEntry>
     */
    public function findAllPaginated(int $page, int $limit): array;
    public function save(GuestbookEntry $guestbookEntry): void;
    public function countAll(): int;
}
