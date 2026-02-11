<?php

declare(strict_types=1);

use App\Controller\ProjectController;

return [
    // Project routes
    '/projects' => [ProjectController::class, 'index'],
    '/projects/create' => [ProjectController::class, 'create'],
    '/projects/store' => [ProjectController::class, 'store'],
    '/projects/show' => [ProjectController::class, 'show'],
    '/projects/edit' => [ProjectController::class, 'edit'],
    '/projects/update' => [ProjectController::class, 'update'],
    '/projects/archive' => [ProjectController::class, 'archive'],
    '/projects/unarchive' => [ProjectController::class, 'unarchive'],
    '/projects/delete' => [ProjectController::class, 'delete'],
];
