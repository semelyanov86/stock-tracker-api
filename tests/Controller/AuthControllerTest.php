<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthControllerTest extends BaseTestCase
{
    private $client;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testSuccessfulLogin(): void
    {
        // Create test user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $this->client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('user', $responseData);
        self::assertIsString($responseData['token']);
        self::assertEquals('test@example.com', $responseData['user']['email']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ];

        $this->client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('error', $responseData);
        self::assertEquals('Invalid credentials', $responseData['error']);
    }

    public function testLoginWithWrongPassword(): void
    {
        // Create test user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $this->client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginWithMissingCredentials(): void
    {
        $loginData = ['email' => 'test@example.com'];

        $this->client->request(
            'POST',
            '/api/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
