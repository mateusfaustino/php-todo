<?php

/**
 * Testes unitários para a entidade Reminder
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Reminder;
use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Enum\TaskPriorityEnum;
use App\Enum\ReminderChannelEnum;

// Helper para criar dependências
function createReminderDependencies(): array
{
    $user = new User(
        name: 'Test User',
        email: 'test@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );

    $list = new TodoList(
        user: $user,
        name: 'A Fazer',
        order: 1
    );

    $task = new Task(
        list: $list,
        title: 'Tarefa Teste',
        priority: TaskPriorityEnum::MEDIUM
    );

    return ['user' => $user, 'list' => $list, 'task' => $task];
}

// Testa a criação de um lembrete com dados válidos
it('cria um lembrete com sucesso', function () {
    $deps = createReminderDependencies();
    $reminderDateTime = new DateTime('2025-02-10 09:00:00');
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: $reminderDateTime,
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder)->toBeInstanceOf(Reminder::class);
    expect($reminder->getReminderDateTime())->toEqual($reminderDateTime);
    expect($reminder->getChannel())->toBe(ReminderChannelEnum::EMAIL);
    expect($reminder->getTask())->toBe($deps['task']);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar lembrete', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('2025-02-10 09:00:00'),
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder->getId())->toBeString();
    expect(strlen($reminder->getId()))->toBe(36);
});

// Testa que o lembrete inicia não enviado
it('inicia como não enviado', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('2025-02-10 09:00:00'),
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder->isSent())->toBeFalse();
    expect($reminder->getSentAt())->toBeNull();
});

// Testa o método schedule
it('agenda um lembrete corretamente', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('+1 day'),
        channel: ReminderChannelEnum::EMAIL
    );

    $newDateTime = new DateTime('+2 days');
    $reminder->schedule($newDateTime);

    expect($reminder->getReminderDateTime())->toEqual($newDateTime);
});

// Testa que o método cancel lança exceção quando já enviado
it('lança exceção ao cancelar lembrete já enviado', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('+1 day'),
        channel: ReminderChannelEnum::EMAIL
    );

    // Marca como enviado primeiro
    $reminder->markAsSent();

    // O método cancel lança exceção quando já foi enviado
    expect(fn() => $reminder->cancel())
        ->toThrow(\DomainException::class);
});

// Testa o método markAsSent
it('marca lembrete como enviado', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('2025-02-10 09:00:00'),
        channel: ReminderChannelEnum::EMAIL
    );

    $reminder->markAsSent();

    expect($reminder->isSent())->toBeTrue();
    expect($reminder->getSentAt())->toBeInstanceOf(DateTime::class);
});

// Testa o método isDue quando está no horário
it('identifica lembrete no horário', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('-1 minute'),
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder->isDue())->toBeTrue();
});

// Testa o método isDue quando não está no horário
it('identifica lembrete fora do horário', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('+1 day'),
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder->isDue())->toBeFalse();
});

// Testa o método isPending
it('identifica lembrete pendente', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('+1 day'),
        channel: ReminderChannelEnum::EMAIL
    );

    expect($reminder->isPending())->toBeTrue();
});

// Testa que lembrete enviado não está pendente
it('lembrete enviado não está pendente', function () {
    $deps = createReminderDependencies();
    
    $reminder = new Reminder(
        task: $deps['task'],
        reminderDateTime: new DateTime('+1 day'),
        channel: ReminderChannelEnum::EMAIL
    );

    $reminder->markAsSent();

    expect($reminder->isPending())->toBeFalse();
});
