<?php

namespace App\Service;

use App\Enum\EmailMessageTypeEnum;
use App\Enum\NotificationTypeEnum;
use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class NotificationDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function sendSmsNotification(string $phone, EmailMessageTypeEnum $messageType, ?string $promoId = null): void
    {
        $this->messageBus->dispatch(
            new NotificationMessage(
                type: NotificationTypeEnum::EMAIL_NOTIFICATION,
                phone: $phone,
                email: null,
                promoId: $promoId,
                messageType: $messageType,
            )
        );
    }
}
