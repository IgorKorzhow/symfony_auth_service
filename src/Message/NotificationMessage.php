<?php

namespace App\Message;

use App\Enum\NotificationTypeEnum;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('kafka_notification_producer')]
readonly class NotificationMessage
{
    public function __construct(
        public NotificationTypeEnum $type,
        public ?string $userPhone = null,
        public ?string $userEmail = null,
        public ?string $promoId = null,
    ) {
    }
}
