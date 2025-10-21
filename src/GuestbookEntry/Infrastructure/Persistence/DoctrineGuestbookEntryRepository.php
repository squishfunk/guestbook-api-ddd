<?php

namespace App\GuestbookEntry\Infrastructure\Persistence;

use App\GuestbookEntry\Domain\Entity\GuestbookEntry;
use App\GuestbookEntry\Domain\Repository\GuestbookEntryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class DoctrineGuestbookEntryRepository implements GuestbookEntryRepositoryInterface
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(DoctrineGuestbookEntry::class);
    }

    public function save(GuestbookEntry $entry): void
    {
        $doctrineEntry = DoctrineGuestbookEntry::fromDomain($entry);
        $this->entityManager->persist($doctrineEntry);
        $this->entityManager->flush();
    }

    /**
     * @return GuestbookEntry[]
     */
    public function findAllPaginated(int $page, $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(DoctrineGuestbookEntry::class, 'e')
            ->orderBy('e.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    public function countAll(): int
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('count(e.id)')
            ->from(DoctrineGuestbookEntry::class, 'e')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findById(string $id): ?GuestbookEntry
    {
        return $this->repository->find($id)?->toDomain();
    }
}
