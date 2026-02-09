<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
