<?php

/**
 * Testes unitários para a entidade Tag
 * Cobertura: criação, getters e setters
 */

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Task;
use App\Entity\TodoList;
use App\Enum\TaskPriorityEnum;

// Helper para criar dependências
function createTagDependencies(): array
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

// Testa a criação de uma tag com dados válidos
it('cria uma tag com sucesso', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Urgente',
        color: '#e74c3c'
    );

    expect($tag)->toBeInstanceOf(Tag::class);
    expect($tag->getName())->toBe('Urgente');
    expect($tag->getColor())->toBe('#e74c3c');
    expect($tag->getUser())->toBe($deps['user']);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar tag', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Tag Teste',
        color: '#3498db'
    );

    expect($tag->getId())->toBeString();
    expect(strlen($tag->getId()))->toBe(36);
});

// Testa o setter de nome
it('atualiza o nome da tag', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Nome Antigo',
        color: '#3498db'
    );

    $tag->setName('Novo Nome');

    expect($tag->getName())->toBe('Novo Nome');
});

// Testa o setter de cor
it('atualiza a cor da tag', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Tag Teste',
        color: '#3498db'
    );

    $tag->setColor('#2ecc71');

    expect($tag->getColor())->toBe('#2ecc71');
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Tag Teste',
        color: '#3498db'
    );

    expect($tag->getCreatedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que a coleção de tarefas é inicializada vazia
it('inicializa coleção de tarefas vazia', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Tag Teste',
        color: '#3498db'
    );

    expect($tag->getTasks())->toBeEmpty();
});

// Testa adicionar tarefa à tag
it('adiciona tarefa à tag', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Urgente',
        color: '#e74c3c'
    );

    $tag->addTask($deps['task']);

    expect($tag->getTasks())->toContain($deps['task']);
});

// Testa remover tarefa da tag
it('remove tarefa da tag', function () {
    $deps = createTagDependencies();
    
    $tag = new Tag(
        user: $deps['user'],
        name: 'Urgente',
        color: '#e74c3c'
    );

    $tag->addTask($deps['task']);
    $tag->removeTask($deps['task']);

    expect($tag->getTasks())->not->toContain($deps['task']);
});
