<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Factory\Message\NotificationMessageFactory;
use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class NotificationMessageSerializer implements SerializerInterface
{
    public function __construct(
        private NotificationMessageFactory $notificationMessageFactory,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);

        if (!$data) {
            throw new MessageDecodingFailedException('Invalid product message format: ' . json_encode($encodedEnvelope));
        }

        return new Envelope($this->notificationMessageFactory->fromArray($data));
    }

    public function encode(Envelope $envelope): array
    {
        /** @var NotificationMessage $message */
        $message = $envelope->getMessage();

        if (!$message instanceof NotificationMessage) {
            throw new \InvalidArgumentException('Expected ProductMessage message');
        }

        $data = [
            'type' => $message->type,
            'userPhone' =>  $message->userPhone,
            'userEmail' => $message->userEmail,
            'promoId' => $message->promoId,
        ];

        return [
            'key' => '',
            'headers' => [],
            'body' => json_encode($data),
        ];
    }
}
