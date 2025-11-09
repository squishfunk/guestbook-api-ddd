<?php

namespace App\Post\Application\Handler;

use App\Post\Application\ReadModel\PostsView;
use App\Post\Application\ReadModel\PostView;
use App\Post\Domain\Repository\PostRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class GetPostsHandler
{
    public function __construct(
        private PostRepositoryInterface $repository,

    )
    {}

    public function __invoke(int $page, int $limit): PostsView
    {
        $posts = $this->repository->findAllPaginated($page, $limit);

        $postsCollectionResponse = [];
        foreach($posts as $post) {
            $postsCollectionResponse[] = new PostView(
                $post->id(),
                $post->author()->getDisplayName(),
                $post->message(),
                $post->createdAt()->format('Y-m-d H:i:s')
            );
        }

        $total = $this->repository->countAll();

        return new PostsView(
            $total,
            $page,
            $limit,
            $postsCollectionResponse
        );
    }
}

