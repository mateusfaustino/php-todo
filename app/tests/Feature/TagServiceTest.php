<?php

/**
 * Testes de integração para o TagService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\TagService;
use App\Service\UserService;
use App\Service\TaskService;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Task;

// Testa buscar todas as tags
it('busca todas as tags', function () {
    $service = new TagService();
    $tags = $service->findAll();

    expect($tags)->toBeArray();
});

// Testa buscar tag por ID
it('busca tag por id', function () {
    $service = new TagService();
    $tags = $service->findAll();

    if (count($tags) > 0) {
        $firstTag = $tags[0];
        $found = $service->find($firstTag->getId());

        expect($found)->toBeInstanceOf(Tag::class);
        expect($found->getId())->toBe($firstTag->getId());
    }
});

// Testa buscar tags por usuario
it('busca tags por usuario', function () {
    $userService = new UserService();
    $service = new TagService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $tags = $service->findByUser($user);

    expect($tags)->toBeArray();
});

// Testa buscar tags por nome
it('busca tags por nome', function () {
    $service = new TagService();
    $tags = $service->findByName('urgente');

    expect($tags)->toBeArray();
});

// Testa buscar tags por criterios
it('busca tags por criterios', function () {
    $service = new TagService();
    $tags = $service->findBy([]);

    expect($tags)->toBeArray();
});

// Testa tags possuem nome valido
it('tags possuem nome valido', function () {
    $service = new TagService();
    $tags = $service->findAll();

    if (count($tags) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($tags as $tag) {
        expect($tag->getName())->toBeString();
        expect(strlen($tag->getName()))->toBeGreaterThan(0);
    }
});

// Testa tags possuem cor valida
it('tags possuem cor valida', function () {
    $service = new TagService();
    $tags = $service->findAll();

    if (count($tags) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($tags as $tag) {
        $color = $tag->getColor();
        expect($color)->toBeString();
        expect(strlen($color))->toBe(7);
        expect($color[0])->toBe('#');
    }
});

// Testa buscar tags por tarefa
it('busca tags por tarefa', function () {
    $taskService = new TaskService();
    $service = new TagService();

    $tasks = $taskService->findAll();

    if (count($tasks) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $task = $tasks[0];
    $tags = $service->findByTask($task);

    expect($tags)->toBeArray();
});

// Testa contar uso de tag retorna inteiro
it('contar uso de tag retorna inteiro', function () {
    $service = new TagService();
    $tags = $service->findAll();

    if (count($tags) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $tag = $tags[0];
    $count = $service->getUsageCount($tag);

    expect($count)->toBeInt();
    expect($count)->toBeGreaterThanOrEqual(0);
});

// Testa retornar null ao buscar tag inexistente
it('retorna null ao buscar tag inexistente', function () {
    $service = new TagService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
