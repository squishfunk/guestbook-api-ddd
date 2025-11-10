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

    public function update(Post $post): void
    {
        $doctrinePost = $this->repository->find($post->id());

        if (!$doctrinePost) {
            throw new \RuntimeException('Post not found');
        }

        $doctrinePost->setMessage($post->message());

        // Update comments
        $existingCommentIds = [];
        foreach ($doctrinePost->getComments() as $existingComment) {
            $existingCommentIds[] = $existingComment->getId();
        }

        $domainCommentIds = [];
        foreach ($post->comments() as $domainComment) {
            $domainCommentIds[] = $domainComment->id();
        }

        // Remove comments that are no longer in the domain
        foreach ($doctrinePost->getComments() as $existingComment) {
            if (!in_array($existingComment->getId(), $domainCommentIds)) {
                $this->entityManager->remove($existingComment);
            }
        }

        // Add or update comments
        foreach ($post->comments() as $domainComment) {
            $existingComment = null;
            foreach ($doctrinePost->getComments() as $comment) {
                if ($comment->getId() === $domainComment->id()) {
                    $existingComment = $comment;
                    break;
                }
            }

            if ($existingComment === null) {
                $newComment = DoctrineComment::fromDomain($domainComment, $doctrinePost);
                $doctrinePost->getComments()->add($newComment);
                $this->entityManager->persist($newComment);
            }
        }

        $this->entityManager->flush();
    }

    public function save(Post $post): void
    {
        $existingDoctrinePost = $this->repository->find($post->id());
        
        if ($existingDoctrinePost) {
            // Update existing post
            $this->update($post);
            return;
        }

        $doctrinePost = DoctrinePost::fromDomain($post);
        $this->entityManager->persist($doctrinePost);
        
        // Persist comments
        foreach ($doctrinePost->getComments() as $comment) {
            $this->entityManager->persist($comment);
        }
        
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
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
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
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(DoctrinePost::class, 'p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $doctrinePost = $query->getOneOrNullResult();

        return $doctrinePost?->toDomain();
    }

    public function delete(Post $post): void
    {
        $doctrinePost = $this->repository->find($post->id());

        if (!$doctrinePost) {
            throw new \RuntimeException('Post not found');
        }

        $this->entityManager->remove($doctrinePost);
        $this->entityManager->flush();
    }
}

