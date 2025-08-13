<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\StockQuery;
use App\Entity\User;
use App\Message\SendEmailMessage;
use App\Service\JWTService;
use App\Service\StockDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class StockController extends AbstractController
{
    public function __construct(
        private readonly StockDataProviderInterface $stockDataProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTService $jwtService,
        private readonly MessageBusInterface $messageBus,
    ) {}

    #[Route('/api/stock', name: 'api_stock_quote', methods: ['GET'])]
    public function getStockQuote(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $symbol = $request->query->get('q');
        if (!$symbol) {
            return new JsonResponse([
                'error' => 'Stock symbol parameter "q" is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $stockQuote = $this->stockDataProvider->getStockQuote($symbol);
        if (!$stockQuote) {
            return new JsonResponse([
                'error' => 'Stock quote not found or service unavailable',
            ], Response::HTTP_NOT_FOUND);
        }

        $stockQuery = new StockQuery();
        $stockQuery->setUser($user);
        $stockQuery->setSymbol($stockQuote->symbol);
        $stockQuery->setName($stockQuote->name);
        $stockQuery->setOpen($stockQuote->open);
        $stockQuery->setHigh($stockQuote->high);
        $stockQuery->setLow($stockQuote->low);
        $stockQuery->setClose($stockQuote->close);
        $stockQuery->setDate(new \DateTimeImmutable());

        $this->entityManager->persist($stockQuery);
        $this->entityManager->flush();

        $emailMessage = new SendEmailMessage(
            $user->getEmail(),
            'Stock Quote Information',
            $stockQuote->toArray(),
        );
        $this->messageBus->dispatch($emailMessage);

        return new JsonResponse($stockQuote->toArray());
    }

    #[Route('/api/history', name: 'api_stock_history', methods: ['GET'])]
    public function getStockHistory(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $stockQueries = $this->entityManager->getRepository(StockQuery::class)
            ->findBy(['user' => $user], ['date' => 'DESC']);

        $history = array_map(static fn(StockQuery $query) => [
            'date' => $query->getDate()->format('c'),
            'name' => $query->getName(),
            'symbol' => $query->getSymbol(),
            'open' => (float) $query->getOpen(),
            'high' => (float) $query->getHigh(),
            'low' => (float) $query->getLow(),
            'close' => (float) $query->getClose(),
        ], $stockQueries);

        return new JsonResponse($history);
    }

    private function getAuthenticatedUser(Request $request): ?User
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        $payload = $this->jwtService->validateToken($token);

        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        return $this->entityManager->getRepository(User::class)
            ->find($payload['user_id']);
    }
}
