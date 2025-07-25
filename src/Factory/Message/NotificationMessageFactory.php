<?php

declare(strict_types=1);

namespace App\Factory\Message;

use App\Message\NotificationMessage;

readonly class NotificationMessageFactory
{
    public function fromArray(array $data): NotificationMessage
    {
        return new NotificationMessage(
            type: $data['type'],
            userPhone:  $data['userPhone'] ?? null,
            userEmail:  $data['userEmail'] ?? null,
            promoId:  $data['promoId'] ?? null,
        );
    }
}
