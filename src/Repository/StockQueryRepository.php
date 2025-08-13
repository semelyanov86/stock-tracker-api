<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StockQuery;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockQuery>
 */
final class StockQueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockQuery::class);
    }

    /**
     * @return StockQuery[]
     */
    public function findByUser(User $user, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('sq')
            ->andWhere('sq.user = :user')
            ->setParameter('user', $user)
            ->orderBy('sq.date', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        // @phpstan-ignore-next-line
        return $qb->getQuery()->getResult();
    }

    /**
     * @return StockQuery[]
     */
    public function findByUserAndSymbol(User $user, string $symbol): array
    {
        // @phpstan-ignore-next-line
        return $this->createQueryBuilder('sq')
            ->andWhere('sq.user = :user')
            ->andWhere('sq.symbol = :symbol')
            ->setParameter('user', $user)
            ->setParameter('symbol', $symbol)
            ->orderBy('sq.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return StockQuery[]
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedIntervalStringException
     */
    public function findRecentByUser(User $user, int $days = 7): array
    {
        $date = new \DateTimeImmutable();
        $date->sub(new \DateInterval('P' . $days . 'D'));

        // @phpstan-ignore-next-line
        return $this->createQueryBuilder('sq')
            ->andWhere('sq.user = :user')
            ->andWhere('sq.date >= :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->orderBy('sq.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('sq')
            ->select('count(sq.id)')
            ->andWhere('sq.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
