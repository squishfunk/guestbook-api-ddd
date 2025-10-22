<?php

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class DoctrineUserRepository implements UserRepositoryInterface
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(DoctrineUser::class);
    }

    public function save(User $user): void
    {
        $doctrineUser = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($doctrineUser);
        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $doctrineUser = $this->repository->find($user->id()->value());
        if ($doctrineUser) {
            $this->entityManager->remove($doctrineUser);
            $this->entityManager->flush();
        }
    }

    public function findById(UserId $id): ?User
    {
        $doctrineUser = $this->repository->find($id->value());
        return $doctrineUser ? $doctrineUser->toDomain() : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $doctrineUser = $this->repository->findOneBy(['email' => $email->value()]);
        return $doctrineUser ? $doctrineUser->toDomain() : null;
    }

    public function existsByEmail(Email $email): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('count(u.id)')
            ->from(DoctrineUser::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function findAllPaginated(int $page, int $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(DoctrineUser::class, 'u')
            ->orderBy('u.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $doctrineUsers = $query->getResult();
        return array_map(fn($du) => $du->toDomain(), $doctrineUsers);
    }

    public function countAll(): int
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('count(u.id)')
            ->from(DoctrineUser::class, 'u')
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
