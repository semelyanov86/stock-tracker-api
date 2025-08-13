<?php

declare(strict_types=1);

namespace App\Message;

final readonly class SendEmailMessage
{
    /**
     * @param  array<string, scalar>  $stockData
     */
    public function __construct(
        public string $email,
        public string $subject,
        public array $stockData,
    ) {}
}
