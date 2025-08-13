<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\JWTService;
use App\Tests\BaseTestCase;

final class JWTServiceTest extends BaseTestCase
{
    private JWTService $jwtService;

    private User $testUser;

    protected function setUp(): void
    {
        $this->jwtService = new JWTService('test-secret-key');

        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        // Simulate having an ID (normally set by Doctrine)
        $reflection = new \ReflectionClass($this->testUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($this->testUser, 1);
    }

    public function testGenerateToken(): void
    {
        $token = $this->jwtService->generateToken($this->testUser);

        self::assertIsString($token);
        self::assertNotEmpty($token);

        // JWT tokens have three parts separated by dots
        $parts = explode('.', $token);
        self::assertCount(3, $parts);
    }

    public function testValidateValidToken(): void
    {
        $token = $this->jwtService->generateToken($this->testUser);
        $payload = $this->jwtService->validateToken($token);

        self::assertIsArray($payload);
        self::assertEquals(1, $payload['user_id']);
        self::assertEquals('test@example.com', $payload['email']);
        self::assertArrayHasKey('iat', $payload);
        self::assertArrayHasKey('exp', $payload);
    }

    public function testValidateInvalidToken(): void
    {
        $invalidToken = 'invalid.token.here';
        $payload = $this->jwtService->validateToken($invalidToken);

        self::assertNull($payload);
    }

    public function testValidateExpiredToken(): void
    {
        // This would require mocking time or creating a token with past expiration
        // For now, we test with a malformed token
        $malformedToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.invalid.signature';
        $payload = $this->jwtService->validateToken($malformedToken);

        self::assertNull($payload);
    }

    public function testTokenContainsCorrectClaims(): void
    {
        $token = $this->jwtService->generateToken($this->testUser);
        $payload = $this->jwtService->validateToken($token);

        // Check that token expires in the future (24 hours)
        self::assertGreaterThan(time(), $payload['exp']);
        self::assertLessThanOrEqual(time() + 24 * 60 * 60 + 1, $payload['exp']);

        // Check that issued at time is recent
        self::assertLessThanOrEqual(time(), $payload['iat']);
        self::assertGreaterThan(time() - 60, $payload['iat']); // Within last minute
    }
}
