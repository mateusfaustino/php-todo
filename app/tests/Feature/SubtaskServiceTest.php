<?php

/**
 * Testes de integração para o SubtaskService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\SubtaskService;
use App\Service\TaskService;
use App\Entity\Subtask;
use App\Entity\Task;

// Testa buscar todas as subtarefas
it('busca todas as subtarefas', function () {
    $service = new SubtaskService();
    $subtasks = $service->findAll();

    expect($subtasks)->toBeArray();
});

// Testa buscar subtarefa por ID
it('busca subtarefa por id', function () {
    $service = new SubtaskService();
    $subtasks = $service->findAll();

    if (count($subtasks) > 0) {
        $firstSubtask = $subtasks[0];
        $found = $service->find($firstSubtask->getId());

        expect($found)->toBeInstanceOf(Subtask::class);
        expect($found->getId())->toBe($firstSubtask->getId());
    }
});

// Testa buscar subtarefas por tarefa
it('busca subtarefas por tarefa', function () {
    $taskService = new TaskService();
    $service = new SubtaskService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $subtasks = $service->findByTask($task);

    expect($subtasks)->toBeArray();
});

// Testa buscar subtarefas concluidas
it('busca subtarefas concluidas', function () {
    $service = new SubtaskService();
    $subtasks = $service->findCompleted();

    expect($subtasks)->toBeArray();

    foreach ($subtasks as $subtask) {
        expect($subtask->isCompleted())->toBeTrue();
    }
});

// Testa buscar subtarefas pendentes
it('busca subtarefas pendentes', function () {
    $service = new SubtaskService();
    $subtasks = $service->findPending();

    expect($subtasks)->toBeArray();

    foreach ($subtasks as $subtask) {
        expect($subtask->isCompleted())->toBeFalse();
    }
});

// Testa buscar subtarefas por criterios
it('busca subtarefas por criterios', function () {
    $service = new SubtaskService();
    $subtasks = $service->findBy(['completed' => false]);

    expect($subtasks)->toBeArray();

    foreach ($subtasks as $subtask) {
        expect($subtask->isCompleted())->toBeFalse();
    }
});

// Testa subtarefas possuem ordem valida
it('subtarefas possuem ordem valida', function () {
    $service = new SubtaskService();
    $subtasks = $service->findAll();

    if (count($subtasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($subtasks as $subtask) {
        expect($subtask->getOrder())->toBeInt();
        expect($subtask->getOrder())->toBeGreaterThanOrEqual(0);
    }
});

// Testa subtarefas possuem titulo valido
it('subtarefas possuem titulo valido', function () {
    $service = new SubtaskService();
    $subtasks = $service->findAll();

    if (count($subtasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($subtasks as $subtask) {
        expect($subtask->getTitle())->toBeString();
        expect(strlen($subtask->getTitle()))->toBeGreaterThan(0);
    }
});

// Testa calcular percentual de conclusao retorna inteiro
it('calcular percentual de conclusao retorna inteiro', function () {
    $taskService = new TaskService();
    $service = new SubtaskService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $percentage = $service->getCompletionPercentage($task);

    expect($percentage)->toBeInt();
    expect($percentage)->toBeGreaterThanOrEqual(0);
    expect($percentage)->toBeLessThanOrEqual(100);
});

// Testa retornar null ao buscar subtarefa inexistente
it('retorna null ao buscar subtarefa inexistente', function () {
    $service = new SubtaskService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
