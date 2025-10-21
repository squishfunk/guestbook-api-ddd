<?php

namespace App\GuestbookEntry\Application\ReadModel;

readonly class GuestbookEntryView
{
    public function __construct(
        public string $id,
        public string $author,
        public string $message,
        public string $createdAt,
    )
    {}
}
