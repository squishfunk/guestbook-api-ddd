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

class AuthControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        // Load fixtures
        $loader = new Loader();
        $loader->addFixture(new UserFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testRegisterSuccess(): void
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'NewPass123'
        ];

        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData) ?: ''
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('User registered successfully, please confirm your email address.', $responseData['message']);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('New User', $responseData['user']['name']);
        $this->assertEquals('newuser@test.com', $responseData['user']['email']);
    }

    public function testRegisterWithInvalidData(): void
    {
        $userData = [
            'name' => '', // Empty name
            'email' => 'invalid-email', // Invalid email
            'password' => '123' // Too short password
        ];

        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData) ?: ''
        );

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterWithMissingFields(): void
    {
        $userData = [
            'name' => 'Test User'
            // Missing email and password
        ];

        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData) ?: ''
        );

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterWithDuplicateEmail(): void
    {
        // First registration
        $userData = [
            'name' => 'First User',
            'email' => 'duplicate@test.com',
            'password' => 'FirstPass123'
        ];

        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData) ?: ''
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        // Try to register with same email
        $duplicateUserData = [
            'name' => 'Second User',
            'email' => 'duplicate@test.com',
            'password' => 'SecondPass123'
        ];

        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($duplicateUserData) ?: ''
        );

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginSuccess(): void
    {
        // Create user first
        $user = new User(
            'Login Test User',
            new Email('login@test.com'),
            new Password('LoginPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        $loginData = [
            'email' => 'login@test.com',
            'password' => 'LoginPass123'
        ];

        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData) ?: ''
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Login successful', $responseData['message']);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('Login Test User', $responseData['user']['name']);
        $this->assertEquals('login@test.com', $responseData['user']['email']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        // Create user first
        $user = new User(
            'Login Test User',
            new Email('login@test.com'),
            new Password('LoginPass123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);
        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();

        $loginData = [
            'email' => 'login@test.com',
            'password' => 'WrongPassword'
        ];

        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData) ?: ''
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }

    public function testLoginWithNonExistentUser(): void
    {
        $loginData = [
            'email' => 'nonexistent@test.com',
            'password' => 'SomePassword'
        ];

        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData) ?: ''
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }

    public function testLoginWithMissingFields(): void
    {
        $loginData = [
            'email' => 'test@test.com'
            // Missing password
        ];

        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData) ?: ''
        );


        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginWithEmptyFields(): void
    {
        $loginData = [
            'email' => '',
            'password' => ''
        ];

        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData) ?: ''
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Login failed', $responseData['message']);
    }

    public function testLogout(): void
    {
        $this->client->request(
            'POST',
            '/auth/logout',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLoginWithInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Email and password are required', $responseData['message']);
    }

    public function testRegisterWithInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/auth/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json'
        );

        $responseData = json_decode($this->client->getResponse()->getContent() ?: '', true);

        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}
