<?php

namespace App\Post\Application\Handler;

use App\Post\Application\Command\UpdatePostCommand;
use App\Post\Application\ReadModel\PostView;
use App\Post\Domain\Entity\Post;
use App\Post\Domain\Repository\PostRepositoryInterface;

class UpdatePostHandler
{
    public function __construct(
        private PostRepositoryInterface $repository
    ) {}

    public function __invoke(UpdatePostCommand $command): PostView
    {

        $post = $this->repository->findById($command->id);

        $post->updateMessage($command->message);

        $this->repository->save($post);

        return new PostView(
            $post->id(),
            $post->author()->getDisplayName(),
            $post->message(),
            $post->createdAt()->format('Y-m-d H:i:s')
        );
    }
}

