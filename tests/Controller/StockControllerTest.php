<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\StockQuery;
use App\Entity\User;
use App\Service\JWTService;
use App\Tests\BaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class StockControllerTest extends BaseTestCase
{
    private $client;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    private JWTService $jwtService;

    private User $testUser;

    private string $token;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->jwtService = self::getContainer()->get(JWTService::class);

        // Create test user and generate token
        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        $this->testUser->setPassword($this->passwordHasher->hashPassword($this->testUser, 'password123'));

        $this->entityManager->persist($this->testUser);
        $this->entityManager->flush();

        $this->token = $this->jwtService->generateToken($this->testUser);
    }

    public function testGetStockQuoteSuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/stock?q=IBM',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('name', $responseData);
        self::assertArrayHasKey('symbol', $responseData);
        self::assertArrayHasKey('open', $responseData);
        self::assertArrayHasKey('high', $responseData);
        self::assertArrayHasKey('low', $responseData);
        self::assertArrayHasKey('close', $responseData);

        // Verify stock query was saved to database
        $stockQueries = $this->entityManager->getRepository(StockQuery::class)
            ->findBy(['user' => $this->testUser]);
        self::assertCount(1, $stockQueries);
        self::assertEquals('IBM', $stockQueries[0]->getSymbol());
    }

    public function testGetStockQuoteWithoutAuthentication(): void
    {
        $this->client->request('GET', '/api/stock?q=IBM');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertEquals('Unauthorized', $responseData['error']);
    }

    public function testGetStockQuoteWithInvalidToken(): void
    {
        $this->client->request(
            'GET',
            '/api/stock?q=IBM',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetStockQuoteWithoutSymbol(): void
    {
        $this->client->request(
            'GET',
            '/api/stock',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertEquals('Stock symbol parameter "q" is required', $responseData['error']);
    }

    public function testGetStockHistory(): void
    {
        // Create some test stock queries
        $query1 = new StockQuery();
        $query1->setUser($this->testUser);
        $query1->setSymbol('IBM');
        $query1->setName('International Business Machines');
        $query1->setOpen(123.66);
        $query1->setHigh(125.50);
        $query1->setLow(122.49);
        $query1->setClose(123.00);
        $query1->setDate(new \DateTimeImmutable('-1 hour'));

        $query2 = new StockQuery();
        $query2->setUser($this->testUser);
        $query2->setSymbol('AAPL');
        $query2->setName('Apple Inc.');
        $query2->setOpen(150.00);
        $query2->setHigh(152.30);
        $query2->setLow(148.75);
        $query2->setClose(151.20);
        $query2->setDate(new \DateTimeImmutable('-2 hours'));

        $this->entityManager->persist($query1);
        $this->entityManager->persist($query2);
        $this->entityManager->flush();

        $this->client->request(
            'GET',
            '/api/history',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertIsArray($responseData);
        self::assertCount(2, $responseData);

        // Check that results are ordered by date (newest first)
        self::assertEquals('IBM', $responseData[0]['symbol']);
        self::assertEquals('AAPL', $responseData[1]['symbol']);

        // Check structure of each entry
        foreach ($responseData as $entry) {
            self::assertArrayHasKey('date', $entry);
            self::assertArrayHasKey('name', $entry);
            self::assertArrayHasKey('symbol', $entry);
            self::assertArrayHasKey('open', $entry);
            self::assertArrayHasKey('high', $entry);
            self::assertArrayHasKey('low', $entry);
            self::assertArrayHasKey('close', $entry);
        }
    }

    public function testGetStockHistoryWithoutAuthentication(): void
    {
        $this->client->request('GET', '/api/history');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetEmptyStockHistory(): void
    {
        $this->client->request(
            'GET',
            '/api/history',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertIsArray($responseData);
        self::assertEmpty($responseData);
    }
}
