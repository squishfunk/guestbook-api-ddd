<?php

namespace App\Post\Application\Handler;

use App\Post\Application\Command\CreatePostCommand;
use App\Post\Application\ReadModel\PostView;
use App\Post\Domain\Entity\Post;
use App\Post\Domain\Repository\PostRepositoryInterface;
use App\Post\Domain\ValueObject\GuestAuthor;
use App\Post\Domain\ValueObject\RegisteredAuthor;

class CreatePostHandler
{
    public function __construct(
        private PostRepositoryInterface $repository
    ) {}

    public function __invoke(CreatePostCommand $command): PostView
    {

        if ($command->userId !== null) {
            $author = new RegisteredAuthor($command->userId, $command->author);
        } else {
            $author = new GuestAuthor($command->author, $command->email);
        }

        $post = new Post(
            $author,
            $command->message
        );
        $this->repository->save($post);

        return new PostView(
            $post->id(),
            $post->author()->getDisplayName(),
            $post->message(),
            $post->createdAt()->format('Y-m-d H:i:s')
        );
    }
}

