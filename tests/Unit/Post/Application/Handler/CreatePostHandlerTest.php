<?php

namespace App\Tests\Unit\Post\Application\Handler;

use App\Post\Application\Command\CreatePostCommand;
use App\Post\Application\Handler\CreatePostHandler;
use App\Post\Domain\Entity\Post;
use App\Post\Domain\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreatePostHandlerTest extends TestCase
{
    public function testHandlerCreatesAndSavesPost(): void
    {
        $mockRepo = $this->createMock(PostRepositoryInterface::class);
        $mockRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Post $post) {
                    return $post->author()->getDisplayName() === 'KubaDDD'
                    && $post->message() === 'Hello World';
            }));

        $command = new CreatePostCommand(
            'KubaDDD',
            'Hello World'
        );

        $handler = new CreatePostHandler($mockRepo);
        $handler($command);
    }
}

