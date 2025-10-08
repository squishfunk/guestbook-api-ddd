<?php

namespace App\MessageHandler;

use App\Message\GuestbookEntryAnalysisMessage;
use App\Repository\GuestbookEntryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class GuestbookEntryAnalysisMessageHandler
{

    public function __construct(
    ){}

    public function __invoke(
        GuestbookEntryAnalysisMessage $message,
    ): void
    {
        dd($message->getEntryId());
    }
}
