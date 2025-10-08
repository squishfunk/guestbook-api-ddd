<?php

namespace App\Controller;

use App\Entity\GuestbookEntry;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/guestbook')]
final class GuestbookController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        $entries = $entityManager->getRepository(GuestbookEntry::class)->findBy([], ['createdAt' => 'DESC']);

        $data = [];

        foreach ($entries as $entry) {
            $data[] = [
                'id' => $entry->getId(),
                'name' => $entry->getName(),
                'message' => $entry->getMessage(),
                'createdAt' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        if (!isset($requestContent['name'], $requestContent['message'])) {
            return new JsonResponse(['error' => 'Missing name or message'], 400);
        }

        $guestbookEntry = new GuestbookEntry();
        $guestbookEntry->setName($requestContent['name']);
        $guestbookEntry->setMessage($requestContent['message']);
        $entityManager->persist($guestbookEntry);
        $entityManager->flush();


        return new JsonResponse([
            'id' => $guestbookEntry->getId(),
            'name' => $guestbookEntry->getName(),
            'message' => $guestbookEntry->getMessage(),
            'createdAt' => $guestbookEntry->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }
}
