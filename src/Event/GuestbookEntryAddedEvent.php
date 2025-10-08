<?php
namespace App\Event;

use App\Entity\GuestbookEntry;

class GuestbookEntryAddedEvent
{
    public function __construct(
        private GuestbookEntry $entry
    ) {}

    public function getEntry(): GuestbookEntry
    {
        return $this->entry;
    }
}
