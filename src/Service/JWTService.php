<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final readonly class JWTService
{
    public function __construct(
        private string $secret,
        private string $algorithm = 'HS256',
    ) {}

    public function generateToken(User $user): string
    {
        $payload = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 часа
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

            return (array) $decoded;
        } catch (\Exception) {
            return null;
        }
    }
}
