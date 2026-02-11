<?php

/**
 * Testes de integração para o TaskService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\TaskService;
use App\Service\TodoListService;
use App\Entity\Task;
use App\Enum\TaskPriorityEnum;
use App\Enum\TaskStatusEnum;

// Testa buscar todas as tarefas
it('busca todas as tarefas', function () {
    $service = new TaskService();
    $tasks = $service->findAll();

    expect($tasks)->toBeArray();
});

// Testa buscar tarefa por ID
it('busca tarefa por id', function () {
    $service = new TaskService();
    $tasks = $service->findAll();

    if (count($tasks) > 0) {
        $firstTask = $tasks[0];
        $found = $service->find($firstTask->getId());

        expect($found)->toBeInstanceOf(Task::class);
        expect($found->getId())->toBe($firstTask->getId());
    }
});

// Testa buscar tarefas por status
it('busca tarefas por status', function () {
    $service = new TaskService();
    $tasks = $service->findByStatus(TaskStatusEnum::TODO);

    expect($tasks)->toBeArray();

    foreach ($tasks as $task) {
        expect($task->getStatus())->toBe(TaskStatusEnum::TODO);
    }
});

// Testa buscar tarefas por prioridade
it('busca tarefas por prioridade', function () {
    $service = new TaskService();
    $tasks = $service->findByPriority(TaskPriorityEnum::HIGH);

    expect($tasks)->toBeArray();

    foreach ($tasks as $task) {
        expect($task->getPriority())->toBe(TaskPriorityEnum::HIGH);
    }
});

// Testa buscar tarefas atrasadas
it('busca tarefas atrasadas', function () {
    $service = new TaskService();
    $tasks = $service->findOverdue();

    expect($tasks)->toBeArray();
});

// Testa buscar tarefas com vencimento hoje
it('busca tarefas com vencimento hoje', function () {
    $service = new TaskService();
    $tasks = $service->findDueToday();

    expect($tasks)->toBeArray();
});

// Testa buscar tarefas por lista
it('busca tarefas por lista', function () {
    $listService = new TodoListService();
    $service = new TaskService();

    $lists = $listService->findAll();

    if (count($lists) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $list = $lists[0];
    $tasks = $service->findByList($list);

    expect($tasks)->toBeArray();
});

// Testa tarefas possuem ordem valida
it('tarefas possuem ordem valida', function () {
    $service = new TaskService();
    $tasks = $service->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($tasks as $task) {
        expect($task->getOrder())->toBeInt();
        expect($task->getOrder())->toBeGreaterThanOrEqual(0);
    }
});

// Testa obter estatísticas de tarefas
it('obtem estatisticas de tarefas', function () {
    $service = new TaskService();
    $stats = $service->getStatistics();

    expect($stats)->toBeArray();
    expect($stats)->toHaveKeys(['total', 'completed', 'todo', 'in_progress', 'completion_rate']);
    expect($stats['total'])->toBeInt();
    expect($stats['completion_rate'])->toBeFloat();
});

// Testa retornar null ao buscar tarefa inexistente
it('retorna null ao buscar tarefa inexistente', function () {
    $service = new TaskService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
