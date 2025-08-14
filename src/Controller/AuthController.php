<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTService $jwtService,
    ) {}

    #[Route('/api/auth/login', name: 'api_login', methods: ['POST'])]
    public function login(#[MapRequestPayload] RegisterUserDTO $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $request->email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $request->password)) {
            return new JsonResponse([
                'error' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtService->generateToken($user);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ]);
    }
}
