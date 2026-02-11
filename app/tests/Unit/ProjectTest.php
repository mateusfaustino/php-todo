<?php

/**
 * Testes unitários para a entidade Project
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\Project;
use App\Entity\User;

// Helper para criar um usuário de teste
function createTestUser(): User
{
    return new User(
        name: 'Test User',
        email: 'test@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );
}

// Testa a criação de um projeto com dados válidos
it('cria um projeto com sucesso', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste',
        color: '#3498db'
    );

    expect($project)->toBeInstanceOf(Project::class);
    expect($project->getName())->toBe('Projeto Teste');
    expect($project->getColor())->toBe('#3498db');
    expect($project->getUser())->toBe($user);
});

// Testa que o UUID é gerado automaticamente
it('gera UUID automaticamente ao criar projeto', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect($project->getId())->toBeString();
    expect(strlen($project->getId()))->toBe(36);
});

// Testa que o projeto inicia não arquivado
it('inicia como não arquivado', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect($project->isArchived())->toBeFalse();
});

// Testa o método de arquivar projeto
it('arquiva um projeto corretamente', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    $project->archive();
    
    expect($project->isArchived())->toBeTrue();
});

// Testa o método de desarquivar projeto
it('desarquiva um projeto corretamente', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    $project->archive();
    $project->unarchive();
    
    expect($project->isArchived())->toBeFalse();
});

// Testa o método rename com nome válido
it('renomeia um projeto corretamente', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Nome Antigo'
    );

    $project->rename('Novo Nome');
    
    expect($project->getName())->toBe('Novo Nome');
});

// Testa que rename lança exceção com nome vazio
it('lança exceção ao renomear com nome vazio', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect(fn() => $project->rename(''))
        ->toThrow(\InvalidArgumentException::class, 'Project name cannot be empty');
});

// Testa o setter de descrição
it('define e atualiza a descrição do projeto', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect($project->getDescription())->toBeNull();

    $project->setDescription('Descrição do projeto');
    
    expect($project->getDescription())->toBe('Descrição do projeto');
});

// Testa o setter de cor
it('atualiza a cor do projeto', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste',
        color: '#3498db'
    );

    $project->setColor('#e74c3c');
    
    expect($project->getColor())->toBe('#e74c3c');
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect($project->getCreatedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que a coleção de listas é inicializada vazia
it('inicializa coleção de listas vazia', function () {
    $user = createTestUser();
    $project = new Project(
        user: $user,
        name: 'Projeto Teste'
    );

    expect($project->getLists())->toBeEmpty();
});
