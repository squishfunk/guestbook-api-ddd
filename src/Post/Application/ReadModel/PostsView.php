<?php

namespace App\Post\Application\ReadModel;

readonly class PostsView
{
    /**
     * @param list<PostView> $entries
     */
    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly array $entries
    )
    {}
}

