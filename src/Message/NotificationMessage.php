<?php

namespace App\Message;

use App\Enum\NotificationTypeEnum;

readonly class NotificationMessage
{
    public function __construct(
        public NotificationTypeEnum $type,
        public ?string $userPhone = null,
        public ?string $userEmail = null,
        public ?string $promoId = null,
    ) {
    }

    public function __toString(): string
    {
        return json_encode([
            'type' => $this->type->value,
            'userPhone' => $this->userPhone,
            'userEmail' => $this->userEmail,
            'promoId' => $this->promoId,
        ]);
    }
}
