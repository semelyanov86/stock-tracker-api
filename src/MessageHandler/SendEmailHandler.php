<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Psr\Log\LoggerInterface;

final readonly class SendEmailHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SendEmailMessage $message): void
    {
        $this->generateEmailContent($message->stockData);
        $this->logger->info('Stock quote email sent', [
            'email' => $message->email,
            'subject' => $message->subject,
            'stock_data' => $message->stockData,
        ]);
    }

    private function generateEmailContent(array $stockData): string
    {
        return \sprintf(
            '<h2>Stock Quote Information</h2>
            <p><strong>Company:</strong> %s</p>
            <p><strong>Symbol:</strong> %s</p>
            <p><strong>Open:</strong> $%.2f</p>
            <p><strong>High:</strong> $%.2f</p>
            <p><strong>Low:</strong> $%.2f</p>
            <p><strong>Close:</strong> $%.2f</p>',
            $stockData['name'],
            $stockData['symbol'],
            $stockData['open'],
            $stockData['high'],
            $stockData['low'],
            $stockData['close'],
        );
    }
}
