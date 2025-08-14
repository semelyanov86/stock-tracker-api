<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class StockRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $q,
    ) {}
}
