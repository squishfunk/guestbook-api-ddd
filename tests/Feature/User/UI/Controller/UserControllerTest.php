<?php

declare(strict_types=1);

namespace App\Tests\Feature\User\UI\Controller;

use App\Tests\DataFixtures\UserFixtures;
use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Infrastructure\Persistence\DoctrineUser;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
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

    public function testListUsersAsAdmin(): void
    {
        // Create admin user
        $adminUser = new User(
            'Admin User',
            new Email('admin@test.com'),
            new Password('AdminPass123')
        );
        $adminUser->addRole('ROLE_ADMIN');

        $adminUserDoctrine = DoctrineUser::fromDomain($adminUser);
        $this->entityManager->persist($adminUserDoctrine);
        $this->entityManager->flush();

        // Login as admin
        $this->client->loginUser($adminUser);

        $this->client->request('GET', '/user');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertArrayHasKey('users', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('page', $responseData);
        $this->assertArrayHasKey('limit', $responseData);
    }

    public function testListUsersWithoutAdminRole(): void
    {
        // Create regular user
        $user = new User(
            'Regular User',
            new Email('user@test.com'),
            new Password('UserPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        // Login as regular user
        $this->client->loginUser($user);

        $this->client->request('GET', '/user');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testGetMe(): void
    {
        // Create user
        $user = new User(
            'Test User',
            new Email('test@test.com'),
            new Password('TestPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        // Login user
        $this->client->loginUser($user);

        $this->client->request('GET', '/user/me');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('Test User', $responseData['user']['name']);
        $this->assertEquals('test@test.com', $responseData['user']['email']);
    }

    public function testGetMeWithoutAuthentication(): void
    {
        $this->client->request('GET', '/user/me');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateMe(): void
    {
        // Create user
        $user = new User(
            'Original Name',
            new Email('original@test.com'),
            new Password('OriginalPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        // Login user
        $this->client->loginUser($user);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@test.com'
        ];

        $this->client->request(
            'PUT',
            '/user/me',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData) ?: ''
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('Updated Name', $responseData['user']['name']);
        $this->assertEquals('updated@test.com', $responseData['user']['email']);
    }

    public function testUpdateMePartial(): void
    {
        // Create user
        $user = new User(
            'Original Name',
            new Email('original@test.com'),
            new Password('OriginalPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        // Login user
        $this->client->loginUser($user);

        $updateData = [
            'name' => 'Updated Name Only'
        ];

        $this->client->request(
            'PATCH',
            '/user/me',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData) ?: ''
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertEquals('Updated Name Only', $responseData['user']['name']);
        $this->assertEquals('original@test.com', $responseData['user']['email']); // Email unchanged
    }

    public function testUpdateMeWithoutAuthentication(): void
    {
        $updateData = ['name' => 'Updated Name'];

        $this->client->request(
            'PUT',
            '/user/me',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData) ?: ''
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteMe(): void
    {
        // Create user
        $user = new User(
            'User To Delete',
            new Email('delete@test.com'),
            new Password('DeletePass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();
        $userId = $user->id()->value();

        // Login user
        $this->client->loginUser($user);

        $this->client->request('DELETE', '/user/');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertEquals('User deleted successfully', $responseData['message']);

        // Verify user is deleted
        $deletedUser = $this->entityManager->find(DoctrineUser::class, $userId);
        $this->assertNull($deletedUser);
    }

    public function testDeleteMeWithoutAuthentication(): void
    {
        $this->client->request('DELETE', '/user/');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListUsersWithPagination(): void
    {
        // Create admin user
        $adminUser = new User(
            'Admin User',
            new Email('admin@test.com'),
            new Password('AdminPass123')
        );
        $adminUser->addRole('ROLE_ADMIN');

        $adminUserDoctrine = DoctrineUser::fromDomain($adminUser);
        $this->entityManager->persist($adminUserDoctrine);
        $this->entityManager->flush();

        // Login as admin
        $this->client->loginUser($adminUser);

        $this->client->request('GET', '/user?page=1&limit=5');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertArrayHasKey('users', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('page', $responseData);
        $this->assertArrayHasKey('limit', $responseData);
    }
}
