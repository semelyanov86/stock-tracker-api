<?php

declare(strict_types=1);

namespace App\Message;

final readonly class SendEmailMessage
{
    public function __construct(
        public string $email,
        public string $subject,
        public array $stockData,
    ) {}
}
