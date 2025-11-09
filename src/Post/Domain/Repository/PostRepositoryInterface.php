<?php

namespace App\Post\Domain\Repository;

use App\Post\Domain\Entity\Post;

interface PostRepositoryInterface
{
    public function findById(string $id): ?Post;

    /**
     * @return list<Post>
     */
    public function findAllPaginated(int $page, int $limit): array;
    public function save(Post $post): void;
    public function countAll(): int;
}

