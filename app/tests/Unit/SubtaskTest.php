<?php

/**
 * Testes unitários para a entidade Subtask
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Subtask;
use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Enum\TaskPriorityEnum;

// Helper para criar dependências
function createSubtaskDependencies(): array
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
        title: 'Tarefa Principal',
        priority: TaskPriorityEnum::MEDIUM
    );

    return ['user' => $user, 'list' => $list, 'task' => $task];
}

// Testa a criação de uma subtarefa com dados válidos
it('cria uma subtarefa com sucesso', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste',
        order: 1
    );

    expect($subtask)->toBeInstanceOf(Subtask::class);
    expect($subtask->getTitle())->toBe('Subtarefa Teste');
    expect($subtask->getOrder())->toBe(1);
    expect($subtask->getTask())->toBe($deps['task']);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar subtarefa', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    expect($subtask->getId())->toBeString();
    expect(strlen($subtask->getId()))->toBe(36);
});

// Testa que a subtarefa inicia não concluída
it('inicia como não concluída', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    expect($subtask->isCompleted())->toBeFalse();
});

// Testa o método completeSubtask
it('completa uma subtarefa corretamente', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    $subtask->completeSubtask();

    expect($subtask->isCompleted())->toBeTrue();
});

// Testa o método uncompleteSubtask
it('desmarca conclusão de uma subtarefa', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    $subtask->completeSubtask();
    $subtask->uncompleteSubtask();

    expect($subtask->isCompleted())->toBeFalse();
});

// Testa o método reorderSubtask
it('reordena uma subtarefa corretamente', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste',
        order: 1
    );

    $subtask->reorderSubtask(5);

    expect($subtask->getOrder())->toBe(5);
});

// Testa o setter de título
it('atualiza o título da subtarefa', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Título Antigo'
    );

    $subtask->setTitle('Novo Título');

    expect($subtask->getTitle())->toBe('Novo Título');
});

// Testa o setter de concluído
it('define status de concluído manualmente', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    $subtask->setCompleted(true);

    expect($subtask->isCompleted())->toBeTrue();
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $deps = createSubtaskDependencies();
    
    $subtask = new Subtask(
        task: $deps['task'],
        title: 'Subtarefa Teste'
    );

    expect($subtask->getCreatedAt())->toBeInstanceOf(DateTime::class);
});
