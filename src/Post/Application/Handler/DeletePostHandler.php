<?php

namespace App\Post\Application\Handler;

use App\Post\Application\Command\DeletePostCommand;
use App\Post\Domain\Repository\PostRepositoryInterface;

class DeletePostHandler
{
    public function __construct(
        private PostRepositoryInterface $repository,

    )
    {}

    public function __invoke(DeletePostCommand $command): void
    {
        $post = $this->repository->findById($command->id);
        $this->repository->delete($post);
    }
}

