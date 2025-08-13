<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class StockQuoteDTO
{
    public function __construct(
        public string $name,
        public string $symbol,
        public float $open,
        public float $high,
        public float $low,
        public float $close,
    ) {}

    /**
     * @return array<string, scalar>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'symbol' => $this->symbol,
            'open' => $this->open,
            'high' => $this->high,
            'low' => $this->low,
            'close' => $this->close,
        ];
    }
}
