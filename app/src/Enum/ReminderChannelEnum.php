<?php

declare(strict_types=1);

namespace App\Enum;

enum ReminderChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
    case IN_APP = 'in_app';
}
