<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final readonly class SendEmailHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
    ) {}

    public function __invoke(SendEmailMessage $message): void
    {
        ray($this->generateEmailContent($message->stockData));
        $this->logger->info('Stock quote email sent', [
            'email' => $message->email,
            'subject' => $message->subject,
            'stock_data' => $message->stockData,
        ]);
        $email = new Email()
            ->from('noreply@stocktracker.com')
            ->to($message->email)
            ->subject($message->subject)
            ->html($this->generateEmailContent($message->stockData));

        $this->mailer->send($email);
    }

    /**
     * @param  array<string, scalar>  $stockData
     */
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
