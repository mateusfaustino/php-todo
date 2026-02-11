<?php

declare(strict_types=1);

use App\Controller\TodoListController;

return [
    // TodoList routes
    '/lists' => [TodoListController::class, 'index'],
    '/lists/create' => [TodoListController::class, 'create'],
    '/lists/store' => [TodoListController::class, 'store'],
    '/lists/show' => [TodoListController::class, 'show'],
    '/lists/edit' => [TodoListController::class, 'edit'],
    '/lists/update' => [TodoListController::class, 'update'],
    '/lists/archive' => [TodoListController::class, 'archive'],
    '/lists/unarchive' => [TodoListController::class, 'unarchive'],
    '/lists/delete' => [TodoListController::class, 'delete'],
];
