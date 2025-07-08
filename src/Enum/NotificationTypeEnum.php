<?php

namespace App\Enum;

enum NotificationTypeEnum: string
{
    case SMS_NOTIFICATION = 'sms';
    case EMAIL_NOTIFICATION  = 'email';
}
