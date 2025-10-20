<?php

namespace App\GuestbookEntry\Application\Command;

class CreateGuestbookEntryCommand
{
    public function __construct(
        public string $author,
        public string $message,
    ) {}
}
