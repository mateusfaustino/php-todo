<?php

declare(strict_types=1);

use App\Controller\TaskController;

return [
    // Task routes
    '/tasks' => [TaskController::class, 'index'],
    '/tasks/by-status' => [TaskController::class, 'byStatus'],
    '/tasks/overdue' => [TaskController::class, 'overdue'],
    '/tasks/create' => [TaskController::class, 'create'],
    '/tasks/store' => [TaskController::class, 'store'],
    '/tasks/show' => [TaskController::class, 'show'],
    '/tasks/edit' => [TaskController::class, 'edit'],
    '/tasks/update' => [TaskController::class, 'update'],
    '/tasks/complete' => [TaskController::class, 'complete'],
    '/tasks/delete' => [TaskController::class, 'delete'],
];
