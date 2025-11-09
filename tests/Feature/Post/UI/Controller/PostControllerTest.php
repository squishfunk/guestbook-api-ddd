<?php


declare(strict_types=1);

namespace App\Tests\Feature\Post\UI\Controller;

use App\Post\Domain\Entity\Post;
use App\Post\Domain\ValueObject\GuestAuthor;
use App\Post\Infrastructure\Persistence\DoctrinePost;
use App\Tests\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        $loader = new Loader();
        $loader->addFixture(new UserFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testCreatePost(): void
    {
        $createData = [
            'author' => 'Adam',
            'message' => 'Hello world!',
            'email' => 'adam@test.com',
        ];

        $this->client->request(
            'POST',
            '/posts',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($createData) ?: ''
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertEquals('Adam', $responseData['author']);
        $this->assertEquals('Hello world!', $responseData['message']);
    }

    public function testEditPost(): void
    {
        $post = new Post(
            new GuestAuthor('Adam'),
            'Hello world!',
        );

        $postDoctrine = DoctrinePost::fromDomain($post);
        $this->entityManager->persist($postDoctrine);
        $this->entityManager->flush();

        $id = $postDoctrine->getId();

        $editData = [
            'message' => 'Hello Poland!',
        ];

        $this->client->request(
            'PUT',
            '/posts/'.$id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($editData) ?: ''
        );


        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertEquals('Adam', $responseData['author']);
        $this->assertEquals('Hello Poland!', $responseData['message']);
    }
}
