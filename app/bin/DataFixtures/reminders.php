<?php

declare(strict_types=1);

use App\Enum\ReminderChannelEnum;

return [
    [
        'reminderDateTime' => new DateTime('2025-02-10 09:00:00'),
        'channel' => ReminderChannelEnum::EMAIL,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-10 14:00:00'),
        'channel' => ReminderChannelEnum::PUSH,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-11 08:00:00'),
        'channel' => ReminderChannelEnum::EMAIL,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-12 10:00:00'),
        'channel' => ReminderChannelEnum::SMS,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-14 16:00:00'),
        'channel' => ReminderChannelEnum::IN_APP,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-10 07:00:00'),
        'channel' => ReminderChannelEnum::EMAIL,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-18 09:30:00'),
        'channel' => ReminderChannelEnum::PUSH,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-09 18:00:00'),
        'channel' => ReminderChannelEnum::IN_APP,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-15 11:00:00'),
        'channel' => ReminderChannelEnum::EMAIL,
    ],
    [
        'reminderDateTime' => new DateTime('2025-02-20 15:00:00'),
        'channel' => ReminderChannelEnum::PUSH,
    ],
];
