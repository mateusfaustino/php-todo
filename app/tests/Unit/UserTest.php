<?php

/**
 * Testes unitários para a entidade User
 * Cobertura: criação, getters, setters e métodos de negócio
 */

use App\Entity\User;

// Testa a criação de um usuário com dados válidos
it('cria um usuário com sucesso', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao.silva@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT),
        timezone: 'America/Sao_Paulo'
    );

    expect($user)->toBeInstanceOf(User::class);
    expect($user->getName())->toBe('João Silva');
    expect($user->getEmail())->toBe('joao.silva@email.com');
    expect($user->getTimezone())->toBe('America/Sao_Paulo');
});

// Testa que o UUID é gerado automaticamente no construtor
it('gera UUID automaticamente ao criar usuário', function () {
    $user = new User(
        name: 'Maria Santos',
        email: 'maria@email.com',
        passwordHash: password_hash('senha123', PASSWORD_BCRYPT)
    );

    expect($user->getId())->toBeString();
    expect(strlen($user->getId()))->toBe(36); // Formato UUID v4
});

// Testa o método getShortName que retorna nome e sobrenome
it('retorna nome curto corretamente', function () {
    $user = new User(
        name: 'João Carlos Silva Pereira',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    expect($user->getShortName())->toBe('João Pereira');
});

// Testa o método getShortName com nome simples
it('retorna nome curto quando há apenas um nome', function () {
    $user = new User(
        name: 'João',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    expect($user->getShortName())->toBe('João');
});

// Testa o setter de nome
it('atualiza o nome do usuário', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    $user->setName('João Carlos');
    
    expect($user->getName())->toBe('João Carlos');
});

// Testa o setter de email
it('atualiza o email do usuário', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    $user->setEmail('joao.novo@email.com');
    
    expect($user->getEmail())->toBe('joao.novo@email.com');
});

// Testa o setter de timezone
it('atualiza o timezone do usuário', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash',
        timezone: 'America/Sao_Paulo'
    );

    $user->setTimezone('America/New_York');
    
    expect($user->getTimezone())->toBe('America/New_York');
});

// Testa o setter de password hash
it('atualiza o hash da senha do usuário', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash_antigo'
    );

    $newHash = password_hash('nova_senha', PASSWORD_BCRYPT);
    $user->setPasswordHash($newHash);
    
    expect($user->getPasswordHash())->toBe($newHash);
});

// Testa que a data de criação é definida automaticamente
it('define data de criação automaticamente', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    expect($user->getCreatedAt())->toBeInstanceOf(DateTime::class);
});

// Testa que as coleções são inicializadas vazias
it('inicializa coleções vazias', function () {
    $user = new User(
        name: 'João Silva',
        email: 'joao@email.com',
        passwordHash: 'hash'
    );

    expect($user->getProjects())->toBeEmpty();
    expect($user->getLists())->toBeEmpty();
    expect($user->getTags())->toBeEmpty();
    expect($user->getComments())->toBeEmpty();
    expect($user->getAttachments())->toBeEmpty();
    expect($user->getNotifications())->toBeEmpty();
});
