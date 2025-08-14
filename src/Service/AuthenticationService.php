<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class AuthenticationService
{
    public function __construct(
        private JWTService $jwtService,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {}

    public function getCurrentUser(?Request $request = null): ?User
    {
        $request ??= $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        $token = $this->extractTokenFromRequest($request);

        return $token ? $this->getUserFromToken($token) : null;
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->jwtService->validateToken($token);

        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        return $this->entityManager->getRepository(User::class)
            ->find($payload['user_id']);
    }

    public function extractTokenFromRequest(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return substr($authHeader, 7);
    }

    public function isAuthenticated(?Request $request = null): bool
    {
        return $this->getCurrentUser($request) !== null;
    }

    public function getCurrentUserId(?Request $request = null): ?int
    {
        $request ??= $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        $token = $this->extractTokenFromRequest($request);
        if (!$token) {
            return null;
        }
        /** @var array<string, int> $payload */
        $payload = $this->jwtService->validateToken($token);

        return $payload['user_id'] ?? null;
    }
}
