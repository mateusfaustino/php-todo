<?php

/**
 * Testes de integração para o TodoListService
 * Testa as operações de CRUD e lógica de negócio do serviço
 */

use App\Service\TodoListService;
use App\Service\UserService;
use App\Entity\TodoList;
use App\Entity\User;

// Testa buscar todas as listas
it('busca todas as listas', function () {
    $service = new TodoListService();
    $lists = $service->findAll();

    expect($lists)->toBeArray();
});

// Testa buscar lista por ID
it('busca lista por id', function () {
    $service = new TodoListService();
    $lists = $service->findAll();

    if (count($lists) > 0) {
        $firstList = $lists[0];
        $found = $service->find($firstList->getId());

        expect($found)->toBeInstanceOf(TodoList::class);
        expect($found->getId())->toBe($firstList->getId());
    }
});

// Testa buscar listas por usuario
it('busca listas por usuario', function () {
    $userService = new UserService();
    $service = new TodoListService();

    $users = $userService->findAll();

    if (count($users) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    $user = $users[0];
    $lists = $service->findByUser($user);

    expect($lists)->toBeArray();
});

// Testa buscar listas por criterios
it('busca listas por criterios', function () {
    $service = new TodoListService();
    $lists = $service->findBy(['archived' => false]);

    expect($lists)->toBeArray();

    foreach ($lists as $list) {
        expect($list->isArchived())->toBeFalse();
    }
});

// Testa buscar listas arquivadas
it('busca listas arquivadas', function () {
    $service = new TodoListService();
    $lists = $service->findArchived();

    expect($lists)->toBeArray();

    foreach ($lists as $list) {
        expect($list->isArchived())->toBeTrue();
    }
});

// Testa buscar listas ativas
it('busca listas ativas', function () {
    $service = new TodoListService();
    $lists = $service->findActive();

    expect($lists)->toBeArray();

    foreach ($lists as $list) {
        expect($list->isArchived())->toBeFalse();
    }
});

// Testa listas possuem ordem valida
it('listas possuem ordem valida', function () {
    $service = new TodoListService();
    $lists = $service->findAll();

    if (count($lists) === 0) {
        expect(true)->toBeTrue();
        return;
    }

    foreach ($lists as $list) {
        expect($list->getOrder())->toBeInt();
        expect($list->getOrder())->toBeGreaterThanOrEqual(0);
    }
});

// Testa retornar null ao buscar lista inexistente
it('retorna null ao buscar lista inexistente', function () {
    $service = new TodoListService();
    $found = $service->find('00000000-0000-0000-0000-000000000000');

    expect($found)->toBeNull();
});
