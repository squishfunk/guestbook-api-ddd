<?php

namespace App\Post\Infrastructure\Persistence;

use App\Post\Domain\Entity\Post;
use App\Post\Domain\Repository\PostRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class DoctrinePostRepository implements PostRepositoryInterface
{
    /**
     * @var ObjectRepository<DoctrinePost>
     */
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(DoctrinePost::class);
    }

    public function save(Post $post): void
    {
        $doctrinePost = DoctrinePost::fromDomain($post);
        $this->entityManager->persist($doctrinePost);
        $this->entityManager->flush();
    }

    /**
     * @return list<Post>
     */
    public function findAllPaginated(int $page, int $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(DoctrinePost::class, 'p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $doctrinePosts = $query->getResult();

        return array_map(
        fn(DoctrinePost $doctrinePost) => $doctrinePost->toDomain(),
        $doctrinePosts
        );
    }

    public function countAll(): int
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('count(p.id)')
            ->from(DoctrinePost::class, 'p')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findById(string $id): ?Post
    {
        return $this->repository->find($id)?->toDomain();
    }
}

