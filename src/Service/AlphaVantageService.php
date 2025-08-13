<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\StockQuoteDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

final readonly class AlphaVantageService implements StockDataProviderInterface
{
    private const string BASE_URL = 'https://www.alphavantage.co/query';

    public function __construct(
        private Client $httpClient,
        private string $apiKey,
        private LoggerInterface $logger,
    ) {}

    public function getStockQuote(string $symbol): ?StockQuoteDTO
    {
        try {
            $response = $this->httpClient->get(self::BASE_URL, [
                'query' => [
                    'function' => 'GLOBAL_QUOTE',
                    'symbol' => $symbol,
                    'apikey' => $this->apiKey,
                ],
                'timeout' => 10,
            ]);
            /** @var array<string, array<string, scalar>> $data */
            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['Global Quote'])) {
                $this->logger->warning('Invalid response from AlphaVantage API', [
                    'symbol' => $symbol,
                    'response' => $data,
                ]);

                return null;
            }

            $quote = $data['Global Quote'];

            return new StockQuoteDTO(
                name: $this->getCompanyName($symbol),
                symbol: (string) $quote['01. symbol'],
                open: (float) $quote['02. open'],
                high: (float) $quote['03. high'],
                low: (float) $quote['04. low'],
                close: (float) $quote['05. price'],
            );

        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch stock quote from AlphaVantage', [
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ]);

            return null;
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error while fetching stock quote', [
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function getCompanyName(string $symbol): string
    {
        $names = [
            'IBM' => 'International Business Machines',
            'AAPL' => 'Apple Inc.',
            'APPL.US' => 'Apple Inc.',
            'MSFT' => 'Microsoft Corporation',
            'GOOGL' => 'Alphabet Inc.',
            'TSLA' => 'Tesla Inc.',
        ];

        return $names[$symbol] ?? strtoupper($symbol);
    }
}
