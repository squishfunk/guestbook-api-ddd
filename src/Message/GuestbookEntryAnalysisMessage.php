<?php

namespace App\Message;

final class GuestbookEntryAnalysisMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */
    public function __construct(private int $entryId) {}

    public function getEntryId(): int
    {
        return $this->entryId;
    }

}
