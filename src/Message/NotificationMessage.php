<?php

namespace App\Message;

use App\Enum\EmailMessageTypeEnum;
use App\Enum\NotificationTypeEnum;

readonly class NotificationMessage
{
    public function __construct(
        public NotificationTypeEnum $type,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $promoId = null,
        public EmailMessageTypeEnum $messageType,
    )
    {}
}
