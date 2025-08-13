<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\StockQuoteDTO;

interface StockDataProviderInterface
{
    public function getStockQuote(string $symbol): ?StockQuoteDTO;
}
