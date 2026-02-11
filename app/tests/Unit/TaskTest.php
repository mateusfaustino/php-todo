<?php

/**
 * Testes unitários para a entidade Task
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Subtask;
use App\Enum\TaskPriorityEnum;
use App\Enum\TaskStatusEnum;

// Helper para criar dependências
function createTaskDependencies(): array
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

    return ['user' => $user, 'list' => $list];
}

// Testa a criação de uma tarefa com dados válidos
it('cria uma tarefa com sucesso', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Implementar autenticação',
        priority: TaskPriorityEnum::HIGH,
        order: 1
    );

    expect($task)->toBeInstanceOf(Task::class);
    expect($task->getTitle())->toBe('Implementar autenticação');
    expect($task->getPriority())->toBe(TaskPriorityEnum::HIGH);
    expect($task->getStatus())->toBe(TaskStatusEnum::TODO);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar tarefa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    expect($task->getId())->toBeString();
    expect(strlen($task->getId()))->toBe(36);
});

// Testa que a tarefa inicia com status TODO
it('inicia com status TODO', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    expect($task->getStatus())->toBe(TaskStatusEnum::TODO);
});

// Testa o método completeTask
it('completa uma tarefa corretamente', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->completeTask();

    expect($task->getStatus())->toBe(TaskStatusEnum::COMPLETED);
    expect($task->getCompletedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que completeTask lança exceção se já estiver completa
it('lança exceção ao completar tarefa já concluída', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->completeTask();

    expect(fn() => $task->completeTask())
        ->toThrow(\DomainException::class, 'Task is already completed');
});

// Testa o método rescheduleTask
it('reagenda uma tarefa corretamente', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $newDueDate = new DateTime('+7 days');
    $task->rescheduleTask($newDueDate);

    expect($task->getDueDate())->toEqual($newDueDate);
});

// Testa que rescheduleTask lança exceção com data no passado
it('lança exceção ao reagendar para data no passado', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $pastDate = new DateTime('2020-01-01');

    expect(fn() => $task->rescheduleTask($pastDate))
        ->toThrow(\InvalidArgumentException::class, 'Due date cannot be in the past');
});

// Testa o método isOverdue
it('identifica tarefa atrasada corretamente', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->setDueDate(new DateTime('2020-01-01'));

    expect($task->isOverdue())->toBeTrue();
});

// Testa que tarefa completa não está atrasada
it('tarefa completa não está atrasada', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->setDueDate(new DateTime('2020-01-01'));
    $task->completeTask();

    expect($task->isOverdue())->toBeFalse();
});

// Testa o método getCompletionPercentage sem subtarefas
it('retorna 0% quando não há subtarefas e não está completa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    expect($task->getCompletionPercentage())->toBe(0);
});

// Testa o método getCompletionPercentage quando completa sem subtarefas
it('retorna 100% quando completa sem subtarefas', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->completeTask();

    expect($task->getCompletionPercentage())->toBe(100);
});

// Testa o método isCompleted
it('verifica se tarefa está completa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    expect($task->isCompleted())->toBeFalse();

    $task->completeTask();

    expect($task->isCompleted())->toBeTrue();
});

// Testa setters e getters
it('atualiza título da tarefa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Título Antigo'
    );

    $task->setTitle('Novo Título');

    expect($task->getTitle())->toBe('Novo Título');
});

it('atualiza descrição da tarefa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste'
    );

    $task->setDescription('Descrição da tarefa');

    expect($task->getDescription())->toBe('Descrição da tarefa');
});

it('atualiza prioridade da tarefa', function () {
    $deps = createTaskDependencies();
    
    $task = new Task(
        list: $deps['list'],
        title: 'Tarefa Teste',
        priority: TaskPriorityEnum::LOW
    );

    $task->setPriority(TaskPriorityEnum::URGENT);

    expect($task->getPriority())->toBe(TaskPriorityEnum::URGENT);
});
