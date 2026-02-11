<?php

/**
 * Testes unitários para a entidade TodoList
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\TodoList;
use App\Entity\User;
use App\Entity\Project;

// Helper para criar um usuário de teste
function createTestUserForList(): User
{
    return new User(
        name: 'Test User',
        email: 'test@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );
}

// Testa a criação de uma lista com dados válidos
it('cria uma lista com sucesso', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'A Fazer',
        order: 1
    );

    expect($list)->toBeInstanceOf(TodoList::class);
    expect($list->getName())->toBe('A Fazer');
    expect($list->getOrder())->toBe(1);
    expect($list->getUser())->toBe($user);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar lista', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect($list->getId())->toBeString();
    expect(strlen($list->getId()))->toBe(36);
});

// Testa que a lista inicia não arquivada
it('inicia como não arquivada', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect($list->isArchived())->toBeFalse();
});

// Testa o método de arquivar lista
it('arquiva uma lista corretamente', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    $list->archive();
    
    expect($list->isArchived())->toBeTrue();
});

// Testa o método de desarquivar lista
it('desarquiva uma lista corretamente', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    $list->archive();
    $list->unarchive();
    
    expect($list->isArchived())->toBeFalse();
});

// Testa o método reorder com ordem válida
it('reordena uma lista corretamente', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste',
        order: 1
    );

    $list->reorder(5);
    
    expect($list->getOrder())->toBe(5);
});

// Testa que reorder lança exceção com ordem negativa
it('lança exceção ao reordenar com valor negativo', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect(fn() => $list->reorder(-1))
        ->toThrow(\InvalidArgumentException::class, 'Order must be a positive integer');
});

// Testa o setter de nome
it('atualiza o nome da lista', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Nome Antigo'
    );

    $list->setName('Novo Nome');
    
    expect($list->getName())->toBe('Novo Nome');
});

// Testa o setter de projeto
it('define o projeto da lista', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect($list->getProject())->toBeNull();

    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    $list->setProject($project);
    
    expect($list->getProject())->toBe($project);
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect($list->getCreatedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que a coleção de tarefas é inicializada vazia
it('inicializa coleção de tarefas vazia', function () {
    $user = createTestUserForList();
    $list = new TodoList(
        user: $user,
        name: 'Lista Teste'
    );

    expect($list->getTasks())->toBeEmpty();
});
