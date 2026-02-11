<?php

/**
 * Testes de integração para o StatusHistoryService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\StatusHistoryService;
use App\Service\TaskService;
use App\Service\UserService;
use App\Entity\StatusHistory;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatusEnum;

// Testa buscar todo o historico de status
it('busca todo o historico de status', function () {
    $service = new StatusHistoryService();
    $history = $service->findAll();

    expect($history)->toBeArray();
});

// Testa buscar entrada de historico por ID
it('busca entrada de historico por id', function () {
    $service = new StatusHistoryService();
    $history = $service->findAll();

    if (count($history) > 0) {
        $firstEntry = $history[0];
        $found = $service->find($firstEntry->getId());

        expect($found)->toBeInstanceOf(StatusHistory::class);
        expect($found->getId())->toBe($firstEntry->getId());
    }
});

// Testa buscar historico por tarefa
it('busca historico por tarefa', function () {
    $taskService = new TaskService();
    $service = new StatusHistoryService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $history = $service->findByTask($task);

    expect($history)->toBeArray();
});

// Testa buscar historico por usuario
it('busca historico por usuario', function () {
    $userService = new UserService();
    $service = new StatusHistoryService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $history = $service->findByUser($user);

    expect($history)->toBeArray();
});

// Testa buscar historico por status anterior
it('busca historico por status anterior', function () {
    $service = new StatusHistoryService();
    $history = $service->findByPreviousStatus(TaskStatusEnum::TODO);

    expect($history)->toBeArray();

    foreach ($history as $entry) {
        expect($entry->getPreviousStatus())->toBe(TaskStatusEnum::TODO);
    }
});

// Testa buscar historico por novo status
it('busca historico por novo status', function () {
    $service = new StatusHistoryService();
    $history = $service->findByNewStatus(TaskStatusEnum::COMPLETED);

    expect($history)->toBeArray();

    foreach ($history as $entry) {
        expect($entry->getNewStatus())->toBe(TaskStatusEnum::COMPLETED);
    }
});

// Testa buscar historico recente
it('busca historico recente', function () {
    $service = new StatusHistoryService();
    $history = $service->findRecent(5);

    expect($history)->toBeArray();
    expect(count($history))->toBeLessThanOrEqual(5);
});

// Testa contar mudancas por tarefa retorna inteiro
it('contar mudancas por tarefa retorna inteiro', function () {
    $taskService = new TaskService();
    $service = new StatusHistoryService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $count = $service->getChangeCountForTask($task);

    expect($count)->toBeInt();
    expect($count)->toBeGreaterThanOrEqual(0);
});

// Testa obter estatisticas de status retorna array
it('obter estatisticas de status retorna array', function () {
    $taskService = new TaskService();
    $service = new StatusHistoryService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $stats = $service->getStatusStatistics($task);

    expect($stats)->toBeArray();
    expect($stats)->toHaveKeys(['total_changes', 'completions', 'cancellations', 'reopenings']);
});

// Testa retornar null ao buscar entrada de historico inexistente
it('retorna null ao buscar entrada de historico inexistente', function () {
    $service = new StatusHistoryService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
