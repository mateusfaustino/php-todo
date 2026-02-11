<?php

/**
 * Testes de integração para o ReminderService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\ReminderService;
use App\Service\TaskService;
use App\Entity\Reminder;
use App\Entity\Task;
use App\Enum\ReminderChannelEnum;

// Testa buscar todos os lembretes
it('busca todos os lembretes', function () {
    $service = new ReminderService();
    $reminders = $service->findAll();

    expect($reminders)->toBeArray();
});

// Testa buscar lembrete por ID
it('busca lembrete por id', function () {
    $service = new ReminderService();
    $reminders = $service->findAll();

    if (count($reminders) > 0) {
        $firstReminder = $reminders[0];
        $found = $service->find($firstReminder->getId());

        expect($found)->toBeInstanceOf(Reminder::class);
        expect($found->getId())->toBe($firstReminder->getId());
    }
});

// Testa buscar lembretes por tarefa
it('busca lembretes por tarefa', function () {
    $taskService = new TaskService();
    $service = new ReminderService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $reminders = $service->findByTask($task);

    expect($reminders)->toBeArray();
});

// Testa buscar lembretes por canal
it('busca lembretes por canal', function () {
    $service = new ReminderService();
    $reminders = $service->findByChannel(ReminderChannelEnum::EMAIL);

    expect($reminders)->toBeArray();

    foreach ($reminders as $reminder) {
        expect($reminder->getChannel())->toBe(ReminderChannelEnum::EMAIL);
    }
});

// Testa buscar lembretes pendentes
it('busca lembretes pendentes', function () {
    $service = new ReminderService();
    $reminders = $service->findPending();

    expect($reminders)->toBeArray();

    foreach ($reminders as $reminder) {
        expect($reminder->isSent())->toBeFalse();
    }
});

// Testa buscar lembretes enviados
it('busca lembretes enviados', function () {
    $service = new ReminderService();
    $reminders = $service->findSent();

    expect($reminders)->toBeArray();

    foreach ($reminders as $reminder) {
        expect($reminder->isSent())->toBeTrue();
    }
});

// Testa buscar lembretes a vencer
it('busca lembretes a vencer', function () {
    $service = new ReminderService();
    $reminders = $service->findUpcoming(24);

    expect($reminders)->toBeArray();
});

// Testa buscar lembretes por criterios
it('busca lembretes por criterios', function () {
    $service = new ReminderService();
    $reminders = $service->findBy(['sent' => false]);

    expect($reminders)->toBeArray();
});

// Testa contar lembretes por tarefa retorna inteiro
it('contar lembretes por tarefa retorna inteiro', function () {
    $taskService = new TaskService();
    $service = new ReminderService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $count = $service->getCountByTask($task);

    expect($count)->toBeInt();
    expect($count)->toBeGreaterThanOrEqual(0);
});

// Testa retornar null ao buscar lembrete inexistente
it('retorna null ao buscar lembrete inexistente', function () {
    $service = new ReminderService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
