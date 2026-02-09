<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationTypeEnum: string
{
    case TASK_ASSIGNED = 'task_assigned';
    case TASK_COMPLETED = 'task_completed';
    case TASK_DUE_SOON = 'task_due_soon';
    case TASK_OVERDUE = 'task_overdue';
    case COMMENT_ADDED = 'comment_added';
    case REMINDER = 'reminder';
    case PROJECT_SHARED = 'project_shared';
}
