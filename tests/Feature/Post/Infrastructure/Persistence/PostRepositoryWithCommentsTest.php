<?php

declare(strict_types=1);

namespace App\Tests\Feature\Post\Infrastructure\Persistence;

use App\Post\Domain\Entity\Comment;
use App\Post\Domain\Entity\Post;
use App\Post\Domain\ValueObject\GuestAuthor;
use App\Post\Infrastructure\Persistence\DoctrinePost;
use App\Post\Infrastructure\Persistence\DoctrinePostRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Tests\DataFixtures\UserFixtures;

class PostRepositoryWithCommentsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrinePostRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        $this->repository = new DoctrinePostRepository($entityManager);

        $loader = new Loader();
        $loader->addFixture(new UserFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testCanSavePostWithComments(): void
    {
        $author = new GuestAuthor('Test Author', 'test@example.com');
        $post = new Post($author, 'Test message');

        $comment1 = new Comment($post->id(), 'First comment', 'author-1');
        $comment2 = new Comment($post->id(), 'Second comment', 'author-2');

        $post->addComment($comment1);
        $post->addComment($comment2);

        $this->repository->save($post);

        $savedPost = $this->repository->findById($post->id());

        $this->assertNotNull($savedPost);
        $this->assertSame($post->id(), $savedPost->id());
        $this->assertCount(2, $savedPost->comments());

        $savedComments = $savedPost->comments();
        $this->assertSame('First comment', $savedComments[0]->content());
        $this->assertSame('Second comment', $savedComments[1]->content());
    }

    public function testCanAddCommentToExistingPost(): void
    {
        $author = new GuestAuthor('Test Author', 'test@example.com');
        $post = new Post($author, 'Test message');

        $this->repository->save($post);

        $savedPost = $this->repository->findById($post->id());
        $this->assertNotNull($savedPost);

        $newComment = new Comment($savedPost->id(), 'New comment', 'author-3');
        $savedPost->addComment($newComment);

        $this->repository->save($savedPost);

        $updatedPost = $this->repository->findById($post->id());
        $this->assertNotNull($updatedPost);
        $this->assertCount(1, $updatedPost->comments());
        $this->assertSame('New comment', $updatedPost->comments()[0]->content());
    }

    public function testCanRetrievePostWithComments(): void
    {
        $author = new GuestAuthor('Test Author', 'test@example.com');
        $post = new Post($author, 'Test message');

        $comment1 = new Comment($post->id(), 'Comment 1', 'author-1');
        $comment2 = new Comment($post->id(), 'Comment 2', 'author-2');

        $post->addComment($comment1);
        $post->addComment($comment2);

        $this->repository->save($post);

        $retrievedPost = $this->repository->findById($post->id());

        $this->assertNotNull($retrievedPost);
        $this->assertCount(2, $retrievedPost->comments());

        $comments = $retrievedPost->comments();
        $this->assertSame('Comment 1', $comments[0]->content());
        $this->assertSame('author-1', $comments[0]->authorId());
        $this->assertSame('Comment 2', $comments[1]->content());
        $this->assertSame('author-2', $comments[1]->authorId());
    }

    public function testCommentsAreDeletedWhenPostIsDeleted(): void
    {
        $author = new GuestAuthor('Test Author', 'test@example.com');
        $post = new Post($author, 'Test message');

        $comment = new Comment($post->id(), 'Test comment', 'author-1');
        $post->addComment($comment);

        $this->repository->save($post);

        $this->repository->delete($post);

        $deletedPost = $this->repository->findById($post->id());
        $this->assertNull($deletedPost);

        // Verify comment is also deleted
        $commentRepository = $this->entityManager->getRepository(\App\Post\Infrastructure\Persistence\DoctrineComment::class);
        $deletedComment = $commentRepository->find($comment->id());
        $this->assertNull($deletedComment);
    }

    public function testCanFindAllPostsWithComments(): void
    {
        $author1 = new GuestAuthor('Author 1', 'author1@example.com');
        $post1 = new Post($author1, 'Post 1');
        $comment1 = new Comment($post1->id(), 'Comment on post 1', 'author-1');
        $post1->addComment($comment1);

        $author2 = new GuestAuthor('Author 2', 'author2@example.com');
        $post2 = new Post($author2, 'Post 2');
        $comment2 = new Comment($post2->id(), 'Comment on post 2', 'author-2');
        $post2->addComment($comment2);

        $this->repository->save($post1);
        $this->repository->save($post2);

        $posts = $this->repository->findAllPaginated(1, 10);

        $this->assertGreaterThanOrEqual(2, count($posts));

        $foundPost1 = null;
        $foundPost2 = null;
        foreach ($posts as $post) {
            if ($post->id() === $post1->id()) {
                $foundPost1 = $post;
            }
            if ($post->id() === $post2->id()) {
                $foundPost2 = $post;
            }
        }

        $this->assertNotNull($foundPost1);
        $this->assertNotNull($foundPost2);
        $this->assertCount(1, $foundPost1->comments());
        $this->assertCount(1, $foundPost2->comments());
    }
}

