<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserControllerTest extends BaseTestCase
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

    public function testRegisterUser(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('message', $responseData);
        self::assertArrayHasKey('user_id', $responseData);
        self::assertEquals('User created successfully', $responseData['message']);

        // Verify user was created in database
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'test@example.com']);

        self::assertNotNull($user);
        self::assertEquals('test@example.com', $user->getEmail());
    }

    public function testRegisterUserWithMissingEmail(): void
    {
        $userData = ['password' => 'password123'];

        $result = $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterUserWithMissingPassword(): void
    {
        $userData = ['email' => 'test@example.com'];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterDuplicateUser(): void
    {
        // Create first user
        $user = new User();
        $user->setEmail('existing@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Try to create duplicate user
        $userData = [
            'email' => 'existing@example.com',
            'password' => 'password123',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertEquals('User with this email already exists', $responseData['error']);
    }

    public function testRegisterWithInvalidEmail(): void
    {
        $userData = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
