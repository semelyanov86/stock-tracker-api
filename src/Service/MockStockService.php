<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\StockQuoteDTO;

final readonly class MockStockService implements StockDataProviderInterface
{
    public function getStockQuote(string $symbol): ?StockQuoteDTO
    {
        // Мок данные для тестирования
        $mockData = [
            'IBM' => ['name' => 'International Business Machines', 'open' => 123.66, 'high' => 125.50, 'low' => 122.49, 'close' => 123.00],
            'AAPL' => ['name' => 'Apple Inc.', 'open' => 150.00, 'high' => 152.30, 'low' => 148.75, 'close' => 151.20],
            'APPL.US' => ['name' => 'Apple Inc.', 'open' => 150.00, 'high' => 152.30, 'low' => 148.75, 'close' => 151.20],
        ];

        if (!isset($mockData[$symbol])) {
            return null;
        }

        $data = $mockData[$symbol];

        return new StockQuoteDTO(
            name: $data['name'],
            symbol: $symbol,
            open: $data['open'],
            high: $data['high'],
            low: $data['low'],
            close: $data['close'],
        );
    }
}
