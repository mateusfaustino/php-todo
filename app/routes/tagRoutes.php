<?php

declare(strict_types=1);

use App\Controller\TagController;

return [
    // Tag routes
    '/tags' => [TagController::class, 'index'],
    '/tags/create' => [TagController::class, 'create'],
    '/tags/store' => [TagController::class, 'store'],
    '/tags/show' => [TagController::class, 'show'],
    '/tags/edit' => [TagController::class, 'edit'],
    '/tags/update' => [TagController::class, 'update'],
    '/tags/assign' => [TagController::class, 'assignToTask'],
    '/tags/remove' => [TagController::class, 'removeFromTask'],
    '/tags/delete' => [TagController::class, 'delete'],
];
