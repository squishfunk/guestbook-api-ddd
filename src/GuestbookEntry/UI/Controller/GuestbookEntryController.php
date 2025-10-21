<?php

namespace App\GuestbookEntry\UI\Controller;

use App\GuestbookEntry\Application\Command\CreateGuestbookEntryCommand;
use App\GuestbookEntry\Application\Handler\CreateGuestbookEntryHandler;
use App\GuestbookEntry\Application\Handler\GetGuestbookEntriesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/guestbook', name: 'guestbook')]
final class GuestbookEntryController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
    ){}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateGuestbookEntryHandler $createGuestbookEntryHandler): JsonResponse
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format');
        }

        $command = $this->serializer->deserialize($request->getContent(), CreateGuestbookEntryCommand::class, 'json');

        $entryView = $createGuestbookEntryHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'guestbook_entry' => $entryView
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetGuestbookEntriesHandler $getGuestbookEntriesHandler): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $result = $getGuestbookEntriesHandler->__invoke($page, $limit);

        return new JsonResponse($result);
    }
}
