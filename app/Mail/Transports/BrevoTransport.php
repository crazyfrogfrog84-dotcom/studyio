<?php

namespace App\Mail\Transports;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class BrevoTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $endpoint,
        private readonly int $timeout = 10,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if ($this->apiKey === '' || $this->endpoint === '') {
            throw new RuntimeException('Brevo email delivery is not configured.');
        }

        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new InvalidArgumentException('Brevo transport only supports email messages.');
        }

        $sender = $email->getFrom()[0] ?? null;
        $payload = [
            'sender' => $this->formatAddress($sender),
            'to' => array_map($this->formatAddress(...), $email->getTo()),
            'subject' => $email->getSubject() ?? 'StuDyIo notification',
        ];

        if (($html = $email->getHtmlBody()) !== null) {
            $payload['htmlContent'] = $html;
        }

        if (($text = $email->getTextBody()) !== null) {
            $payload['textContent'] = $text;
        }

        Http::withHeaders(['api-key' => $this->apiKey])
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout($this->timeout)
            ->post($this->endpoint, $payload)
            ->throw();

        Log::info('Brevo accepted a StuDyIo email.');
    }

    public function __toString(): string
    {
        return 'brevo';
    }

    /**
     * @return array{email: string, name?: string}
     */
    private function formatAddress(?Address $address): array
    {
        if ($address === null) {
            throw new InvalidArgumentException('A sender address is required for Brevo email delivery.');
        }

        $formatted = ['email' => $address->getAddress()];

        if ($address->getName() !== '') {
            $formatted['name'] = $address->getName();
        }

        return $formatted;
    }
}
